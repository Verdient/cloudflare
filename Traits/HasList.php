<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\Traits;

use Exception;
use Iterator;

/**
 * 包含列表
 * @author Verdient。
 */
trait HasList
{
    /**
     * 获取域名列表
     * @param array $options 参数
     * @return Response
     * @author Verdient。
     */
    public function list($options = [])
    {
        return $this
            ->request('')
            ->setMethod('GET')
            ->setQuery($options)
            ->send();
    }

    /**
     * 批量遍历元素
     * @author Verdient。
     */
    public function batch($options = [])
    {
        return $this->iterator($options);
    }

    /**
     * 遍历每个元素
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
     * 迭代器
     * @return Iterator
     * @author Verdient。
     */
    protected function iterator($options = []): Iterator
    {
        unset($options['page']);
        $continue = true;
        while ($continue === true) {
            $continue = false;
            $res = $this->listWithRetry($options);
            if (!$res->getIsOK()) {
                throw new Exception($res->getErrorMessage());
            }
            $body = $res->getData();
            $resultInfo = $body['result_info'];
            $totalPages = isset($resultInfo['total_pages']) ? $resultInfo['total_pages'] : ceil($resultInfo['total_count'] / $resultInfo['per_page']);
            $continue = $resultInfo['page'] < $totalPages;
            if ($continue) {
                $options['page'] = isset($options['page']) ? ($options['page'] + 1) : 2;
            }
            yield $body['result'];
        }
    }


    protected function listWithRetry($options, $limit = 3)
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
