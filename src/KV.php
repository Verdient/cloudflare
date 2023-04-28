<?php

declare(strict_types=1);

namespace Verdient\Cloudflare;

use Exception;
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
     * @var string 命名空间标识符
     * @author Verdient。
     */
    protected $namespaceIdentifier;

    /**
     * @var string $accountIdentifier 账户识别码
     * @param string $authorization 授权秘钥
     * @param string $namespaceIdentifier 命名空间标识符
     * @author Verdient。
     */
    public function __construct($accountIdentifier, $authorization, $namespaceIdentifier)
    {
        $this->accountIdentifier = $accountIdentifier;
        $this->namespaceIdentifier = $namespaceIdentifier;
        parent::__construct($authorization);
    }

    /**
     * 获取命名空间对象
     * @param string $identifier 识别码
     * @return KVNamespace
     * @author Verdient。
     */
    public function namespace($identifier = null)
    {
        if (!$identifier) {
            if (!$this->namespaceIdentifier) {
                throw new Exception('Namespace identifier can not be blank');
            }
            $identifier = $this->namespaceIdentifier;
        }
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
