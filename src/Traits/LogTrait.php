<?php

namespace WebmanTech\CrontabTask\Traits;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use support\Log;
use WebmanTech\CrontabTask\Helper\ConfigHelper;

trait LogTrait
{
    /**
     * 日志 channel
     * @var string|null
     */
    protected ?string $logChannel = null;
    /**
     * 默认的日志级别
     * @var string|null
     */
    protected ?string $logType = null;
    /**
     * 是否记录 class
     * 如果 logChannel 是独立的，可以选择关闭
     * @var bool|null
     */
    protected ?bool $logClass = null;
    /**
     * @var ?LoggerInterface
     */
    protected ?LoggerInterface $logger = null;

    /**
     * log
     * @param string $msg
     * @param string|null $type
     * @return void
     */
    protected function log(string $msg, string $type = 'info')
    {
        if ($this->logger === null) {
            $config = array_merge(
                ['channel' => null, 'type' => 'info', 'log_class' => true],
                array_filter(ConfigHelper::get('app.log', []), fn($value) => $value !== null),
                array_filter(['channel' => $this->logChannel, 'type' => $this->logType, 'log_class' => $this->logClass], fn($value) => $value !== null),
            );
            if (!$config['channel']) {
                $this->logger = new NullLogger();
            } else {
                $this->logChannel = $config['channel'];
                $this->logType = $config['type'];
                $this->logClass = $config['log_class'];
                $this->logger = Log::channel($config['channel']);
            }
        }

        $type = $type ?? $this->logType;
        if ($this->logClass) {
            $msg = static::class . ':' . $msg;
        }
        $this->logger->{$type}($msg);
    }
}
