<?php

use Tests\Fixtures\EmptyTask;
use WebmanTech\CrontabTask\Schedule;
use WebmanTech\CrontabTask\TaskProcess;

test('常规配置', function () {
    $schedule = new Schedule();

    $schedule->addTask('task1', '*/1 * * * *', EmptyTask::class);
    $schedule->addTask('task2', '*/1 * * * *', EmptyTask::class);
    $schedule->addTasks('task3', [
        ['*/1 * * * *',EmptyTask::class],
        ['*/1 * * * *',EmptyTask::class],
    ]);

    $processes = $schedule->buildProcesses();

    expect($processes)->toBe([
        'cron_task_task1' => [
            'handler' => TaskProcess::class,
            'constructor' => [
                'tasks' => [
                    ['*/1 * * * *', EmptyTask::class],
                ],
            ],
        ],
        'cron_task_task2' => [
            'handler' => TaskProcess::class,
            'constructor' => [
                'tasks' => [
                    ['*/1 * * * *', EmptyTask::class],
                ],
            ],
        ],
        'cron_task_task3' => [
            'handler' => TaskProcess::class,
            'constructor' => [
                'tasks' => [
                    ['*/1 * * * *', EmptyTask::class],
                    ['*/1 * * * *', EmptyTask::class],
                ],
            ],
        ],
    ]);
});

test('修改进程前缀', function () {
    $schedule = new Schedule([
        'process_name_prefix' => 'abc_',
    ]);

    $schedule->addTask('task1', '*/1 * * * *', EmptyTask::class);

    $processes = $schedule->buildProcesses();
    expect(isset($processes['abc_task1']))->toBeTrue();
});

test('addTask cron 错误', function () {
    $schedule = new Schedule();
    expect(fn() => $schedule->addTask('abc', '123', EmptyTask::class))->toThrow(InvalidArgumentException::class);
});

test('addTask task 错误', function () {
    $schedule = new Schedule();
    expect(fn() => $schedule->addTask('abc', '123', Schedule::class))->toThrow(InvalidArgumentException::class);
});

test('addTask name 重复', function () {
    $schedule = new Schedule();
    $schedule->addTask('task1', '*/1 * * * *', EmptyTask::class);
    expect(fn() => $schedule->addTask('task1', '*/1 * * * *', EmptyTask::class))->toThrow(InvalidArgumentException::class);
});
