<?php

use Tests\Fixtures\EmptyTask;
use Tests\Fixtures\SimpleTask;
use WebmanTech\CrontabTask\Components\TaskViewer;
use WebmanTech\CrontabTask\Schedule;

test('getData', function () {
    $schedule = new Schedule();

    $schedule->addTask('task1', '*/1 * * * *', EmptyTask::class);
    $schedule->addTask('task2', '*/1 * * * *', EmptyTask::class);
    $schedule->addTasks('task3', [
        ['*/1 * * * *', EmptyTask::class],
        ['*/1 * * * *', SimpleTask::class],
    ]);

    $processes = $schedule->buildProcesses();

    $viewer = new TaskViewer($processes);
    expect($viewer->getData())->toBe([
        ['process_name' => 'cron_task_task1', 'cron' => '*/1 * * * *', 'task_class' => EmptyTask::class],
        ['process_name' => 'cron_task_task2', 'cron' => '*/1 * * * *', 'task_class' => EmptyTask::class],
        ['process_name' => 'cron_task_task3', 'cron' => '*/1 * * * *', 'task_class' => EmptyTask::class],
        ['process_name' => 'cron_task_task3', 'cron' => '*/1 * * * *', 'task_class' => SimpleTask::class],
    ]);
});