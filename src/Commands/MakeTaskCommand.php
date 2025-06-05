<?php

namespace WebmanTech\CrontabTask\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebmanTech\CrontabTask\Helper\ConfigHelper;

#[AsCommand(name: 'make:crontab-task', description: 'Make crontab task')]
class MakeTaskCommand extends Command
{
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
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $savePath = $this->savePath ?: ConfigHelper::get('app.command_make.save_path', 'crontab/tasks');

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

    protected function createFile(string $name, string $namespace, string $file): void
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
