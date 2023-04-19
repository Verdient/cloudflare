<?php

declare(strict_types=1);

namespace Verdient\Cloudflare;

use Exception;
use Verdient\Cloudflare\R2\R2;
use Verdient\Cloudflare\Traits\Configurable;
use Verdient\Cloudflare\Zone\Zone;

/**
 * Cloudflare
 * @author Verdient。
 */
class Cloudflare
{
    use Configurable;

    /**
     * @var array 缓存的客户端
     * @author Verdient。
     */
    protected $cachedClients = [];

    /**
     * @var string 账户编号
     * @author Verdient。
     */
    protected $accountId;

    /**
     * @var string API授权秘钥
     * @author Verdient。
     */
    protected $apiAuthorization;

    /**
     * @var string KV空间编号
     * @author Verdient。
     */
    protected $kvNamespaceId;

    /**
     * @var string R2接入点
     * @author Verdient。
     */
    protected $r2Endpoint = 'r2.cloudflarestorage.com';

    /**
     * @var string R2文件夹
     * @author Verdient。
     */
    protected $r2Bucket;

    /**
     * @var string R2秘钥标识
     * @author Verdient。
     */
    protected $r2AccessKey;

    /**
     * @var string R2访问秘钥
     * @author Verdient。
     */
    protected $r2AccessSecret;

    /**
     * @var string R2域名编号
     * @author Verdient。
     */
    protected $r2ZoneId;

    /**
     * @var string R2域名
     * @author Verdient。
     */
    protected $r2ZoneName;

    /**
     * 获取属性
     * @return mixed
     * @author Verdient。
     */
    public function get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new Exception('Getting unknown property: ' . $name);
    }

    /**
     * 获取客户端
     * @param string $class 类名
     * @return mixed
     * @author Verdient。
     */
    protected function client($class)
    {
        if (!isset($this->cachedClients[$class])) {
            $this->cachedClients[$class] = new $class($this);
        }
        return $this->cachedClients[$class];
    }

    /**
     * R2
     * @return R2
     * @author Verdient。
     */
    public function r2(): R2
    {
        return $this->client(R2::class);
    }

    /**
     * Zone
     * @return Zone
     * @author Veedient。
     */
    public function zone()
    {
        return $this->client(Zone::class);
    }
}
