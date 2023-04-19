<?php

declare(strict_types=1);

namespace Verdient\Cloudflare;

use Verdient\Cloudflare\API\AbstractClient;
use Verdient\Cloudflare\Traits\HasCreate;
use Verdient\Cloudflare\Traits\HasDelete;
use Verdient\Cloudflare\Traits\HasList;
use Verdient\Cloudflare\Traits\HasOne;
use Verdient\Cloudflare\Traits\HasUpdate;

/**
 * 防火墙规则
 * @author Verdient。
 */
class FirewallRule extends AbstractClient
{
    use HasCreate;
    use HasDelete;
    use HasList;
    use HasOne;
    use HasUpdate;

    /**
     * @var string 域编号
     * @author Verdient。
     */
    protected $zoneIdentifier;

    /**
     * @param string $zoneIdentifier 域编号
     * @param string $authorization 授权秘钥
     * @author Verdient。
     */
    public function __construct($zoneIdentifier, $authorization)
    {
        $this->zoneIdentifier = $zoneIdentifier;
        parent::__construct($authorization);
    }

    /**
     * @inheritdoc
     * @author Verdient。
     */
    protected function resource(): string
    {
        return 'zones/' . $this->zoneIdentifier . '/firewall/rules';
    }
}
