<?php

namespace WebmanTech\CrontabTask\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebmanTech\CrontabTask\Components\TaskViewer;

class CrontabTaskListCommand extends Command
{
    protected static $defaultName = 'crontab-task:list';
    protected static $defaultDescription = '展示 cron task 进程的定时任务名和执行时间';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $taskViewer = new TaskViewer(
            config('plugin.webman-tech.crontab-task.process', []),
            config('plugin.webman-tech.crontab-task.app.task_viewer_config', [])
        );
        $data = $taskViewer->getData();

        if (!$data) {
            $output->writeln('No task found');
            return self::SUCCESS;
        }

        $table = new Table($output);
        $table->setHeaders(array_keys($data[0]));
        $table->setRows(array_map(function (array $item) {
            return array_map(function ($value) {
                return is_array($value) ? json_encode($value) : $value;
            }, $item);
        }, $data));
        $table->render();

        return self::SUCCESS;
    }

}
