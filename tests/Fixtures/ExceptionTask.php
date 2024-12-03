<?php

namespace Tests\Fixtures;

use WebmanTech\CrontabTask\BaseTask;
use WebmanTech\CrontabTask\Exceptions\TaskException;

class ExceptionTask extends BaseTask
{
    public static $useTaskException = true;

    /**
     * @return void
     */
    public function handle()
    {
        if (static::$useTaskException) {
            throw new TaskException('Task Exception');
        } else {
            throw new \Exception('Another Exception');
        }
    }
}