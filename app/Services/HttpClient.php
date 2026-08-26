<?php
/** Minimal JSON POST client: cURL when available, PHP streams otherwise. */
class HttpClient
{
    public static function postJson($url, array $headers, array $payload, $timeout = 20)
    {
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($body === false) throw new RuntimeException('Unable to encode HTTP payload.');
        $headers = array_merge(['Content-Type: application/json', 'Accept: application/json'], $headers);
        if (function_exists('curl_init')) return self::curl($url, $headers, $body, $timeout);
        if (!filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN)) throw new RuntimeException('No HTTP transport available. Enable cURL or allow_url_fopen.');
        return self::stream($url, $headers, $body, $timeout);
    }

    private static function curl($url, array $headers, $body, $timeout)
    {
        $h = curl_init($url);
        $options = [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $body, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => (int) $timeout, CURLOPT_CONNECTTIMEOUT => 5, CURLOPT_HTTPHEADER => $headers];
        $ca = env('HTTP_CA_BUNDLE');
        if (!$ca) foreach (['C:/xampp/php/extras/ssl/cacert.pem', 'C:/xampp/phpMyAdmin/vendor/composer/ca-bundle/res/cacert.pem'] as $candidate) if (is_readable($candidate)) { $ca = $candidate; break; }
        if ($ca && is_readable($ca)) $options[CURLOPT_CAINFO] = $ca;
        curl_setopt_array($h, $options);
        $response = curl_exec($h); $status = (int) curl_getinfo($h, CURLINFO_RESPONSE_CODE); $error = curl_error($h); curl_close($h);
        if ($response === false || $status < 200 || $status >= 300) throw new RuntimeException('HTTP request failed' . ($status ? " ({$status})" : '') . ($error ? ': ' . $error : '.'));
        return self::decode($response);
    }

    private static function stream($url, array $headers, $body, $timeout)
    {
        $ssl = ['verify_peer' => true, 'verify_peer_name' => true];
        $ca = env('HTTP_CA_BUNDLE'); if ($ca && is_readable($ca)) $ssl['cafile'] = $ca;
        $context = stream_context_create(['http' => ['method' => 'POST', 'header' => implode("\r\n", $headers), 'content' => $body, 'timeout' => (int) $timeout, 'ignore_errors' => true], 'ssl' => $ssl]);
        $response = @file_get_contents($url, false, $context); $status = 0;
        foreach (($http_response_header ?? []) as $line) if (preg_match('#^HTTP/\S+\s+(\d+)#', $line, $m)) $status = (int) $m[1];
        if ($response === false || $status < 200 || $status >= 300) throw new RuntimeException('HTTP request failed' . ($status ? " ({$status})" : '.'));
        return self::decode($response);
    }

    private static function decode($response)
    {
        $data = json_decode((string) $response, true);
        if (!is_array($data)) throw new RuntimeException('HTTP response is not valid JSON.');
        return $data;
    }
}
