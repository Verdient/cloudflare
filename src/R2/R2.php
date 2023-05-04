<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\R2;

use Verdient\Cloudflare\Traits\HasCachedClient;

/**
 * R2 存储桶
 * @author Verdient。
 */
class R2
{
    use HasCachedClient;

    /**
     * @var string 账户编号
     * @author Verdient。
     */
    protected $accountId;

    /**
     * @var string 存储桶
     * @author Verdient。
     */
    protected $bucket;

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
     * @var string $accountId 账户编号
     * @var string $bucket 存储桶
     * @var string $accessKey 秘钥标识
     * @var string $accessSecret 授权秘钥
     */
    public function __construct($accountId, $bucket, $accessKey, $accessSecret)
    {
        $this->accountId = $accountId;
        $this->bucket = $bucket;
        $this->accessKey = $accessKey;
        $this->accessSecret = $accessSecret;
    }

    /**
     * 对象
     * @return Objects
     * @author Verdient。
     */
    public function object()
    {
        return $this->_client(Objects::class, $this->accountId, $this->accessKey, $this->accessSecret, $this->bucket);
    }

    /**
     * 存储桶
     * @return Bucket
     * @author Verdient。
     */
    public function bucket()
    {
        return $this->_client(Bucket::class, $this->accountId, $this->accessKey, $this->accessSecret);
    }
}
