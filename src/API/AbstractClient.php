<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\API;

/**
 * 抽象客户端
 * @author Verdient。
 */
abstract class AbstractClient
{
    /**
     * @var string 基础地址
     * @author Verdient。
     */
    const BASE_URL = 'https://api.cloudflare.com/client/v4/';

    /**
     * @var string 授权秘钥
     * @author Verdient。
     */
    protected $authorization;

    /**
     * @param string $authorization 授权秘钥
     * @author Verdient。
     */
    public function __construct($authorization)
    {
        $this->authorization = $authorization;
    }

    /**
     * 资源
     * @return string
     * @author Verdient。
     */
    abstract protected function resource(): string;

    /**
     * 请求
     * @param string $path 路径
     * @return Request
     * @author Verdient。
     */
    public function request($path = null): Request
    {
        if ($path) {
            $path = $this->resource() . '/' . $path;
        } else {
            $path = $this->resource();
        }
        return (new Request())
            ->addHeader('Authorization', 'Bearer ' . $this->authorization)
            ->setUrl(static::BASE_URL . $path);
    }
}
