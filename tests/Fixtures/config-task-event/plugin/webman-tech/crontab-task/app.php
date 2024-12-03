<?php

use WebmanTech\CrontabTask\BaseTask;

return [
    'enable' => true,
    'log' => [
        /**
         * @see \WebmanTech\CrontabTask\Traits\LogTrait::log()
         */
        'channel' => null, // 为 null 时不记录日志
    ],
    'event' => [
        'before_exec' => function (BaseTask $task) {
            if ($task instanceof \Tests\Fixtures\SimpleTask) {
                $task->mark('before_exec');
            }
        },
        'after_exec' => function (BaseTask $task) {
            if ($task instanceof \Tests\Fixtures\SimpleTask) {
                $task->mark('after_exec');
            }
        },
    ],
];
