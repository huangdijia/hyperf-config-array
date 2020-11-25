<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-config-array.
 *
 * @link     https://github.com/huangdijia/hyperf-config-array
 * @document https://github.com/huangdijia/hyperf-config-array/blob/main/README.md
 * @contact  huangdijia@gmail.com
 * @license  https://github.com/huangdijia/hyperf-config-array/blob/main/LICENSE
 */
namespace Huangdijia\ConfigArray\Source;

use Huangdijia\ConfigArray\SourceInterface;

class DemoSource implements SourceInterface
{
    public function toArray(): array
    {
        return [
            'bar' => [
                'foo' => date('Y-m-d'),
            ],
        ];
    }
}
