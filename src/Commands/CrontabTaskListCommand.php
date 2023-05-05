<?php

namespace WebmanTech\CrontabTask\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebmanTech\CrontabTask\BaseTask;

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
        $headers = ['name', 'class', 'interval'];

        $rows = [];
        $processes = config('plugin.webman-tech.crontab-task.process', []);
        foreach ($processes as $name => $item) {
            $handler = $item['handler'];
            if (!is_a($handler, BaseTask::class, true)) {
                continue;
            }
            /** @var BaseTask $process */
            $process = new $handler();
            $rows[] = [$name, $handler, $process->getCrontab()];
        }

        $table = new Table($output);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->render();

        return self::SUCCESS;
    }

}
