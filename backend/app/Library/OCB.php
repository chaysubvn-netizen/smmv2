<?php

namespace App\Library;

class OCB
{
    public $username;
    public $password;
    public $account_number;
    public $file;
    public $cookies_file;
    public $state;
    public $nonce;
    public $code_verifier;
    public $code_challenge;
    public $auth_token;
    public $refresh_token;
    public $id; // arrangementId
    public $user_agent;
    public $user_context = null; // USER_CONTEXT cookie - long-lived, set after login

    public function __construct()
    {
     
    }

    private function init_state($username, $password = '', $force_new = false)
    {
        $this->username = $username;
        if (!empty($password)) {
            $this->password = $password;
        }

        // Use username as identifier since account_number might not be provided during login
        $identifier = md5($username); 
        $this->file = storage_path("app/ocb/users/{$identifier}.json");
        $this->cookies_file = storage_path("app/ocb/cookies/{$identifier}.txt");

        $dir = dirname($this->file);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $dir_cookies = dirname($this->cookies_file);
        if (!is_dir($dir_cookies)) {
            mkdir($dir_cookies, 0777, true);
        }

        if ($force_new || !file_exists($this->file)) {
            // Không xóa cookies_file để giữ lại cookie thiết bị tin cậy (tránh bị bắt nhập lại OTP)
            $this->state = $this->get_imei();
            $this->nonce = $this->state;
            $this->code_verifier = $this->generateRandomString(96);
            $this->code_challenge = $this->get_code_challenge($this->code_verifier);
            $this->user_agent = $this->get_user_agent();
            $this->save_data();
        } else {
            $this->parse_data();
        }
    }

    public function load_state($username) {
        $this->username = $username;
        $identifier = md5($username); 
        $this->file = storage_path("app/ocb/users/{$identifier}.json");
        $this->cookies_file = storage_path("app/ocb/cookies/{$identifier}.txt");
        $this->parse_data();
    }

    private function generateRandomString($length = 96) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function get_code_challenge($string) {
        $hash = hash('sha256', $string, true);
        $base64 = base64_encode($hash);
        return str_replace(['+', '/', '='], ['-', '_', ''], $base64);
    }

    private function get_microtime() {
        return (int)round(microtime(true) * 1000);
    }

    private function get_imei() {
        $time = md5((string)$this->get_microtime());
        return strtoupper(substr($time, 0, 8) . '-' . substr($time, 8, 4) . '-' . substr($time, 12, 4) . '-' . substr($time, 16, 4) . '-' . substr($time, 20));
    }

    private function get_user_agent() {
        $user_agents = [
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36",
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36",
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:115.0) Gecko/20100101 Firefox/115.0"
        ];
        return $user_agents[array_rand($user_agents)];
    }

    private function save_data() {
        $data = [
            'username' => $this->username,
            'password' => $this->password,
            'state' => $this->state,
            'nonce' => $this->nonce,
            'code_verifier' => $this->code_verifier,
            'code_challenge' => $this->code_challenge,
            'auth_token' => $this->auth_token,
            'refresh_token' => $this->refresh_token,
            'id' => $this->id,
            'user_agent' => $this->user_agent,
            'user_context' => $this->user_context
        ];
        file_put_contents($this->file, json_encode($data));
    }

    private function parse_data() {
        if (file_exists($this->file)) {
            $data = json_decode(file_get_contents($this->file), true);
            if ($data) {
                $this->username = $data['username'] ?? $this->username;
                $this->password = $data['password'] ?? $this->password;
                $this->state = $data['state'] ?? $this->state;
                $this->nonce = $data['nonce'] ?? $this->nonce;
                $this->code_verifier = $data['code_verifier'] ?? $this->code_verifier;
                $this->code_challenge = $data['code_challenge'] ?? $this->code_challenge;
                $this->auth_token = $data['auth_token'] ?? null;
                $this->refresh_token = $data['refresh_token'] ?? null;
                $this->id = $data['id'] ?? null;
                $this->user_agent = $data['user_agent'] ?? $this->get_user_agent();
                $this->user_context = $data['user_context'] ?? null;
            }
        }
    }

    private function curl($url, $method = 'GET', $headers = [], $data = null, $allow_redirects = false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data) ? http_build_query($data) : $data);
            }
        }

        $formatted_headers = [];
        foreach ($headers as $key => $value) {
            if (is_numeric($key)) {
                $formatted_headers[] = $value; // Support numeric index arrays
            } else {
                $formatted_headers[] = "$key: $value";
            }
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $formatted_headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $allow_redirects);
        
        if ($this->cookies_file) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookies_file);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookies_file);
        }
        
        if (!empty($this->user_context)) {
            curl_setopt($ch, CURLOPT_COOKIE, "USER_CONTEXT=" . $this->user_context);
        }

        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        if (!$response) return null;

        $response_headers = substr($response, 0, $header_size);
        $response_body = substr($response, $header_size);

        if (preg_match('/Set-Cookie:\s*USER_CONTEXT=([^;\r\n]+)/i', $response_headers, $matches)) {
            $this->user_context = trim($matches[1]);
            if (!empty($this->file)) {
                $this->save_data();
            }
        }

        $location = null;
        if (preg_match('/^Location:\s*(.*)$/mi', $response_headers, $matches)) {
            $location = trim($matches[1]);
        }

        $request_header_sent = curl_getinfo($ch, CURLINFO_HEADER_OUT);

        return [
            'status_code' => $status_code,
            'headers' => $response_headers,
            'body' => $response_body,
            'url' => $effective_url,
            'location' => $location,
            'request_headers' => $request_header_sent
        ];
    }

    private function get_session_and_code($url) {
        $query = parse_url($url, PHP_URL_QUERY);
        if ($query) {
            parse_str($query, $query_params);
            if (isset($query_params['session_state']) && isset($query_params['code'])) {
                return [$query_params['session_state'], $query_params['code']];
            }
        }
        return [null, null];
    }

    private function get_login_url() {
        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
            'Connection: keep-alive',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: ' . $this->user_agent
        ];

        $params = [
            "client_id" => "bb-web-client",
            "redirect_uri" => "https://ocbomni.ocb.com.vn/en-US/select-context",
            "state" => $this->state,
            "response_type" => "code",
            "scope" => "openid",
            "nonce" => $this->nonce,
            "ui_locales" => "vi",
            "code_challenge" => $this->code_challenge,
            "code_challenge_method" => "S256"
        ];

        $url = "https://identity-omni.ocb.com.vn/auth/realms/backbase/protocol/openid-connect/auth?" . http_build_query($params);

        $res = $this->curl($url, 'GET', $headers);
        if (!$res) return null;

        $session_and_code = $this->get_session_and_code($res['url']);
        if ($session_and_code[0] && $session_and_code[1]) {
            return $this->get_login_url(); // Already logged in, fetching again
        }

        if (preg_match('/action="(.*?)" method/', $res['body'], $matches)) {
            return str_replace(["amp;", "&&"], ["&", "&"], $matches[1]);
        }
        return null;
    }

    private function internal_login() {
        $login_url = $this->get_login_url();
        if (!$login_url) return ['status' => 'error', 'msg' => 'Lỗi kết nối OCB Auth'];

        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9',
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: ' . $this->user_agent
        ];
        
        $data = [
            'username' => $this->username,
            'password' => $this->password,
            'locale' => 'vi',
            'rememberMe' => 'on'
        ];
        
        $res = $this->curl($login_url, 'POST', $headers, $data);
        if (!$res) return ['status' => 'error', 'msg' => 'Lỗi kết nối OCB Login'];

        $result = $res['body'];
        $url_to_check = !empty($res['location']) ? $res['location'] : $res['url'];
        $session_and_code = $this->get_session_and_code($url_to_check);
        
        if ($session_and_code[0] && $session_and_code[1]) {
            $token = $this->fetch_token($session_and_code[1]);
            if ($token) {
                return [
                    'status' => 'success',
                    'accessToken' => $token,
                    'msg' => 'Đăng nhập thành công'
                ];
            }
        }
        
        // Handle SmartOTP / Push / SMS
        if (strpos($result, 'Xác thực đăng nhập') !== false) {
            $action_url = null;
            if (preg_match('/action="(.*?)"/', $result, $matches)) {
                $action_url = str_replace(["amp;", "&&"], ["&", "&"], $matches[1]);
            }
            if ($action_url) {
                list($request_result, $request_url) = $this->send_request_login($action_url);
                
                if (strpos($request_result, 'Chúng tôi đã gửi yêu cầu xác thực tới thiết bị đăng ký của bạn') !== false) {
                    return [
                        'status' => 'waiting',
                        'msg' => 'Chúng tôi đã gửi yêu cầu xác thực tới ứng dụng OCB OMNI. Vui lòng mở app và bấm xác nhận trong 2 phút.',
                        'url' => $request_url
                    ];
                } else {
                    return [
                        'status' => 'otp',
                        'msg' => 'Hệ thống đã gửi mã OTP qua SMS. Vui lòng kiểm tra tin nhắn và nhập mã.',
                        'action' => $request_url,
                        'raw' => $request_result
                    ];
                }
            }
            return ['status' => 'error', 'msg' => 'Yêu cầu xác thực (Không thể lấy action).'];
        }

        if (strpos($result, 'OMNI_03_MS') !== false) {
            return ['status' => 'error', 'msg' => 'Tài khoản hoặc thiết bị có vấn đề (Mã lỗi OCB).'];
        }

        return ['status' => 'error', 'msg' => 'Đăng nhập thất bại', 'raw' => $result];
    }

    public function send_request_login($request_url) {
        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9',
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: ' . $this->user_agent
        ];
        $data = [
            'otpChoice' => 'SMS',
        ];
        
        $res = $this->curl($request_url, 'POST', $headers, $data);
        if (!$res) return [null, null];

        $result = $res['body'];
        if (preg_match('/action="(.*?)"/', $result, $matches)) {
            $url = str_replace(["amp;", "&&"], ["&", "&"], $matches[1]);
            return [$result, $url];
        }
        return [$result, null];
    }

    public function check_session($url) {
        $headers = [
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: ' . $this->user_agent
        ];
        $data = [
            'oob-authn-action' => 'confirmation-poll'
        ];
        $res = $this->curl($url, 'POST', $headers, $data);
        return $res ? $res['body'] : null;
    }

    public function continue_check_session($url) {
        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9',
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: ' . $this->user_agent
        ];
        $data = [
            'oob-authn-action' => 'confirmation-continue'
        ];
        $res = $this->curl($url, 'POST', $headers, $data, false);
        if ($res && $res['status_code'] == 302 && $res['location']) {
            return $res['location'];
        }
        return null;
    }

    public function fetch_token_from_code($code) {
        return $this->fetch_token($code);
    }

    private function fetch_token($code) {
        $headers = [
            'Accept: application/json',
            'User-Agent: ' . $this->user_agent
        ];

        $data = [
            'code' => $code,
            'grant_type' => 'authorization_code',
            'client_id' => 'bb-web-client',
            'redirect_uri' => 'https://ocbomni.ocb.com.vn/en-US/select-context',
            'code_verifier' => $this->code_verifier,
            'ui_locales' => 'vi'
        ];

        $endpoint = 'https://identity-omni.ocb.com.vn/auth/realms/backbase/protocol/openid-connect/token';
        $response = $this->curl($endpoint, 'POST', $headers, $data);
        
        if ($response) {
            $result = json_decode($response['body'], true);
            if (isset($result['access_token'])) {
                $this->auth_token = $result['access_token'];
                $this->refresh_token = $result['refresh_token'] ?? $this->refresh_token;
                // Visit select-context page so OCB sets USER_CONTEXT cookie in cookiejar
                $this->visit_select_context($result['access_token']);
                $this->save_data();
                return $this->auth_token;
            }
        }
        return null;
    }

    /**
     * Visit the select-context page after login so OCB sets USER_CONTEXT cookie.
     * This is the key step that enables balance/transaction APIs to work.
     */
    private function visit_select_context($token) {
        // Step 1: GET the select-context page (browser redirect after login)
        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: vi',
            'Authorization: Bearer ' . $token,
            'Connection: keep-alive',
            'User-Agent: ' . $this->user_agent
        ];
        $this->curl('https://ocbomni.ocb.com.vn/en-US/select-context', 'GET', $headers, null, true);

        // Step 2: Call context-select API (what the JS app does after page load)
        $api_headers = [
            'Accept: application/json',
            'Accept-Language: vi',
            'Authorization: Bearer ' . $token,
            'Connection: keep-alive',
            'Content-Type: application/json',
            'Lang: vi',
            'Origin: https://ocbomni.ocb.com.vn',
            'Referer: https://ocbomni.ocb.com.vn/en-US/select-context',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Site: same-origin',
            'User-Agent: ' . $this->user_agent
        ];
        // Get list of service agreements
        $sa_res = $this->curl(
            'https://ocbomni.ocb.com.vn/api/context-manager/client-api/v2/service-agreements',
            'GET', $api_headers
        );
        if ($sa_res && $sa_res['status_code'] == 200) {
            $sa_data = json_decode($sa_res['body'], true);
            $sa_id = $sa_data[0]['id'] ?? null;
            if ($sa_id) {
                // Select the service agreement context (this triggers USER_CONTEXT cookie to be set)
                $this->curl(
                    'https://ocbomni.ocb.com.vn/api/context-manager/client-api/v2/service-agreements/' . $sa_id . '/context',
                    'POST', $api_headers, json_encode([])
                );
            }
        }
    }

    // ==============================================================================
    // PUBLIC METHODS (ACB-like Interface)
    // ==============================================================================

    /**
     * login_ocb (similar to login_acb)
     */
    public function login_ocb($username, $password)
    {
        $this->init_state($username, $password);
        
        // Refresh token if available
        if ($this->refresh_token) {
            $headers = [
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'User-Agent: ' . $this->user_agent
            ];
            $data = [
                'grant_type' => 'refresh_token',
                'client_id' => 'bb-web-client',
                'refresh_token' => $this->refresh_token
            ];
            $res = $this->curl('https://identity-omni.ocb.com.vn/auth/realms/backbase/protocol/openid-connect/token', 'POST', $headers, $data);
            if ($res && $json = json_decode($res['body'], true)) {
                if (isset($json['access_token'])) {
                    $this->auth_token = $json['access_token'];
                    $this->refresh_token = $json['refresh_token'] ?? $this->refresh_token;
                    $this->visit_select_context($this->auth_token);
                    $this->save_data();
                    return json_encode([
                        'status' => 'success',
                        'accessToken' => $this->auth_token,
                        'msg' => 'Làm mới token thành công'
                    ]);
                }
            }
        }

        // Full login flow - if we reach here, we must generate a FRESH session state
        $this->init_state($username, $password, true); 
        $loginResult = $this->internal_login();
        if ($loginResult['status'] === 'success') {
            return json_encode([
                'status' => 'success',
                'accessToken' => $loginResult['accessToken'],
                'msg' => 'Đăng nhập thành công'
            ]);
        } elseif ($loginResult['status'] === 'waiting') {
            return json_encode([
                'status' => 'waiting',
                'msg' => $loginResult['msg'],
                'url' => $loginResult['url'] ?? ''
            ]);
        } elseif ($loginResult['status'] === 'otp') {
            return json_encode([
                'status' => 'otp',
                'msg' => $loginResult['msg'],
                'action' => $loginResult['action'] ?? ''
            ]);
        }

        return json_encode([
            'status' => 'error',
            'msg' => $loginResult['msg'] ?? 'Đăng nhập thất bại',
            'raw' => $loginResult['raw'] ?? null
        ]);
    }

    public function submit_otp($otp_action, $otp_code) {
        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9',
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: ' . $this->user_agent
        ];
        
        $data = [
            'otp' => $otp_code,
            'login' => 'Xác nhận'
        ];
        
        $res = $this->curl($otp_action, 'POST', $headers, $data);
        if (!$res) return ['status' => 'error', 'msg' => 'Lỗi kết nối submit OTP'];

        $url_to_check = !empty($res['location']) ? $res['location'] : $res['url'];
        $session_and_code = $this->get_session_and_code($url_to_check);
        if ($session_and_code[0] && $session_and_code[1]) {
            $token = $this->fetch_token($session_and_code[1]);
            if ($token) {
                return [
                    'status' => 'success',
                    'accessToken' => $token,
                    'msg' => 'Đăng nhập thành công'
                ];
            }
        }

        if (strpos($res['body'], 'otp') !== false || strpos($res['body'], 'Xác thực') !== false) {
             return ['status' => 'error', 'msg' => 'Mã OTP không chính xác. Vui lòng thử lại.'];
        }

        return ['status' => 'error', 'msg' => 'OTP không chính xác hoặc lỗi đăng nhập (Vui lòng kiểm tra lại)'];
    }


    /**
     * Save USER_CONTEXT cookie from browser for use in API calls.
     * This is a long-lived cookie that enables balance/transaction APIs.
     */
    public function set_user_context($user_context) {
        $this->user_context = $user_context;
        $this->save_data();
    }

    /**
     * Build common API headers, injecting USER_CONTEXT cookie if available.
     */
    private function build_api_headers($token) {
        $headers = [
            'Accept: application/json',
            'Accept-Language: vi',
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Lang: vi',
            'Origin: https://ocbomni.ocb.com.vn',
            'Referer: https://ocbomni.ocb.com.vn/en-US/select-context',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Site: same-origin',
            'User-Agent: ' . $this->user_agent
        ];
        return $headers;
    }

    /**
     * get_balance - uses USER_CONTEXT from state to authenticate with OCB balance API.
     */
    public function get_balance($token, $accountNo = null)
    {
        $headers = $this->build_api_headers($token);
        $url = 'https://ocbomni.ocb.com.vn/api/arrangement-manager/client-api/v2/arrangement-views/account-overview/groups/current-account-vnd?_limit=100';
        $response = $this->curl($url, 'GET', $headers);
        
        if ($response && $response['status_code'] == 200) {
            $data = json_decode($response['body'], true);
            if (isset($data['elements'])) {
                foreach ($data['elements'] as $element) {
                    $elementAccountNo = (string) ($element['attributes']['bban']['value'] ?? '');
                    if ($accountNo !== null && $elementAccountNo !== (string) $accountNo) {
                        continue;
                    }
                    return json_encode([
                        'status' => 'success',
                        'balance' => (int)$element['attributes']['availableBalance']['value'],
                        'accountNo' => $elementAccountNo,
                        'arrangementId' => $element['id']
                    ]);
                }
            }
        }
        return json_encode(['status' => 'error', 'msg' => 'Lỗi lấy số dư']);
    }

    public function get_account_name($token, $accountNo) {
        $headers = [
            'Accept: application/json',
            'Accept-Language: vi',
            'Authorization: Bearer ' . $token,
            'Connection: keep-alive',
            'Content-Type: application/json',
            'Lang: vi',
            'Origin: https://ocbomni.ocb.com.vn',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Site: same-origin',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36',
            'sec-ch-ua: "Chromium";v="130", "Microsoft Edge";v="130", "Not?A_Brand";v="99"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Windows"'
        ];
        
        $url = 'https://ocbomni.ocb.com.vn/api/account-integration-service/client-api/v1/accounts/inquiry-accounts';
        $data = json_encode([
            'accountOrPhone' => $accountNo,
            'transferType' => 'INTERNAL_TRANSFER'
        ]);
        
        $response = $this->curl($url, 'POST', $headers, $data);
        if ($response && $response['status_code'] == 200) {
            $result = json_decode($response['body'], true);
            if (isset($result['accountHolderName'])) {
                return $result['accountHolderName'];
            }
        }
        return null;
    }

   
    public function LSGD($accountNo, $rows, $token)
    {
        if (empty($token)) {
            return json_encode(['status' => 'error', 'msg' => 'Missing OCB token', 'code' => 401]);
        }

        // First, get arrangement ID which is required for transactions in OCB
        $balanceInfo = json_decode($this->get_balance($token, $accountNo), true);
        if (!isset($balanceInfo['arrangementId'])) {
            return json_encode(['status' => 'error', 'msg' => 'Không thể lấy Arrangement ID']);
        }
        $arrangementId = $balanceInfo['arrangementId'];

        $headers = $this->build_api_headers($token);

        $from_date = date('Y-m-d', strtotime('-30 days'));
        $to_date = date('Y-m-d', strtotime('+1 days'));
        $page = 0;

        $url = "https://ocbomni.ocb.com.vn/api/transaction-manager/client-api/v2/transactions?bookingDateGreaterThan={$from_date}&bookingDateLessThan={$to_date}&arrangementId={$arrangementId}&from={$page}&size={$rows}&orderBy=bookingDate&direction=DESC";
        
        $response = $this->curl($url, 'GET', $headers);
        if ($response && $response['status_code'] == 200) {
            return $response['body'];
        }
        
        return json_encode(['status' => 'error', 'msg' => 'Không thể lấy lịch sử giao dịch', 'code' => 401]);
    }
}
