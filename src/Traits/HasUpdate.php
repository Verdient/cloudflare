<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\Traits;

use Verdient\Cloudflare\API\Request;
use Verdient\Cloudflare\API\Response;
use Verdient\http\builder\BuilderInterface;

/**
 * 包含更新
 * @author Verdient。
 */
trait HasUpdate
{
    /**
     * 更新
     * @param string $identifier 编号
     * @param array|BuilderInterface $options 参数
     * @return Response
     * @author Verdient。
     */
    public function update($identifier, $options): Response
    {
        /** @var Request */
        $request = $this->request($identifier);
        $request->setMethod('PUT');
        if ($options instanceof BuilderInterface) {
            $request->setContent($options);
        } else {
            $request->setBody($options);
        }
        return $request->send();
    }
}
