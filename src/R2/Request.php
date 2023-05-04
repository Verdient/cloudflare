<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\R2;

use Verdient\http\Request as HttpRequest;

/**
 * 请求
 * @author Verdient。
 */
class Request extends HttpRequest
{
    /**
     * @var string 秘钥标识
     * @author Verdient。
     */
    public $accessKey;

    /**
     * @var string 访问秘钥
     * @author Verdient。
     */
    public $accessSecret;

    /**
     * @inheritdoc
     * @return Response
     * @author Verdient。
     */
    public function send()
    {
        $date = gmdate('Ymd\THis\Z');
        $this->prepare();
        $contentHash = hash('sha256', $this->getContent());
        $this->addHeader('Host', parse_url($this->getUrl(), PHP_URL_HOST));
        $this->addHeader('X-Amz-Date', $date);
        $this->addHeader('X-Amz-User-Agent', 'Cloudflare R2 Agent');
        $this->addHeader('X-Amz-Content-Sha256', $contentHash);
        $this->addHeader('User-Agent', 'Cloudflare R2 Agents');
        $this->addHeader('Authorization', $this->sign($date, $this->getHeaders(), $contentHash));
        return new Response(parent::send());
    }

    /**
     * 获取用于签名的头部
     * @author Verdient。
     */
    protected function getHeadersToSign()
    {
        $ignoreHeaders = [
            'accept',
            'except',
            'user-agent',
            'cache-control',
            'content-type',
            'content-length',
            'expect',
            'max-forwards',
            'pragma',
            'range',
            'te',
            'if-match',
            'if-none-match',
            'if-modified-since',
            'if-unmodified-since',
            'if-range',
            'authorization',
            'proxy-authorization',
            'from',
            'referer',
            'user-agent',
            'X-Amz-User-Agent',
            'x-amzn-trace-id',
            'aws-sdk-invocation-id',
            'aws-sdk-retry',
        ];
        $headers = [];
        foreach ($this->getHeaders() as $key => $values) {
            $key = strtolower($key);
            if (!in_array($key, $ignoreHeaders)) {
                if (is_array($values)) {
                    $headers[$key] = $values;
                } else {
                    $headers[$key] = [$values];
                }
            }
        }
        ksort($headers);
        return $headers;
    }

    /**
     * 获取签名秘钥
     * @param string $shortDate 简短日期
     * @param string $region 地域
     * @param string $service 服务名称
     * @param string $secretKey 访问秘钥
     * @return string
     * @author Verdient。
     */
    protected function getSigningKey($shortDate, $region, $service, $secretKey)
    {
        $dateKey = hash_hmac(
            'sha256',
            $shortDate,
            "AWS4{$secretKey}",
            true
        );
        $regionKey = hash_hmac('sha256', $region, $dateKey, true);
        $serviceKey = hash_hmac('sha256', $service, $regionKey, true);
        return hash_hmac(
            'sha256',
            'aws4_request',
            $serviceKey,
            true
        );
    }

    /**
     * 获取用于签名的字符串
     * @param string $longDate 长日期
     * @param string $credentialScope 认证范围
     * @param string $payload 请求内容
     * @return string
     * @author Verdient。
     */
    protected function createStringToSign($longDate, $credentialScope, $payload)
    {
        $hash = hash('sha256', $payload);
        return "AWS4-HMAC-SHA256\n{$longDate}\n{$credentialScope}\n{$hash}";
    }

    /**
     * 获取规范的查询参数
     * @return string
     * @author Verdient。
     */
    protected function getCanonicalizedQuery()
    {
        $query = array_filter($this->getQuery());
        unset($query['X-Amz-Signature']);
        if (empty($query)) {
            return '';
        }
        $qs = '';
        ksort($query);
        foreach ($query as $k => $v) {
            if (!is_array($v)) {
                $qs .= rawurlencode($k) . '=' . rawurlencode($v !== null ? (string) $v : '') . '&';
            } else {
                sort($v);
                foreach ($v as $value) {
                    $qs .= rawurlencode($k) . '=' . rawurlencode($value !== null ? (string) $value : '') . '&';
                }
            }
        }
        return substr($qs, 0, -1);
    }

    /**
     * 签名
     * @param string $date 日期
     * @return string
     * @author Verdient。
     */
    protected function sign($date, $headers, $contentHash)
    {
        $headers = [];
        $signedHeaders = [];
        foreach ($this->getHeadersToSign() as $key => $values) {
            $signedHeaders[] = $key;
            if (count($values) > 0) {
                sort($values);
            }
            $headers[] = $key . ':' . preg_replace('/\s+/', ' ', implode(',', $values));
        }
        $shortDate = substr($date, 0, 8);
        $region = 'auto';
        $service = 's3';
        $cs = "$shortDate/$region/$service/aws4_request";
        $signingKey = $this->getSigningKey($shortDate, $region, $service, $this->accessSecret);
        $signedHeadersString = implode(';', $signedHeaders);
        $payload = $this->getMethod() . "\n"
            . parse_url($this->getUrl(), PHP_URL_PATH) . "\n"
            . $this->getCanonicalizedQuery() . "\n";
        $payload .= implode("\n", $headers) . "\n\n"
            . $signedHeadersString . "\n"
            . $contentHash;
        $toSign = $this->createStringToSign($date, $cs, $payload);
        $signature = hash_hmac('sha256', $toSign, $signingKey);
        $parts = [
            'Credential' => $this->accessKey . "/$shortDate/$region/$service/aws4_request",
            'SignedHeaders' => $signedHeadersString,
            'Signature' => $signature
        ];
        $result = implode(', ', array_map(function ($key, $value) {
            return "$key=$value";
        }, array_keys($parts), array_values($parts)));
        return "AWS4-HMAC-SHA256 $result";
    }
}
