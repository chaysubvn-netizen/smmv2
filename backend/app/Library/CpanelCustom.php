<?php

namespace App\Library;

use App\Models\Config;

class CpanelCustom
{
    private $panel_type = 'cpanel';
    // cPanel properties
    private $cpanel_server = "";
    private $username_cpanel = "";
    private $password_cpanel = "";

    // aaPanel properties
    private $panel_url = "";
    private $api_key = "";
    private $site_id = "";

    public function __construct()
    {
        $config = Config::first();

        if ($config) {
            $this->panel_type = ltrim(strtolower($config->hosting_panel_type ?? 'cpanel'));
            if ($this->panel_type === 'aapanel') {
                $this->panel_url = rtrim($config->cpanel_host ?? '', '/');
                $this->api_key   = $config->cpanel_username ?? '';
                $this->site_id   = $config->cpanel_password ?? '';
            } else {
                $this->cpanel_server = rtrim($config->cpanel_host ?? '', '/');
                $this->username_cpanel = $config->cpanel_username ?? '';
                $this->password_cpanel = $config->cpanel_password ?? '';
            }
        }
    }

    private function buildAuthParams(): array
    {
        $now   = time();
        $token = md5($now . md5($this->api_key));

        return [
            'request_time'  => $now,
            'request_token' => $token,
        ];
    }

    private function aapanelPost(string $endpoint, array $data = []): array
    {
        $url      = $this->panel_url . $endpoint;
        $postData = array_merge($this->buildAuthParams(), $data);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($postData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST =>  2,
        ]);

        $response = curl_exec($ch);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['status' => false, 'msg' => 'cURL Error: ' . $err];
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : ['status' => false, 'msg' => 'Invalid JSON response'];
    }

    public function createDomain($domain)
    {
        $domain = preg_replace('/^www\./', '', strtolower(trim($domain)));
        
        if ($this->panel_type === 'aapanel') {
            if (empty($this->panel_url) || empty($this->api_key) || empty($this->site_id)) {
                return ['status' => false, 'msg' => 'aaPanel chưa được cấu hình đầy đủ (URL, API Key, Site ID).'];
            }

            $config = Config::first();
            $webname  = $config ? $config->domain : '';
            $webname = preg_replace('/^www\./', '', strtolower(trim($webname)));

            $result = $this->aapanelPost('/v2/site?action=AddDomain', [
                'id'      => $this->site_id,
                'webname' => $webname,
                'domain'  => $domain,
            ]);

            // Transform to cpanel result structure for backward compatibility
            return [
                'cpanelresult' => [
                    'data' => [
                        [
                            'result' => ($result['status'] ?? false) ? 1 : 0,
                            'reason' => $result['msg'] ?? ''
                        ]
                    ]
                ]
            ];
        }

        // cPanel logic
        $url = $this->cpanel_server . "/json-api/cpanel?cpanel_jsonapi_apiversion=2&cpanel_jsonapi_module=Park&cpanel_jsonapi_func=park&domain=$domain";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,  2);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $header[0] = "Authorization: Basic " . base64_encode($this->username_cpanel . ":" . $this->password_cpanel) . "\n\r";
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, $url);
        $return = curl_exec($curl);
        curl_close($curl);
        return json_decode($return, true);
    }

    public function deleteDomain($domain)
    {
        $domain = preg_replace('/^www\./', '', strtolower(trim($domain)));

        if ($this->panel_type === 'aapanel') {
            if (empty($this->panel_url) || empty($this->api_key) || empty($this->site_id)) {
                return ['status' => false, 'msg' => 'aaPanel chưa được cấu hình đầy đủ.'];
            }

            $config = Config::first();
            $webname  = $config ? $config->domain : '';
            $webname = preg_replace('/^www\./', '', strtolower(trim($webname)));

            $result = $this->aapanelPost('/v2/site?action=DelDomain', [
                'id'      => $this->site_id,
                'webname' => $webname,
                'domain'  => $domain,
                'port'    => 80,
            ]);

            return [
                'cpanelresult' => [
                    'data' => [
                        [
                            'result' => ($result['status'] ?? false) ? 1 : 0,
                            'reason' => $result['msg'] ?? ''
                        ]
                    ]
                ]
            ];
        }

        // cPanel logic
        $url = $this->cpanel_server . "/json-api/cpanel?cpanel_jsonapi_apiversion=2&cpanel_jsonapi_module=Park&cpanel_jsonapi_func=unpark&domain=$domain";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,  2);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $header[0] = "Authorization: Basic " . base64_encode($this->username_cpanel . ":" . $this->password_cpanel) . "\n\r";
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, $url);
        $return = curl_exec($curl);
        curl_close($curl);
        return json_decode($return, true);
    }
}
