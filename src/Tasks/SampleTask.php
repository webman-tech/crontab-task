<?php

namespace WebmanTech\CrontabTask\Tasks;

use WebmanTech\CrontabTask\BaseTask;
use WebmanTech\CrontabTask\Exceptions\TaskException;

class SampleTask extends BaseTask
{
    /**
     * @inheritDoc
     */
    public function handle()
    {
        echo date('Y-m-d H:i:s') . ' Test task' . PHP_EOL;

        if (random_int(1, 100) > 50) {
            throw new TaskException('该异常会记录 warning 日志');
        }
        if (random_int(1, 100) > 50) {
            throw new \Exception('其他异常会记录 error 日志，不会抛出异常到 console');
        }
        $this->log('这个可以记录 info 日志');
        // 正常结束无需返回
    }
}