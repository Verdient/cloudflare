<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\R2;

use Verdient\http\Response as HttpResponse;
use Verdient\HttpAPI\AbstractResponse;
use Verdient\HttpAPI\Result;

/**
 * 响应
 * @author Verdient。
 */
class Response extends AbstractResponse
{
    /**
     * @inheritdoc
     * @author Verdient。
     */
    protected function normailze(HttpResponse $response): Result
    {
        $result = new Result;
        $statusCode = $response->getStatusCode();
        $body = $response->getBody();
        $result->isOK = $statusCode >= 200 && $statusCode < 300;
        if (!$result->isOK) {
            $result->errorCode = $body['Code'] ?? $statusCode;
            $result->errorMessage = $body['Message'] ?? $response->getStatusMessage();
        } else {
            $result->data = $body;
        }
        return $result;
    }
}
