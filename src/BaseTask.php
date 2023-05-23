<?php

namespace WebmanTech\CrontabTask;

use Closure;
use Throwable;
use WebmanTech\CrontabTask\Exceptions\TaskException;
use WebmanTech\CrontabTask\Exceptions\TaskExceptionInterface;
use WebmanTech\CrontabTask\Traits\LogTrait;

abstract class BaseTask
{
    use LogTrait;

    /**
     * @var null|Closure
     */
    protected ?Closure $eventBeforeExec = null;
    /**
     * @var null|Closure
     */
    protected ?Closure $eventAfterExec = null;

    final public function __construct()
    {
        if ($this->eventBeforeExec === null) {
            $this->eventBeforeExec = config('plugin.webman-tech.crontab-task.app.event.before_exec');
        }
        if ($this->eventAfterExec === null) {
            $this->eventAfterExec = config('plugin.webman-tech.crontab-task.app.event.after_exec');
        }
    }

    /**
     * 定时任务的执行入口
     * @return void
     */
    public static function taskExec()
    {
        $self = new static();
        $self->log('start');
        if ($self->eventBeforeExec instanceof Closure) {
            call_user_func($self->eventBeforeExec, $self);
        }

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

        if ($self->eventBeforeExec instanceof Closure) {
            call_user_func($self->eventAfterExec, $self);
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
}
