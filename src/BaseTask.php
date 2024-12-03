<?php

namespace WebmanTech\CrontabTask;

use Throwable;
use WebmanTech\CrontabTask\Exceptions\TaskException;
use WebmanTech\CrontabTask\Exceptions\TaskExceptionInterface;
use WebmanTech\CrontabTask\Traits\LogTrait;
use WebmanTech\CrontabTask\Traits\TaskAutoFreeMemoryTrait;
use WebmanTech\CrontabTask\Traits\TaskEventTrait;

abstract class BaseTask
{
    use LogTrait;
    use TaskEventTrait;
    use TaskAutoFreeMemoryTrait;

    final public function __construct()
    {
        $this->initEvents();
    }

    /**
     * 定时任务的执行入口
     * @return void
     */
    public static function taskExec()
    {
        $self = new static();

        try {
            $self->log('start');
            $self->fireBeforeEvent();
            $self->handle();
        } catch (Throwable $e) {
            if ($e instanceof TaskExceptionInterface) {
                $self->log('TaskException:' . $e->getMessage() . $e->getDataAsString(), 'warning');
                return;
            }

            $self->log($e, 'error');
            return;
        } finally {
            $self->fireAfterEvent();

            if ($self->isAutoFreeMemory()) {
                $self->freeMemory();
            }

            $self->log('end');
        }
    }

    /**
     * 真实业务
     * @return void
     * @throws TaskException
     * @throws Throwable
     */
    abstract public function handle();
}
