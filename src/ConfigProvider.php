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
namespace Huangdijia\ConfigArray;

use Huangdijia\ConfigArray\Listener\BootProcessListener;
use Huangdijia\ConfigArray\Listener\OnPipeMessageListener;
use Huangdijia\ConfigArray\Process\ConfigFetcherProcess;

class ConfigProvider
{
    public function __invoke(): array
    {
        defined('BASE_PATH') or define('BASE_PATH', __DIR__ . '/../../../');

        return [
            'dependencies' => [
            ],
            'processes' => [
                ConfigFetcherProcess::class,
            ],
            'listeners' => [
                BootProcessListener::class,
                OnPipeMessageListener::class,
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id'          => 'config',
                    'description' => 'The config for config_array.',
                    'source'      => __DIR__ . '/../publish/config_array.php',
                    'destination' => BASE_PATH . '/config/autoload/config_array.php',
                ],
            ],
        ];
    }
}
