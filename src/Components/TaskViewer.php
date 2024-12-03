<?php

namespace WebmanTech\CrontabTask\Components;

use support\Container;
use WebmanTech\CrontabTask\TaskProcess;

final class TaskViewer
{
    private array $processes;

    public function __construct(array $processes = [])
    {
        $this->processes = $processes;
    }

    public function getData(): array
    {
        $data = [];

        foreach ($this->processes as $name => $item) {
            $handler = $item['handler'];
            if (!is_a($handler, TaskProcess::class, true)) {
                continue;
            }
            /** @var TaskProcess $process */
            $process = Container::make($handler, $item['constructor'] ?? []);
            foreach ($process->getTasks() as [$cron, $task]) {
                $data[] = [
                    'process_name' => $name,
                    'cron' => $cron,
                    'task_class' => $task,
                ];
            }
        }

        return $data;
    }
}