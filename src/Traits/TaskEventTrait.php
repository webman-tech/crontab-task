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

    /**
     * @return void
     */
    protected function initEvents()
    {
        // 旧版本单独事件的模式，暂时兼容
        if ($this->eventBeforeExec === null) {
            /** @phpstan-ignore-next-line */
            $this->eventBeforeExec = $this->disableGlobalBeforeEvent ? null : ConfigHelper::get('app.event.before_exec');
        }
        if ($this->eventAfterExec === null) {
            /** @phpstan-ignore-next-line */
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

    /**
     * @return void
     */
    protected function addBeforeEvent(Closure $closure)
    {
        $this->eventsBeforeExec[] = $closure;
    }

    /**
     * @return void
     */
    protected function addAfterEvent(Closure $closure)
    {
        $this->eventsAfterExec[] = $closure;
    }

    /**
     * @return void
     */
    protected function fireBeforeEvent()
    {
        foreach ($this->eventsBeforeExec as $event) {
            $event($this);
        }
    }

    /**
     * @return void
     */
    protected function fireAfterEvent()
    {
        foreach ($this->eventsAfterExec as $event) {
            $event($this);
        }
    }
}
