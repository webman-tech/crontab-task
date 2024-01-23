<?php

namespace WebmanTech\CrontabTask\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class MakeTaskCommand extends Command
{
    protected static $defaultName = 'make:crontab-task';
    protected static $defaultDescription = 'Make crontab task';

    protected ?string $savePath = null;

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Task name');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $name = $input->getArgument('name');
        $savePath = $this->savePath ?: config('plugin.webman-tech.crontab-task.app.command_make.save_path', 'crontab/tasks');

        $name = str_replace('\\', '/', $name);
        // 将 name 按照 / 分隔，并取最后一个
        $paths = explode('/', $name);
        $name = ucfirst(array_pop($paths));
        if (!str_ends_with($name, 'Task')) {
            $name .= 'Task';
        }
        if ($paths) {
            $savePath .= '/' . implode('/', $paths);
        }
        $namespace = 'app\\' . str_replace('/', '\\', $savePath);
        $file = app_path() . '/' . $savePath . '/' . $name . '.php';

        $output->writeln("Make Crontab Task {$namespace}\\{$name}");

        $this->createFile($name, $namespace, $file);

        return self::SUCCESS;
    }

    /**
     * @param $name
     * @param $namespace
     * @param $file
     * @return void
     */
    protected function createFile($name, $namespace, $file)
    {
        $path = pathinfo($file, PATHINFO_DIRNAME);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $content = <<<EOF
<?php

namespace $namespace;

use WebmanTech\CrontabTask\BaseTask;

class $name extends BaseTask
{
    /**
     * @inheritDoc
     */
    public function handle()
    {
        // do something
    }
}

EOF;
        file_put_contents($file, $content);
    }
}
