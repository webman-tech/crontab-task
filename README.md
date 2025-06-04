# webman-tech/crontab-task

本项目是从 [webman-tech/components-monorepo](https://github.com/orgs/webman-tech/components-monorepo) 自动 split 出来的，请勿直接修改

> 简介

让 `workerman/crontab` 在 webman 中更加便捷的使用

## 安装

```bash
composer require webman-tech/crontab-task
```

## 特点

- 基于 [workerman/crontab](https://www.workerman.net/doc/webman/components/crontab.html)
- 支持单进程单个定时任务，和单进程多定时任务快捷配置（解决webman默认的单进程下起多个定时任务阻塞的问题），见配置 [process.php](copy/config/plugin/process.php)
- 定时任务常规 log 支持（start/end/exception），见配置 [app.php](copy/config/plugin/app.php)

## 使用

1. 创建 Task

```php
<?php

namespace app\crontab\tasks;

use WebmanTech\CrontabTask\BaseTask;

class SampleTask extends BaseTask 
{
    /**
     * @inheritDoc
     */
    public function handle()
    {   
        // 实际业务
        echo date('Y-m-d H:i:s') . PHP_EOL;
    }
}
```

2. 添加到 process

配置：`config/plugin/webman-tech/crontab-task/process.php`

```php
<?php

return (new Schedule())
    // 添加单个定时任务，独立进程
    ->addTask('task1', '*/1 * * * * *', \WebmanTech\CrontabTask\Tasks\SampleTask::class)
    // 添加多个定时任务，在同个进程中（注意会存在阻塞）
    ->addTasks('task2', [
        ['*/1 * * * * *', \WebmanTech\CrontabTask\Tasks\SampleTask::class],
        ['*/1 * * * * *', \WebmanTech\CrontabTask\Tasks\SampleTask::class],
    ])
    ->buildProcesses();
```

## 命令

`php webman crontab-task:list`: 列出所有 crontab 定时任务

`php webman make:crontab-task [name]`: 创建 crontab task