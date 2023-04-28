<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\Traits;

use Exception;
use Iterator;
use Verdient\Cloudflare\API\Response;

/**
 * 包含列表
 * @author Verdient。
 */
trait HasList
{
    /**
     * 获取列表
     * @param array $options 参数
     * @return Response
     * @author Verdient。
     */
    public function list($options = []): Response
    {
        return $this
            ->request()
            ->setMethod('GET')
            ->setQuery($options)
            ->send();
    }

    /**
     * 批量遍历元素
     * @return Iterator
     * @author Verdient。
     */
    public function batch($options = []): Iterator
    {
        return $this->iterator($options);
    }

    /**
     * 单个遍历元素
     * @return Iterator
     * @author Verdient。
     */
    public function each($options = []): Iterator
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
            if (isset($resultInfo['cursor'])) {
                if (!empty($resultInfo['cursor'])) {
                    $continue = true;
                    $options['cursor'] = $resultInfo['cursor'];
                }
            } else if (isset($resultInfo['page'])) {
                $totalPages = isset($resultInfo['total_pages']) ? $resultInfo['total_pages'] : ceil($resultInfo['total_count'] / $resultInfo['per_page']);
                $continue = $resultInfo['page'] < $totalPages;
                if ($continue) {
                    $options['page'] = isset($options['page']) ? ($options['page'] + 1) : 2;
                }
            }
            yield $body['result'];
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
