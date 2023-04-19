<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\Traits;

use Verdient\Cloudflare\API\Response;

/**
 * 包含删除
 * @author Verdient。
 */
trait HasDelete
{
    /**
     * 删除
     * @param string $identifier 编号
     * @return Response
     * @author Verdient。
     */
    public function delete($identifier): Response
    {
        return $this
            ->request($identifier)
            ->setMethod('DELETE')
            ->send();
    }
}
