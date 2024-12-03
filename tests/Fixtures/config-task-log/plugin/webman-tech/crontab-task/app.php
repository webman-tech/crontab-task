<?php

return [
    'enable' => true,
    'log' => [
        /**
         * @see \WebmanTech\CrontabTask\Traits\LogTrait::log()
         */
        'channel' => 'task', // 为 null 时不记录日志
    ],
];
