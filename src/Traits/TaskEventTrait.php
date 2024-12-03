<?php

namespace WebmanTech\CrontabTask\Traits;

use Closure;

trait TaskEventTrait
{
    /**
     * @deprecated
     * @var null|Closure
     */
    protected ?Closure $eventBeforeExec = null;
    /**
     * @deprecated
     * @var null|Closure
     */
    protected ?Closure $eventAfterExec = null;

    protected bool $disableGlobalBeforeEvent = false;
    protected bool $disableGlobalAfterEvent = false;
    /**
     * @var Closure[]
     */
    protected array $eventsBeforeExec = [];
    /**
     * @var Closure[]
     */
    protected array $eventsAfterExec = [];

    protected function initEvents()
    {
        // 旧版本单独事件的模式，暂时兼容
        if ($this->eventBeforeExec === null) {
            $this->eventBeforeExec = $this->disableGlobalBeforeEvent ? null : config('plugin.webman-tech.crontab-task.app.event.before_exec');
        }
        if ($this->eventAfterExec === null) {
            $this->eventAfterExec = $this->disableGlobalAfterEvent ? null : config('plugin.webman-tech.crontab-task.app.event.after_exec');
        }
        // 支持多事件
        if ($this->eventBeforeExec !== null) {
            $this->addBeforeEvent($this->eventBeforeExec);
        }
        if ($this->eventAfterExec !== null) {
            $this->addAfterEvent($this->eventAfterExec);
        }
    }

    protected function addBeforeEvent(Closure $closure)
    {
        $this->eventsBeforeExec[] = $closure;
    }

    protected function addAfterEvent(Closure $closure)
    {
        $this->eventsAfterExec[] = $closure;
    }

    protected function fireBeforeEvent()
    {
        foreach ($this->eventsBeforeExec as $event) {
            $event($this);
        }
    }

    protected function fireAfterEvent()
    {
        foreach ($this->eventsAfterExec as $event) {
            $event($this);
        }
    }
}