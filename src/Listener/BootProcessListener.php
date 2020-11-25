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
namespace Huangdijia\ConfigAny\Listener;

use Huangdijia\ConfigAny\SourceInterface;
use Hyperf\Command\Event\BeforeHandle;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BeforeWorkerStart;
use Hyperf\Process\Event\BeforeProcessHandle;
use Hyperf\Server\Event\MainCoroutineServerStart;
use Hyperf\Utils\Coordinator\Constants;
use Hyperf\Utils\Coordinator\CoordinatorManager;
use Hyperf\Utils\Coroutine;
use Psr\Container\ContainerInterface;

class BootProcessListener implements ListenerInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var StdoutLoggerInterface
     */
    private $logger;

    /**
     * @var SourceInterface
     */
    private $source;

    public function __construct(ContainerInterface $container)
    {
        $this->config = $container->get(ConfigInterface::class);
        $this->logger = $container->get(StdoutLoggerInterface::class);
        $this->source = $container->get(SourceInterface::class);
    }

    public function listen(): array
    {
        return [
            BeforeWorkerStart::class,
            BeforeProcessHandle::class,
            BeforeHandle::class,
            MainCoroutineServerStart::class,
        ];
    }

    public function process(object $event)
    {
        if (! $this->config->get('config_any.enable', false)) {
            return;
        }

        if ($config = $this->source->toArray()) {
            $this->updateConfig($config);
        }

        if (! $this->config->get('config_any.use_standalone_process', true)) {
            Coroutine::create(function () {
                $interval = $this->config->get('config_any.interval', 5);

                retry(INF, function () use ($interval) {
                    $prevConfig = [];
                    while (true) {
                        $coordinator  = CoordinatorManager::until(Constants::WORKER_EXIT);
                        $workerExited = $coordinator->yield($interval);

                        if ($workerExited) {
                            break;
                        }

                        $config = $this->source->toArray();

                        if ($config !== $prevConfig) {
                            $this->updateConfig($config);
                        }

                        $prevConfig = $config;
                    }
                }, $interval * 1000);
            });
        }
    }

    protected function updateConfig(array $config)
    {
        $key            = $this->config->get('config_any.prefix');
        $configurations = $this->format($config);

        $this->config->set($key, $configurations);
        $this->logger->debug(sprintf('Config [%s] is updated', $key));
    }

    /**
     * Format configurations.
     */
    protected function format(array $config): array
    {
        return $config;
    }
}
