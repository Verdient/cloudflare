<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\Traits;

/**
 * 包含缓存客户端
 * @author Verdient。
 */
trait HasCachedClient
{
    /**
     * @var array 缓存的客户端
     * @author Verdient。
     */
    protected $cachedClients = [];

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
}
