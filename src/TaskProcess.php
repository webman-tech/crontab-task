<?php

namespace WebmanTech\CrontabTask;

use InvalidArgumentException;
use Workerman\Crontab\Crontab;
use Workerman\Crontab\Parser;

final class TaskProcess
{
    /**
     * @var array [[$cron, $task]]
     */
    private array $tasks;

    public function __construct(array $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * 检查 tasks 参数是否正确
     * @param array $tasks
     * @return void
     */
    public static function checkTasks(array $tasks): void
    {
        $parser = new Parser();
        foreach ($tasks as [$cron, $task]) {
            if (!$parser->isValid($cron)) {
                throw new InvalidArgumentException('cron invalid: ' . $cron);
            }
            if (!is_string($task)) {
                throw new InvalidArgumentException('task must be string');
            }
            if (!is_a($task, BaseTask::class, true)) {
                throw new InvalidArgumentException('task must be instance of ' . BaseTask::class);
            }
        }
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function onWorkerStart(): void
    {
        foreach ($this->tasks as [$cron, $task]) {
            /** @var BaseTask $task */
            new Crontab($cron, [$task, 'taskExec']);
        }
    }
}
