<?php

namespace WebmanTech\CrontabTask\Commands;

use support\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebmanTech\CrontabTask\TaskProcess;

class CrontabTaskListCommand extends Command
{
    protected static $defaultName = 'crontab-task:list';
    protected static $defaultDescription = '展示 cron task 进程的定时任务名和执行时间';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $headers = ['process_name', 'cron', 'task_class'];

        $rows = [];
        $processes = config('plugin.webman-tech.crontab-task.process', []);
        foreach ($processes as $name => $item) {
            $handler = $item['handler'];
            if (!is_a($handler, TaskProcess::class, true)) {
                continue;
            }
            /** @var TaskProcess $process */
            $process = Container::make($handler, $item['constructor'] ?? []);
            foreach ($process->getTasks() as [$cron, $task]) {
                $rows[] = [$name, $cron, $task];
            }
        }

        $table = new Table($output);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->render();

        return self::SUCCESS;
    }

}
