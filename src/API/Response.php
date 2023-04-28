<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\API;

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
        if ($result->isOK) {
            $result->data = $body;
        } else {
            if (!empty($body['errors'])) {
                $result->errorCode = $body['errors'][0]['code'];
                $result->errorMessage = $body['errors'][0]['message'];
            } else {
                $result->errorCode = $response->getStatusCode();
                $result->errorMessage = $response->getStatusMessage();
            }
        }
        return $result;
    }
}
