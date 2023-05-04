<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\R2;

use Exception;

/**
 * 存储桶
 * @author Verdient。
 */
class Bucket extends AbstractClient
{
    /**
     * 枚举存储桶
     * @return Response
     * @author Verdient。
     */
    public function list()
    {
        $request = $this
            ->request('')
            ->setMethod('GET');
        $res = $request->send();
        if (!$res->getIsOK()) {
            throw new Exception($res->getErrorMessage());
        }
        return $res;
    }

    /**
     * 获取存储桶元数据
     * @param string $name 名称
     * @author Verdient。
     */
    public function head($name)
    {
        $request = $this
            ->request('', $name)
            ->setMethod('HEAD');
        $res = $request->send();
        if (!$res->getIsOK()) {
            throw new Exception($res->getErrorMessage());
        }
        return $res;
    }

    /**
     * 批量枚举存储桶
     * @author Verdient。
     */
    public function batch()
    {
        $res = $this->list();
        if (!$res->getIsOK()) {
            throw new Exception($res->getErrorMessage());
        }
        $resData = $res->getData();
        yield $resData['Buckets']['Bucket'];
    }

    /**
     * 逐个枚举存储桶
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
}
