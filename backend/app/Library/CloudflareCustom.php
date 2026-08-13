<?php

namespace App\Library;

use App\Models\Config;

class CloudflareCustom
{
    public $email = "";
    public $global_key = "";
    public $account_id = "";
    public $token = "";
    public $ip_host = "";
    public $frontend_ip_host = "";
    public $backend_ip_host = "";

    public function __construct()
    {
        $config = Config::first();

        if ($config) {
            $this->email = $config->cloudflare_email;
            $this->global_key = $config->cloudflare_global_key;
            $this->account_id = $config->cloudflare_account_id;
            $this->token = $config->cloudflare_token;
            $this->ip_host = $config->cloudflare_ip_host;
            $this->frontend_ip_host = config('services.child_panel.nextjs_server_ip') ?: $this->ip_host;
            $this->backend_ip_host = config('services.child_panel.backend_server_ip') ?: $this->ip_host;
        }
    }

    public function profile()
    {
        $url = "https://api.cloudflare.com/client/v4/user";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            "X-Auth-Email: " . $this->email,
            "X-Auth-Key: " . $this->global_key,
            "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,  2);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        $resp = curl_exec($curl);
        curl_close($curl);
        return json_decode($resp, true);
    }

    public function addDomain($domainName)
    {
        $url = 'https://api.cloudflare.com/client/v4/zones';
        $data = [
            "account" => [
                "id" => $this->account_id
            ],
            "name" => $domainName,
            "type" => "full"
        ];

        $result = $this->send($url, "POST", $data);

        if (isset($result) && isset($result['success']) && $result['success'] == true) {
            $dns = $this->provisionChildPanelDns($result['result']['id'], $domainName);
            if (!($dns['success'] ?? false)) {
                return [
                    'status' => 'error',
                    'message' => $dns['errors'][0]['message'] ?? 'Không thể tạo DNS cho Child Panel.',
                    'data' => $dns,
                ];
            }
            $dd = $this->recordDomain($result['result']['id'], $domainName);
            $dns_id = $dd['result'][0]['id'] ?? null;

            return [
                'status' => 'success',
                'message' => 'Thêm tên miền thành công!',
                'data' => [
                    'zone_id' => $result['result']['id'],
                    'zone_name' => $result['result']['name'],
                    'zone_status' => $result['result']['status'],
                    'dns_id' => $dns_id,
                    'nameserver1' => $result['result']['name_servers'][0] ?? null,
                    'nameserver2' => $result['result']['name_servers'][1] ?? null,
                ]
            ];
        } else {
            return [
                'status' => 'error',
                'message' => $result['errors'][0]['message'] ?? "Lỗi không xác định vui lòng thử lại sau!",
                "code" => $result['errors'][0]['code'] ?? null,
                "data" => $result
            ];
        }
    }

    public function deleteDomain($zone_id)
    {
        $url = 'https://api.cloudflare.com/client/v4/zones/' . $zone_id;
        $result = $this->send($url, "DELETE");

        if (isset($result) && isset($result['success']) && $result['success'] == true) {
            return [
                'status' => 'success',
                'message' => 'Xóa tên miền thành công!',
                'data' => [
                    'zone_id' => $result['result']['id'],
                ]
            ];
        } else {
            return [
                'status' => 'error',
                'message' => $result['errors'][0]['message'] ?? "Lỗi không xác định vui lòng thử lại sau!"
            ];
        }
    }

    public function findDomain($domain_name)
    {
        $url = "https://api.cloudflare.com/client/v4/zones?name=" . urlencode($domain_name);
        $data = $this->send($url, "GET");

        $v = [];
        if (isset($data['result']) && !empty($data['result'])) {
            $v = $data['result'][0];
            $v['zone_id'] = $v['id'];
        }
        return $v;
    }

    public function infoDomain($zone_id)
    {
        $url = "https://api.cloudflare.com/client/v4/zones/$zone_id";
        return $this->send($url, "GET");
    }

    public function recordDomain($zone_id, $domain)
    {
        $url = "https://api.cloudflare.com/client/v4/zones/$zone_id/dns_records?search=$domain";
        return $this->send($url, "GET");
    }

    public function dnsRecord($zone_id, $dns_record)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.cloudflare.com/client/v4/zones/$zone_id/dns_records/$dns_record");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $headers = array();
        $headers[] = 'X-Auth-Email: ' . $this->email;
        $headers[] = 'X-Auth-Key: ' . $this->global_key;
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }

    public function createDns($zone_id)
    {
        return $this->upsertARecord($zone_id, '@', $this->frontend_ip_host);
    }

    public function provisionChildPanelDns($zone_id, $domain)
    {
        if (empty($this->frontend_ip_host) || empty($this->backend_ip_host)) {
            return [
                'success' => false,
                'errors' => [['message' => 'Thiếu NEXTJS_SERVER_IP hoặc BACKEND_SERVER_IP.']],
            ];
        }

        $frontend = $this->upsertARecord($zone_id, $domain, $this->frontend_ip_host);
        if (!($frontend['success'] ?? false)) {
            return $frontend;
        }

        return $this->upsertARecord($zone_id, 'api.'.$domain, $this->backend_ip_host);
    }

    private function upsertARecord($zone_id, $name, $ip)
    {
        $lookupUrl = "https://api.cloudflare.com/client/v4/zones/$zone_id/dns_records?type=A&name=".urlencode($name);
        $existing = $this->send($lookupUrl, 'GET');
        $recordId = $existing['result'][0]['id'] ?? null;
        $url = "https://api.cloudflare.com/client/v4/zones/$zone_id/dns_records".($recordId ? "/$recordId" : '');
        $data = [
            'type' => 'A',
            'name' => $name,
            'content' => $ip,
            'ttl' => 1,
            'proxied' => true,
        ];

        return $this->send($url, $recordId ? 'PUT' : 'POST', $data);
    }

    public function updateDns($zone_id, $dns_record)
    {
        $url = 'https://api.cloudflare.com/client/v4/zones/' . $zone_id . '/dns_records/' . $dns_record;
        $data = [
            "type" => "A",
            "name" => '@',
            "content" => $this->ip_host,
            "ttl" => 1,
            "priority" => 10,
            "proxied" => true
        ];
        return $this->send($url, "PUT", $data);
    }

    public function deleteDns($zone_id, $dns_record)
    {
        $url = "https://api.cloudflare.com/client/v4/zones/$zone_id/dns_records/$dns_record";
        return $this->send($url, "DELETE");
    }

    public function updateSslTls($zone_id)
    {
        $url = 'https://api.cloudflare.com/client/v4/zones/' . $zone_id . '/settings/ssl';
        $data = [
            "value" => "full"
        ];
        return $this->send($url, "PATCH", $data);
    }

    public function send($url, $method = "GET", $data = [])
    {
        $curl = curl_init($url);

        $headers = [
            'Content-Type: application/json',
        ];

        if (!empty($this->token)) {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        } elseif (!empty($this->email) && !empty($this->global_key)) {
            $headers[] = 'X-Auth-Email: ' . $this->email;
            $headers[] = 'X-Auth-Key: ' . $this->global_key;
        }

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYHOST =>  2,
            CURLOPT_SSL_VERIFYPEER => true,
        );

        if ($method != "GET") {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return [
                'success' => false,
                'errors' => [['message' => "cURL Error: " . $err]]
            ];
        }

        $decoded = json_decode($response, true);

        return $decoded;
    }
}
