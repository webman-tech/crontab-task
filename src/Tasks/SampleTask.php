<?php

namespace WebmanTech\CrontabTask\Tasks;

use WebmanTech\CrontabTask\BaseTask;

class SampleTask extends BaseTask
{
    /**
     * @inheritDoc
     */
    public function handle()
    {
        echo date('Y-m-d H:i:s') . PHP_EOL;
    }
}