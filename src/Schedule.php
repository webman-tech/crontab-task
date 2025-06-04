<?php

namespace WebmanTech\CrontabTask;

use InvalidArgumentException;

class Schedule
{
    private array $config = [
        'process_name_prefix' => 'cron_task_',
    ];
    private array $processes = [];

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 添加单个定时任务，独立进程
     * @param string $name
     * @param string $cron
     * @param string $task
     * @return $this
     */
    public function addTask(string $name, string $cron, string $task): self
    {
        return $this->addTasks($name, [
            [$cron, $task]
        ]);
    }

    /**
     * 添加多个定时任务，在同个进程中（注意会存在阻塞）
     * @param string $name
     * @param array $tasks
     * @return $this
     */
    public function addTasks(string $name, array $tasks): self
    {
        $name = $this->config['process_name_prefix'] . $name;
        if (isset($this->processes[$name])) {
            throw new InvalidArgumentException('Task already exists: ' . $name);
        }

        TaskProcess::checkTasks($tasks);

        $this->processes[$name] = [
            'handler' => TaskProcess::class,
            'constructor' => [
                'tasks' => $tasks,
            ],
        ];
        return $this;
    }

    /**
     * @return array
     */
    public function buildProcesses(): array
    {
        return $this->processes;
    }
}