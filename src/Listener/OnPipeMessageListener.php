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

use Huangdijia\ConfigArray\PipeMessage;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\OnPipeMessage;
use Hyperf\Process\Event\PipeMessage as UserProcessPipMessage;
use Psr\Container\ContainerInterface;

class OnPipeMessageListener implements ListenerInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var StdoutLoggerInterface
     */
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->config = $container->get(ConfigInterface::class);
        $this->logger = $container->get(StdoutLoggerInterface::class);
    }

    /**
     * @return string[] returns the events that you want to listen
     */
    public function listen(): array
    {
        return [
            OnPipeMessage::class,
            UserProcessPipMessage::class,
        ];
    }

    /**
     * Handle the Event when the event is triggered, all listeners will
     * complete before the event is returned to the EventDispatcher.
     */
    public function process(object $event)
    {
        if (! $this->config->get('config_array.enable', false)) {
            return;
        }

        if (property_exists($event, 'data') && $event->data instanceof PipeMessage) {
            /** @var PipeMessage $data */
            $data = $event->data;

            $mapping            = $this->config->get('config_array.mapping');
            $configurations     = $data->configurations;

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
    }
}
