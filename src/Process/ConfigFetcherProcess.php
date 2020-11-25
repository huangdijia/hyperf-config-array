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
namespace Huangdijia\ConfigAny\Process;

use Huangdijia\ConfigAny\PipeMessage;
use Huangdijia\ConfigAny\SourceInterface;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\ProcessCollector;
use Psr\Container\ContainerInterface;
use Swoole\Server;

class ConfigFetcherProcess extends AbstractProcess
{
    public $name = 'config-any-fetcher';

    /**
     * @var Server
     */
    private $server;

    /**
     * @var SourceInterface
     */
    private $source;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var array
     */
    private $cacheConfig;

    /**
     * @var StdoutLoggerInterface
     */
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->source = $container->get(SourceInterface::class);
        $this->config = $container->get(ConfigInterface::class);
        $this->logger = $container->get(StdoutLoggerInterface::class);
    }

    public function bind($server): void
    {
        $this->server = $server;
        parent::bind($server);
    }

    public function isEnable($server): bool
    {
        return $server instanceof Server
            && $this->config->get('config_any.enable', false)
            && $this->config->get('config_any.use_standalone_process', true);
    }

    public function handle(): void
    {
        while (true) {
            $config = $this->source->toArray();

            if ($config !== $this->cacheConfig) {
                $this->cacheConfig = $config;
                $workerCount       = $this->server->setting['worker_num'] + $this->server->setting['task_worker_num'] - 1;
                $pipeMessage       = new PipeMessage($this->format($config));

                for ($workerId = 0; $workerId <= $workerCount; ++$workerId) {
                    $this->server->sendMessage($pipeMessage, $workerId);
                }

                $string    = serialize($pipeMessage);
                $processes = ProcessCollector::all();

                /** @var \Swoole\Process $process */
                foreach ($processes as $process) {
                    $result = $process->exportSocket()->send($string, 10);
                    if ($result === false) {
                        $this->logger->error('Configuration synchronization failed. Please restart the server.');
                    }
                }
            }

            sleep($this->config->get('config_any.interval', 5));
        }
    }

    /**
     * Format kv configurations.
     */
    protected function format(array $config): array
    {
        return $config;
    }
}
