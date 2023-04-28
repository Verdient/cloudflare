<?php

declare(strict_types=1);

namespace Verdient\Cloudflare;

use Verdient\Cloudflare\API\AbstractClient;
use Verdient\Cloudflare\Traits\HasCachedClient;

/**
 * KV命名空间
 * @author Verdient。
 */
class KVNamespace extends AbstractClient
{
    use HasCachedClient;

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
     * 获取键
     * @return KVNamespaceKeys
     * @author Verdient。
     */
    public function keys()
    {
        return $this->_client(KVNamespaceKeys::class, $this->accountIdentifier, $this->namespaceIdentifier, $this->authorization);
    }

    /**
     * 获取元数据
     * @return KVNamespaceMetadata
     * @author Verdient。
     */
    public function metadata()
    {
        return $this->_client(KVNamespaceMetadata::class, $this->accountIdentifier, $this->namespaceIdentifier, $this->authorization);
    }

    /**
     * 获取值
     * @return KVNamespaceValues
     * @author Verdient。
     */
    public function values()
    {
        return $this->_client(KVNamespaceValues::class, $this->accountIdentifier, $this->namespaceIdentifier, $this->authorization);
    }

    /**
     * @inheritdoc
     * @author Verdient。
     */
    protected function resource(): string
    {
        return 'accounts/' . $this->accountIdentifier . '/storage/kv/namespaces/' . $this->namespaceIdentifier;
    }
}
