<?php

namespace WebmanTech\CrontabTask\Components;

use WebmanTech\CommonUtils\Container;
use WebmanTech\CrontabTask\TaskProcess;

final class TaskViewer
{
    private array $config = [
        'with_next_due' => true,
        'next_due_limit' => 3,
    ];

    public function __construct(private readonly array $processes = [], array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    public function getData(): array
    {
        $data = [];

        foreach ($this->processes as $name => $item) {
            $handler = $item['handler'];
            if (!is_string($handler) || !is_a($handler, TaskProcess::class, true)) {
                continue;
            }
            $cronParser = new CronParser();
            /** @var TaskProcess $process */
            $process = Container::getCurrent()->make($handler, $item['constructor'] ?? []);
            foreach ($process->getTasks() as [$cron, $task]) {
                $item = [
                    'process_name' => $name,
                    'cron' => $cron,
                    'task_class' => $task,
                ];

                if ($this->config['with_next_due']) {
                    $times = $cronParser->getNextDueTimes($cron, null, $this->config['next_due_limit']);
                    $item['next_due_times'] = array_map(fn($time) => date('Y-m-d H:i:s', $time), $times);
                }

                $data[] = $item;
            }
        }

        return $data;
    }
}
