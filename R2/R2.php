<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\R2;

use Exception;
use Verdient\Cloudflare\Traits\Constructible;
use Verdient\http\builder\XmlBuilder;

/**
 * R2
 * @author Verdient。
 */
class R2
{
    use Constructible;

    /**
     * 上传对象
     * @param string $name 名称
     * @param string $content 内容
     * @param array $options 选项
     * @param string $bucket 目录
     * @author Verdient。
     */
    public function putObject($name, $content, $options = [], $bucket = null)
    {
        $request = $this
            ->request($name, $bucket)
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
     * @param string $bucket 目录
     * @author Verdient。
     */
    public function putJson($name, $content, $options = [], $bucket = null)
    {
        $options['Content-Type'] = 'application/json';
        return $this->putObject($name, $content, $options, $bucket);
    }

    /**
     * 枚举对象
     * @param string $name 名称
     * @param string $content 内容
     * @param array $options 选项
     * @param string $bucket 目录
     * @author Verdient。
     */
    public function listObjects($options = [], $bucket = null)
    {
        $options['list-type'] = 2;
        $request = $this
            ->request('', $bucket)
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
     * @param string $bucket 目录
     * @author Verdient。
     */
    public function deleteObject($name, $options = [], $bucket = null)
    {
        $request = $this
            ->request($name, $bucket)
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
     * @param string $bucket 目录
     * @author Verdient。
     */
    public function deleteObjects($objects, $bucket = null)
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
            ->request('', $bucket)
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
     * @param string $bucket 目录
     * @author Verdient。
     */
    public function headObject($name, $options = [], $bucket = null)
    {
        $request = $this
            ->request($name, $bucket)
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
     * 清除缓存
     * @param string $name 名称
     * @author Verdient。
     */
    public function purgeCache($name)
    {
        $zoneId = $this->cloudflare()->get('r2ZoneId');
        $zoneName = $this->cloudflare()->get('r2ZoneName');
        $res = $this->cloudflare()
            ->zone()
            ->purgeCache($zoneId, [
                'files' => [
                    'https://' . $zoneName . '/' . $name,
                    'http://' . $zoneName . '/' . $name
                ]
            ]);
        if (!$res->getIsOK()) {
            throw new Exception($res->getErrorMessage());
        }
        return $res;
    }

    /**
     * 批量清除缓存
     * @param string $prefix 前缀
     * @author Verdient。
     */
    public function purgeCaches($prefix)
    {
        $zoneId = $this->cloudflare()->get('r2ZoneId');
        $zoneName = $this->cloudflare()->get('r2ZoneName');
        $res = $this->cloudflare()
            ->zone()
            ->purgeCache($zoneId, [
                'prefixes' => [
                    'https://' . $zoneName . '/' . $prefix,
                    'http://' . $zoneName . '/' . $prefix
                ]
            ]);
        if (!$res->getIsOK()) {
            throw new Exception($res->getErrorMessage());
        }
        return $res;
    }

    /**
     * @inheritdoc
     * @author Verdient。
     */
    public function request($path, $bucket = null): Request
    {
        if (!$bucket) {
            $bucket = $this->cloudflare()->get('r2Bucket');
        }
        $request = new Request([
            'accessKey' => $this->cloudflare()->get('r2AccessKey'),
            'accessSecret' => $this->cloudflare()->get('r2AccessSecret')
        ]);
        $endpoint = $this->cloudflare()->get('r2Endpoint');
        $accountId = $this->cloudflare()->get('accountId');
        $request->setUrl("https://$bucket.$accountId.$endpoint/$path");
        return $request;
    }
}
