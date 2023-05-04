<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\R2;

/**
 * 抽象客户端
 * @author Verdient。
 */
abstract class AbstractClient
{
    /**
     * @var string 账户编号
     * @author Verdient。
     */
    protected $accountId;

    /**
     * @var string 秘钥标识
     * @author Verdient。
     */
    protected $accessKey;

    /**
     * @var string 授权秘钥
     * @author Verdient。
     */
    protected $accessSecret;

    /**
     * @var string 存储桶
     * @author Verdient。
     */
    protected $bucket;

    /**
     * @param string $accountId 账户编号
     * @param string $accessKey 秘钥标识
     * @param string $accessSecret 授权秘钥
     * @param string $bucket 存储桶
     */
    public function __construct($accountId, $accessKey, $accessSecret, $bucket = null)
    {
        $this->accountId = $accountId;
        $this->accessKey = $accessKey;
        $this->accessSecret = $accessSecret;
        $this->bucket = $bucket;
    }

    /**
     * 创建请求对象
     * @param string $path 请求路径
     * @param string $bucket 存储桶
     * @return Request
     * @author Verdient。
     */
    protected function request($path, $bucket = null)
    {
        if (!$bucket) {
            $bucket = $this->bucket;
        }
        $request = new Request([
            'accessKey' => $this->accessKey,
            'accessSecret' => $this->accessSecret
        ]);
        $accountId = $this->accountId;
        if ($bucket) {
            $request->setUrl("https://$bucket.$accountId.r2.cloudflarestorage.com/$path");
        } else {
            $request->setUrl("https://$accountId.r2.cloudflarestorage.com/$path");
        }
        return $request;
    }
}
