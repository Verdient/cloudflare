<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\R2;

use Exception;
use Verdient\http\builder\XmlBuilder;

/**
 * R2 存储桶
 * @author Verdient。
 */
class R2
{
    /**
     * @var string 账户编号
     * @author Verdient。
     */
    protected $accountId;

    /**
     * @var string 存储桶
     * @author Verdient。
     */
    protected $bucket;

    /**
     * @var string 秘钥标识
     * @author Verdient。
     */
    protected $accessKey;

    /**
     * @var string 授权秘钥
     * @author Verdient。
     */
    protected $accessSecret;

    /**
     * @var string $accountId 账户编号
     * @var string $bucket 存储桶
     * @var string $accessKey 秘钥标识
     * @var string $accessSecret 授权秘钥
     */
    public function __construct($accountId, $bucket, $accessKey, $accessSecret)
    {
        $this->accountId = $accountId;
        $this->bucket = $bucket;
        $this->accessKey = $accessKey;
        $this->accessSecret = $accessSecret;
    }

    /**
     * 上传对象
     * @param string $name 名称
     * @param string $content 内容
     * @param array $options 选项
     * @author Verdient。
     */
    public function putObject($name, $content, $options = [])
    {
        $request = $this
            ->request($name)
            ->setMethod('PUT')
            ->setContent((string) $content);
        foreach ($options as $name => $value) {
            $request->addHeader($name, $value);
        }
        $res = $request->send();
        if (!$res->getIsOK()) {
            throw new Exception($res->getErrorMessage());
        }
        return $res;
    }

    /**
     * 上传Json对象
     * @param string $name 名称
     * @param string $content 内容
     * @param array $options 选项
     * @author Verdient。
     */
    public function putJson($name, $content, $options = [])
    {
        $options['Content-Type'] = 'application/json';
        return $this->putObject($name, $content, $options);
    }

    /**
     * 获取对象
     * @param string $name 名称
     * @param array $options 选项
     * @author Verdient。
     */
    public function getObject($name, $options = [])
    {
        $request = $this
            ->request($name)
            ->setMethod('GET')
            ->setQuery($options);
        $res = $request->send();
        if (!$res->getIsOK()) {
            throw new Exception($res->getErrorMessage());
        }
        return $res;
    }

    /**
     * 枚举对象
     * @param string $name 名称
     * @param string $content 内容
     * @param array $options 选项
     * @author Verdient。
     */
    public function listObjects($options = [])
    {
        $options['list-type'] = 2;
        $request = $this
            ->request('')
            ->setMethod('GET');
        foreach ($options as $name => $value) {
            $request->addQuery($name, $value);
        }
        $res = $request->send();
        if (!$res->getIsOK()) {
            throw new Exception($res->getErrorMessage());
        }
        return $res;
    }

    /**
     * 删除对象
     * @param string $name 名称
     * @param array $options 选项
     * @author Verdient。
     */
    public function deleteObject($name, $options = [])
    {
        $request = $this
            ->request($name)
            ->setMethod('DELETE');
        foreach ($options as $name => $value) {
            $request->addQuery($name, $value);
        }
        $res = $request->send();
        if (!$res->getIsOK()) {
            throw new Exception($res->getErrorMessage());
        }
        return $res;
    }

    /**
     * 批量删除对象
     * @param array $objects 要删除的对象
     * @author Verdient。
     */
    public function deleteObjects($objects)
    {
        $xmlBuilder = new XmlBuilder;
        $xmlBuilder->charset = 'UTF-8';
        $xmlBuilder->rootTag = 'Delete';
        $xmlBuilder->itemTag = 'Object';
        $xmlBuilder->setElements(array_map(function ($value) {
            return [
                'Key' => mb_convert_encoding($value, 'UTF-8', 'auto')
            ];
        }, $objects));
        $request = $this
            ->request('')
            ->addQuery('delete', '')
            ->addHeader('Content-Type', 'application/xml')
            ->setContent($xmlBuilder->toString())
            ->setMethod('POST');
        $res = $request->send();
        if (!$res->getIsOK()) {
            throw new Exception($res->getErrorMessage());
        }
        return $res;
    }

    /**
     * 获取对象元数据
     * @param string $name 名称
     * @param array $options 选项
     * @author Verdient。
     */
    public function headObject($name, $options = [])
    {
        $request = $this
            ->request($name)
            ->setMethod('HEAD');
        foreach ($options as $name => $value) {
            $request->addQuery($name, $value);
        }
        $res = $request->send();
        if (!$res->getIsOK()) {
            throw new Exception($res->getErrorMessage());
        }
        return $res;
    }

    /**
     * @inheritdoc
     * @author Verdient。
     */
    public function request($path = ''): Request
    {
        $bucket = $this->bucket;
        $request = new Request([
            'accessKey' => $this->accessKey,
            'accessSecret' => $this->accessSecret
        ]);
        $accountId = $this->accountId;
        $request->setUrl("https://$bucket.$accountId.r2.cloudflarestorage.com/$path");
        return $request;
    }
}
