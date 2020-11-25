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

return [
    'enable'                 => env('CONFIG_ANY_ENABLE', false),
    'interval'               => env('CONFIG_ANY_INTERVAL', 5),
    'use_standalone_process' => true,
    'source'                 => \Huangdijia\ConfigArray\Source\DemoSource::class,
    'mapping'                => [
        // source => target
        'bar.foo' => 'bar.foo',
    ],
];
