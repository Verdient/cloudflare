<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\API;

use Verdient\Cloudflare\Traits\Constructible;

/**
 * 抽象客户端
 * @author Verdient。
 */
abstract class AbstractClient
{
    use Constructible;

    /**
     * @var string 基础地址
     * @author Verdient。
     */
    const BASE_URL = 'https://api.cloudflare.com/client/v4/';

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
    public function request($path): Request
    {
        return (new Request())
            ->addHeader('Authorization', 'Bearer ' . $this->cloudflare()->get('apiAuthorization'))
            ->setUrl(static::BASE_URL . $this->resource() . '/' . $path);
    }
}
