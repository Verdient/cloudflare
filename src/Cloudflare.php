<?php

declare(strict_types=1);

namespace Verdient\Cloudflare;

use Exception;
use Verdient\Cloudflare\R2\R2;
use Verdient\Cloudflare\Traits\Configurable;
use Verdient\Cloudflare\Zone;

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
    protected function _client($class, ...$options)
    {
        $key = serialize([$class, $options]);
        if (!isset($this->cachedClients[$key])) {
            $this->cachedClients[$key] = new $class(...$options);
        }
        return $this->cachedClients[$key];
    }

    /**
     * R2
     * @param string $bucket 存储桶
     * @return R2
     * @author Verdient。
     */
    public function r2($bucket = null): R2
    {
        if (!$bucket) {
            $bucket = $this->r2Bucket;
        }
        return $this->_client(R2::class, $this->accountId, $bucket, $this->r2AccessKey, $this->r2AccessSecret);
    }

    /**
     * 域
     * @return Zone
     * @author Verdient。
     */
    public function zone()
    {
        return $this->_client(Zone::class, $this->apiAuthorization);
    }

    /**
     * DNS
     * @param string $zoneIdentifier 域编号
     * @return DNS
     * @author Verdient。
     */
    public function dns($zoneIdentifier)
    {
        return $this->_client(DNS::class, $zoneIdentifier, $this->apiAuthorization);
    }

    /**
     * 防火墙规则
     * @param string $zoneIdentifier 域编号
     * @return FirewallRule
     * @author Verdient。
     */
    public function firewallRule($zoneIdentifier)
    {
        return $this->_client(FirewallRule::class, $zoneIdentifier, $this->apiAuthorization);
    }
}
