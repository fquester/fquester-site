<?php

namespace App\Support;

class NextcloudClient
{
    protected string $baseUrl;
    protected string $user;
    protected string $appPassword;
    protected string $basePath;
    protected bool $verifySsl;

    public function __construct(string $baseUrl, string $user, string $appPassword, string $basePath = '', bool $verifySsl = true)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->user = $user;
        $this->appPassword = $appPassword;
        $this->basePath = trim($basePath, '/');
        $this->verifySsl = $verifySsl;
    }

    public static function fromEnv(): ?self
    {
        $url = env('NEXTCLOUD_URL');
        $user = env('NEXTCLOUD_USER');
        $pass = env('NEXTCLOUD_APP_PASSWORD');

        if (!$url || !$user || !$pass) {
            return null;
        }

        return new self(
            $url,
            $user,
            $pass,
            (string) env('NEXTCLOUD_BASE_PATH', ''),
            (bool) env('NEXTCLOUD_VERIFY_SSL', true)
        );
    }

    protected function davUrl(string $path): string
    {
        $full = trim($this->basePath . '/' . ltrim($path, '/'), '/');
        $encoded = implode('/', array_map('rawurlencode', explode('/', $full)));

        return $this->baseUrl . '/remote.php/dav/files/' . rawurlencode($this->user) . '/' . $encoded;
    }

    public function stat(string $path): ?array
    {
        $ch = curl_init($this->davUrl($path));

        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => 'PROPFIND',
            CURLOPT_HTTPHEADER     => ['Depth: 0'],
            CURLOPT_USERPWD        => $this->user . ':' . $this->appPassword,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => $this->verifySsl,
            CURLOPT_TIMEOUT        => 8,
        ]);

        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($body === false || $status < 200 || $status >= 300) {
            error_log("Nextcloud PROPFIND failed for '{$path}': HTTP {$status} {$error}");
            return null;
        }

        $xml = @simplexml_load_string($body);

        if ($xml === false) {
            return null;
        }

        $xml->registerXPathNamespace('d', 'DAV:');
        $size = $xml->xpath('//d:getcontentlength');
        $modified = $xml->xpath('//d:getlastmodified');

        return [
            'size'          => isset($size[0]) ? (int) (string) $size[0] : null,
            'last_modified' => isset($modified[0]) ? strtotime((string) $modified[0]) : null,
        ];
    }

    public function stream(string $path): bool
    {
        $out = fopen('php://output', 'wb');

        $ch = curl_init($this->davUrl($path));

        curl_setopt_array($ch, [
            CURLOPT_HTTPGET        => true,
            CURLOPT_USERPWD        => $this->user . ':' . $this->appPassword,
            CURLOPT_FILE           => $out,
            CURLOPT_SSL_VERIFYPEER => $this->verifySsl,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FAILONERROR    => true,
        ]);

        $ok = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        fclose($out);

        if (!$ok || $status < 200 || $status >= 300) {
            error_log("Nextcloud GET failed for '{$path}': HTTP {$status} {$error}");
            return false;
        }

        return true;
    }
}
