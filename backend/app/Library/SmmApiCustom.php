<?php

namespace App\Library;

class SmmApiCustom
{
    /** API URL */
    public $api_url = '';

    /** Your API key */
    public $api_key = '';

    private int $lastHttpStatus = 0;

    private string $lastError = '';

    public function __construct($api_url, $api_key)
    {
        $this->api_url = $this->normalizeApiUrl((string) $api_url);
        $this->api_key = $api_key;
    }

    private function normalizeApiUrl(string $url): string
    {
        $parts = parse_url($url);
        $host = (string) ($parts['host'] ?? '');
        if ($host === '') {
            return $url;
        }

        $resolvedIp = gethostbyname($host);
        $labels = explode('.', $host);
        if (! in_array($resolvedIp, ['127.0.0.1', '::1'], true) || count($labels) <= 2) {
            return $url;
        }

        $rootHost = implode('.', array_slice($labels, -2));

        return preg_replace(
            '#^(https?://)'.preg_quote($host, '#').'#i',
            '$1'.$rootHost,
            $url,
        ) ?: $url;
    }

    /** Add order */
    public function order($data)
    {
        $post = array_merge(['key' => $this->api_key, 'action' => 'add'], $data);

        return json_decode($this->connect($post), true);
    }

    /** Get order status  */
    public function status($order_id)
    {
        return json_decode(
            $this->connect([
                'key' => $this->api_key,
                'action' => 'status',
                'order' => $order_id,
            ]),
            true
        );
    }

    /** Get orders status */
    public function multiStatus($order_ids)
    {
        return json_decode(
            $this->connect([
                'key' => $this->api_key,
                'action' => 'status',
                'orders' => $order_ids,
            ]),
            true
        );
    }

    /** Get services */
    public function services()
    {
        $raw = $this->connect([
            'key' => $this->api_key,
            'action' => 'services',
        ], 2);
        $decoded = json_decode(ltrim((string) $raw, "\xEF\xBB\xBF\x00..\x20"), true);
        if (is_array($decoded)) return $decoded;

        $message = $this->lastError !== ''
            ? 'Không thể kết nối API: '.$this->lastError
            : ($this->lastHttpStatus > 0
                ? "API dịch vụ trả về HTTP {$this->lastHttpStatus} hoặc dữ liệu không hợp lệ."
                : 'API dịch vụ không phản hồi dữ liệu JSON hợp lệ.');

        return ['error' => ['code' => $this->lastHttpStatus ?: 502, 'message' => $message]];
    }

    /** Refill order */
    public function refill($orderId)
    {
        return json_decode(
            $this->connect([
                'key' => $this->api_key,
                'action' => 'refill',
                'order' => $orderId,
            ]),
            true
        );
    }

    /** Refill orders */
    public function multiRefill(array $orderIds)
    {
        return json_decode(
            $this->connect([
                'key' => $this->api_key,
                'action' => 'refill',
                'orders' => implode(',', $orderIds),
            ]),
            true,
        );
    }

    /** Get refill status */
    public function refillStatus(int $refillId)
    {
        return json_decode(
            $this->connect([
                'key' => $this->api_key,
                'action' => 'refill_status',
                'refill' => $refillId,
            ]),
            true
        );
    }

    /** Get refill statuses */
    public function multiRefillStatus(array $refillIds)
    {
        return json_decode(
            $this->connect([
                'key' => $this->api_key,
                'action' => 'refill_status',
                'refills' => implode(',', $refillIds),
            ]),
            true,
        );
    }

    /** Cancel orders */
    public function cancel(array $orderIds)
    {
        return json_decode(
            $this->connect([
                'key' => $this->api_key,
                'action' => 'cancel',
                'orders' => implode(',', $orderIds),
            ]),
            true,
        );
    }

    /** Get balance */
    public function balance()
    {
        $raw = $this->connect([
            'key' => $this->api_key,
            'action' => 'balance',
        ], 2);
        $decoded = json_decode(ltrim((string) $raw, "\xEF\xBB\xBF\x00..\x20"), true);
        if (is_array($decoded)) {
            return $decoded;
        }

        $host = (string) parse_url($this->api_url, PHP_URL_HOST);
        $resolvedIp = $host !== '' ? gethostbyname($host) : '';
        $loopback = $resolvedIp === '127.0.0.1' || $resolvedIp === '::1';
        $message = $this->lastError !== ''
            ? 'Không thể kết nối API: '.$this->lastError
            : ($this->lastHttpStatus > 0
                ? "URL API trả về HTTP {$this->lastHttpStatus} thay vì JSON."
                : 'API không phản hồi dữ liệu JSON hợp lệ.');
        if ($loopback) {
            $message .= " Domain {$host} đang trỏ về địa chỉ nội bộ {$resolvedIp}.";
        }

        return ['error' => ['code' => $this->lastHttpStatus ?: 502, 'message' => $message]];
    }

    private function connect($post, int $maxRetries = 0)
    {
        $_post = [];
        if (is_array($post)) {
            foreach ($post as $name => $value) {
                $_post[] = $name.'='.urlencode($value);
            }
        }

        $attempt = 0;

        do {
            $retryAfter = 0;
            $ch = curl_init($this->api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HEADERFUNCTION, static function ($curl, string $header) use (&$retryAfter) {
                if (stripos($header, 'Retry-After:') === 0) {
                    $retryAfter = max(0, (int) trim(substr($header, 12)));
                }

                return strlen($header);
            });

            if (is_array($post)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&', $_post));
            }
            curl_setopt($ch, CURLOPT_USERAGENT, 'SMMV2/1.0');
            $result = curl_exec($ch);
            $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $this->lastHttpStatus = $statusCode;
            $this->lastError = curl_error($ch);
            if (curl_errno($ch) != 0 && empty($result)) {
                $result = false;
            }
            curl_close($ch);

            if ($statusCode !== 429 || $attempt >= $maxRetries) {
                return $result;
            }

            $attempt++;
            sleep(min(3, $retryAfter > 0 ? $retryAfter : $attempt));
        } while (true);
    }
}
