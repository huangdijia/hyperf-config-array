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
namespace Huangdijia\ConfigArray\Listener;

use Huangdijia\ConfigArray\SourceInterface;
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

        $sourceClass = $this->config->get('config_array.source');

        if ($sourceClass && class_exists($sourceClass)) {
            $this->source = make($sourceClass);
        }
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
        if (! $this->config->get('config_array.enable', false)) {
            return;
        }

        if (! ($this->source instanceof SourceInterface)) {
            return;
        }

        if ($config = $this->source->toArray()) {
            $this->updateConfig($config);
        }

        if (! $this->config->get('config_array.use_standalone_process', true)) {
            Coroutine::create(function () {
                $interval = $this->config->get('config_array.interval', 5);

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
        $mapping            = $this->config->get('config_array.mapping');
        $configurations     = $this->format($config);

        if (is_string($mapping)) {
            $this->config->set($mapping, $configurations);
            $this->logger->debug(sprintf('Config [%s] is updated', $mapping));
        } elseif (is_array($mapping)) {
            foreach ($mapping as $source => $key) {
                $this->config->set((string) $key, data_get($configurations, $source));
                $this->logger->debug(sprintf('Config [%s] is updated', $key));
            }
        }
    }

    /**
     * Format configurations.
     */
    protected function format(array $config): array
    {
        return $config;
    }
}
