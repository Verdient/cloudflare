<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\Traits;

use Verdient\Cloudflare\Cloudflare;

/**
 * 可构造的
 * @author Verdient。
 */
trait Constructible
{
    /**
     * @var Cloudflare Cloudflare
     * @author Verdient。
     */
    protected $cloudflare;

    /**
     * @param Cloudflare $cloudflare
     * @author Verdient。
     */
    public function __construct(Cloudflare $cloudflare)
    {
        $this->cloudflare = $cloudflare;
    }

    /**
     * 获取Cloudflare对象
     * @return Cloudflare|null
     * @author Verdinet。
     */
    public function cloudflare()
    {
        return $this->cloudflare;
    }
}
