<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\Traits;

use Verdient\Cloudflare\API\Response;

/**
 * 包含更新
 * @author Verdient。
 */
trait HasUpdate
{
    /**
     * 更新
     * @param string $identifier 编号
     * @param array $options 参数
     * @return Response
     * @author Verdient。
     */
    public function update($identifier, $options): Response
    {
        return $this
            ->request($identifier)
            ->setMethod('PUT')
            ->setBody($options)
            ->send();
    }
}
