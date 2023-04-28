<?php

declare(strict_types=1);

namespace Verdient\Cloudflare;

use Verdient\Cloudflare\API\AbstractClient;
use Verdient\Cloudflare\Traits\HasCachedClient;
use Verdient\Cloudflare\Traits\HasCreate;
use Verdient\Cloudflare\Traits\HasDelete;
use Verdient\Cloudflare\Traits\HasList;
use Verdient\Cloudflare\Traits\HasOne;
use Verdient\Cloudflare\Traits\HasUpdate;

/**
 * KV存储
 * @author Verdient。
 */
class KV extends AbstractClient
{
    use HasCachedClient;
    use HasCreate;
    use HasDelete;
    use HasList;
    use HasOne;
    use HasUpdate;

    /**
     * @var string 账户识别码
     * @author Verdient。
     */
    protected $accountIdentifier;

    /**
     * @var string $accountIdentifier 账户识别码
     * @param string $authorization 授权秘钥
     * @author Verdient。
     */
    public function __construct($accountIdentifier, $authorization)
    {
        $this->accountIdentifier = $accountIdentifier;
        parent::__construct($authorization);
    }

    /**
     * 获取命名空间对象
     * @param string $identifier 识别码
     * @return KVNamespace
     * @author Verdient。
     */
    public function namespace($identifier)
    {
        return $this->_client(KVNamespace::class, $this->accountIdentifier, $identifier, $this->authorization);
    }

    /**
     * @inheritdoc
     * @author Verdient。
     */
    protected function resource(): string
    {
        return 'accounts/' . $this->accountIdentifier . '/storage/kv/namespaces';
    }
}
