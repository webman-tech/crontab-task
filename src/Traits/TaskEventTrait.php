<?php

namespace WebmanTech\CrontabTask\Traits;

use Closure;
use WebmanTech\CrontabTask\Helper\ConfigHelper;

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

    protected function initEvents(): void
    {
        // 旧版本单独事件的模式，暂时兼容
        if ($this->eventBeforeExec === null) {
            $this->eventBeforeExec = $this->disableGlobalBeforeEvent ? null : ConfigHelper::get('app.event.before_exec');
        }
        if ($this->eventAfterExec === null) {
            $this->eventAfterExec = $this->disableGlobalAfterEvent ? null : ConfigHelper::get('app.event.after_exec');
        }
        // 支持多事件
        if ($this->eventBeforeExec !== null) {
            $this->addBeforeEvent($this->eventBeforeExec);
        }
        if ($this->eventAfterExec !== null) {
            $this->addAfterEvent($this->eventAfterExec);
        }
    }

    protected function addBeforeEvent(Closure $closure): void
    {
        $this->eventsBeforeExec[] = $closure;
    }

    protected function addAfterEvent(Closure $closure): void
    {
        $this->eventsAfterExec[] = $closure;
    }

    protected function fireBeforeEvent(): void
    {
        foreach ($this->eventsBeforeExec as $event) {
            $event($this);
        }
    }

    protected function fireAfterEvent(): void
    {
        foreach ($this->eventsAfterExec as $event) {
            $event($this);
        }
    }
}
