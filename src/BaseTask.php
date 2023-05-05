<?php

namespace WebmanTech\CrontabTask;

use Psr\Log\LoggerInterface;
use support\Log;
use Throwable;
use WebmanTech\CrontabTask\Exceptions\TaskException;
use Workerman\Crontab\Crontab;

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
     * crontab 的规则
     * @link https://www.workerman.net/doc/webman/components/crontab.html#%E8%AF%B4%E6%98%8E
     * @return string
     */
    abstract protected function getCrontabRule(): string;

    public function getCrontab(): string
    {
        return $this->getCrontabRule();
    }

    public function onWorkerStart()
    {
        new Crontab($this->getCrontabRule(), [static::class, 'consume']);
    }

    /**
     * 定时任务的入口
     * @return void
     */
    public static function consume()
    {
        $self = new static();
        $self->log('start');

        try {
            $self->handle();
        } catch (TaskException $e) {
            $self->log('TaskException:' . $e->getMessage() . $e->getDataAsString(), 'warning');
            return;
        } catch (Throwable $e) {
            $self->log($e, 'error');
            return;
        }

        $self->log('end');
    }

    /**
     * @return void.
     * @throws TaskException
     */
    abstract protected function handle();

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
