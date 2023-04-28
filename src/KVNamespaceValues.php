<?php

declare(strict_types=1);

namespace Verdient\Cloudflare;

use Verdient\Cloudflare\API\AbstractClient;
use Verdient\Cloudflare\Traits\HasDelete;
use Verdient\Cloudflare\Traits\HasOne;
use Verdient\Cloudflare\Traits\HasUpdate;

/**
 * KV命名空间值
 * @author Verdient。
 */
class KVNamespaceValues extends AbstractClient
{
    use HasDelete;
    use HasOne;
    use HasUpdate;

    /**
     * @var string 账户识别码
     * @author Verdient。
     */
    protected $accountIdentifier;

    /**
     * @var string 命名空间识别码
     * @author Verdient。
     */
    protected $namespaceIdentifier;

    /**
     * @var string $accountIdentifier 账户识别码
     * @param string $namespaceIdentifier 命名空间识别码
     * @param string $authorization 授权秘钥
     * @author Verdient。
     */
    public function __construct($accountIdentifier, $namespaceIdentifier, $authorization)
    {
        $this->accountIdentifier = $accountIdentifier;
        $this->namespaceIdentifier = $namespaceIdentifier;
        parent::__construct($authorization);
    }

    /**
     * @inheritdoc
     * @author Verdient。
     */
    protected function resource(): string
    {
        return 'accounts/' . $this->accountIdentifier . '/storage/kv/namespaces/' . $this->namespaceIdentifier . '/values';
    }
}
