<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\API;

use Verdient\http\Request as HttpRequest;

/**
 * 请求
 * @author Verdient。
 */
class Request extends HttpRequest
{
    /**
     * @inheritdoc
     * @return Response
     * @author Verdient。
     */
    public function send()
    {
        return new Response(parent::send());
    }
}
