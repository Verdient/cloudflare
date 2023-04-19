<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\Traits;

use Verdient\Cloudflare\API\Response;

/**
 * 包含创建
 * @author Verdient。
 */
trait HasCreate
{
    /**
     * 创建
     * @param array $options 参数
     * @return Response
     * @author Verdient。
     */
    public function create($options): Response
    {
        return $this
            ->request()
            ->setMethod('POST')
            ->setBody($options)
            ->send();
    }
}
