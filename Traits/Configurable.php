<?php

declare(strict_types=1);

namespace Verdient\Cloudflare\Traits;

/**
 * 可配置的
 * @author Verdient。
 */
trait Configurable
{
    /**
     * @param array $options 选项
     * @author Verdient。
     */
    public function __construct($options = [])
    {
        foreach ($options as $name => $value) {
            if (property_exists($this, $name)) {
                if (!$value && $this->$name) {
                    continue;
                }
                $this->$name = $value;
            }
        }
    }
}
