<?php

namespace WebmanTech\CrontabTask\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebmanTech\CrontabTask\BaseTask;

#[AsCommand(name: 'crontab-task:exec', description: '执行一个 task')]
class CrontabTaskExecCommand extends Command
{
    protected function configure()
    {
        $this->addArgument('task', null, 'task className');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $task = (string)$input->getArgument('task');
        if (!is_a($task, BaseTask::class, true)) {
            throw new \RuntimeException('task must be a subclass of ' . BaseTask::class);
        }

        $task::taskExec();

        return self::SUCCESS;
    }

}
