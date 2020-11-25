<?php

declare(strict_types=1);
/**
 * This file is part of Smsease.
 *
 * @link     https://github.com/huangdijia/hyperf-config-any
 * @document https://github.com/huangdijia/hyperf-config-any/blob/main/README.md
 * @contact  huangdijia@gmail.com
 * @license  https://github.com/huangdijia/hyperf-config-any/blob/main/LICENSE
 */
namespace Huangdijia\ConfigAny;

use Huangdijia\ConfigAny\Listener\BootProcessListener;
use Huangdijia\ConfigAny\Listener\OnPipeMessageListener;
use Huangdijia\ConfigAny\Process\ConfigFetcherProcess;

class ConfigProvider
{
    public function __invoke(): array
    {
        defined('BASE_PATH') or define('BASE_PATH', __DIR__ . '/../../../');

        var_dump($this->config->get('config_any'));

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
                    'description' => 'The config for config_any.',
                    'source'      => __DIR__ . '/../publish/config_any.php',
                    'destination' => BASE_PATH . '/config/autoload/config_any.php',
                ],
            ],
        ];
    }
}
