<?php

namespace WebmanTech\CrontabTask\Traits;

use WebmanTech\CrontabTask\Helper\ConfigHelper;

/**
 * Task 任务自动释放内存
 * 由于定时任务频率一般都不会太快，因此执行完释放下内存是个不错的选择
 */
trait TaskAutoFreeMemoryTrait
{
    protected ?bool $isAutoFreeMemory = null;

    protected function isAutoFreeMemory(): bool
    {
        if ($this->isAutoFreeMemory === null) {
            $this->isAutoFreeMemory = ConfigHelper::get('app.auto_free_memory', false);
        }
        return $this->isAutoFreeMemory;
    }

    protected function freeMemory()
    {
        gc_mem_caches();
    }
}
