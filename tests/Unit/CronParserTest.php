<?php

use WebmanTech\CrontabTask\Components\CronParser;

test('getNextDueTimes 分钟级别', function () {
    $parser = new CronParser();

    $time = strtotime(date('Y-m-d H:i:0'));
    expect($parser->getNextDueTimes('*/1 * * * *', $time, 6))->toBe([
        $time + 60,
        $time + 120,
        $time + 180,
        $time + 240,
        $time + 300,
        $time + 360,
    ])
        ->and($parser->getNextDueTime('*/1 * * * *', $time))->toBe($time + 60);
});

test('getNextDueTimes 秒级别', function () {
    $parser = new CronParser();

    $time = strtotime(date('Y-m-d H:i:0'));
    expect($parser->getNextDueTimes('*/1 */1 * * * *', $time, 3))->toBe([
        $time + 60,
        $time + 61,
        $time + 62,
    ]);
});
