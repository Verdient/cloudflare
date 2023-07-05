<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\R2;

use Exception;
use Verdient\http\builder\XmlBuilder;

/**
 * 对象
 * @author Verdient。
 */
class Objects extends AbstractClient
{
    /**
     * 上传对象
     * @param string $name 名称
     * @param string $content 内容
     * @param array $options 选项
     * @return Response
     * @author Verdient。
     */
    public function put($name, $content, $options = [])
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
     * @return Response
     * @author Verdient。
     */
    public function putJson($name, $content, $options = [])
    {
        $options['Content-Type'] = 'application/json';
        return $this->put($name, $content, $options);
    }

    /**
     * 获取对象
     * @param string $name 名称
     * @param array $options 选项
     * @return Response
     * @author Verdient。
     */
    public function get($name, $options = [])
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
     * @param array $options 选项
     * @return Response
     * @author Verdient。
     */
    public function list($options = [])
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
     * @return Response
     * @author Verdient。
     */
    public function delete($name, $options = [])
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
     * @return Response
     * @author Verdient。
     */
    public function bulkDelete($objects)
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
    public function head($name, $options = [])
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
     * 批量枚举对象
     * @param array $options 选项
     * @author Verdient。
     */
    public function batch($options = [])
    {
        $hasMore = true;
        $continuationToken = null;
        while ($hasMore) {
            $params = $options;
            if ($continuationToken) {
                $params['continuation-token'] = $continuationToken;
            }
            $res = $this->listWithRetry($options);
            if (!$res->getIsOK()) {
                throw new Exception($res->getErrorMessage());
            }
            $resData = $res->getData();
            yield $resData['Contents'] ?? [];
            $hasMore = isset($resData['NextContinuationToken']);
            if ($hasMore) {
                $continuationToken = $resData['NextContinuationToken'];
            }
        }
    }

    /**
     * 逐个枚举对象
     * @param array $options 选项
     * @author Verdient。
     */
    public function each($options = [])
    {
        foreach ($this->batch($options) as $rows) {
            foreach ($rows as $row) {
                yield $row;
            }
        }
    }

    /**
     * 带有重试的获取列表
     * @return Response
     * @author Verdient。
     */
    protected function listWithRetry($options, $limit = 3): Response
    {
        $exception = null;
        for ($i = 0; $i < $limit; $i++) {
            try {
                $res = $this->list($options);
                return $res;
            } catch (\Throwable $e) {
                $exception = $e;
            }
        }
        throw $exception;
    }
}
