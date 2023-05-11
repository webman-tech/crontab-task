<?php

namespace WebmanTech\CrontabTask;

use Psr\Log\LoggerInterface;
use support\Log;
use Throwable;
use WebmanTech\CrontabTask\Exceptions\TaskException;
use WebmanTech\CrontabTask\Exceptions\TaskExceptionInterface;

abstract class BaseTask
{
    /**
     * 日志 channel
     * @var string|null
     */
    protected $logChannel = null;
    /**
     * 默认的日志级别
     * @var string|null
     */
    protected $logType = null;
    /**
     * 是否记录 class
     * 如果 logChannel 是独立的，可以选择关闭
     * @var bool|null
     */
    protected $logClass = null;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    final public function __construct()
    {
        if ($this->logChannel === null) {
            $this->logChannel = config('plugin.webman-tech.crontab-task.app.log.channel', 'default');
        }
        if ($this->logType === null) {
            $this->logType = config('plugin.webman-tech.crontab-task.app.log.type', 'debug');
        }
        if ($this->logClass === null) {
            $this->logClass = config('plugin.webman-tech.crontab-task.app.log.log_class', true);
        }
        $this->logger = Log::channel($this->logChannel);
    }

    /**
     * 定时任务的执行入口
     * @return void
     */
    public static function taskExec()
    {
        $self = new static();
        $self->log('start');

        try {
            $self->handle();
        } catch (Throwable $e) {
            if ($e instanceof TaskExceptionInterface) {
                $self->log('TaskException:' . $e->getMessage() . $e->getDataAsString(), 'warning');
                return;
            }

            $self->log($e, 'error');
            return;
        }

        $self->log('end');
    }

    /**
     * 真实业务
     * @return void
     * @throws TaskException
     * @throws Throwable
     */
    abstract public function handle();

    /**
     * @param string $msg
     * @param string|null $type
     * @return void
     */
    protected function log(string $msg, ?string $type = null): void
    {
        $type = $type ?? $this->logType;
        if ($this->logClass) {
            $msg = static::class . ':' . $msg;
        }
        $this->logger->{$type}($msg);
    }
}
