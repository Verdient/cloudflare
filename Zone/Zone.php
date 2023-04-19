<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\Zone;

use Verdient\Cloudflare\API\AbstractClient;
use Verdient\Cloudflare\API\Response;
use Verdient\Cloudflare\Traits\Constructible;
use Verdient\Cloudflare\Traits\HasList;

/**
 * 区域
 * @author Verdient。
 */
class Zone extends AbstractClient
{
    use Constructible;
    use HasList;

    /**
     * @inheritdoc
     * @author Verdient。
     */
    protected function resource(): string
    {
        return 'zones';
    }

    /**
     * 清除缓存
     * @param string $identifier 域名编号
     * @param string
     * @return Response
     * @author Verdient。
     */
    public function purgeCache($identifier, $options): Response
    {
        return $this
            ->request("$identifier/purge_cache")
            ->setMethod('POST')
            ->setBody($options)
            ->send();
    }
}
