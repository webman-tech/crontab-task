<?php

namespace Tests\Fixtures;

use WebmanTech\CrontabTask\BaseTask;

class SimpleTask extends BaseTask
{
    public static $counter = 0;
    public static $markArr = [];

    /**
     * @return void
     */
    public function handle()
    {
        static::$counter++;
    }

    public function mark(string $string)
    {
        static::$markArr[] = $string;
    }
}