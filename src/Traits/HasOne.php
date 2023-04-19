<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\Traits;

use Verdient\Cloudflare\API\Response;

/**
 * 包含详情
 * @author Verdient。
 */
trait HasOne
{
    /**
     * 获取详情
     * @param string $identifier 编号
     * @return Response
     * @author Verdient。
     */
    public function one($identifier): Response
    {
        return $this
            ->request($identifier)
            ->setMethod('GET')
            ->send();
    }
}
