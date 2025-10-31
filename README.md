# webman-tech/crontab-task

本项目是从 [webman-tech/components-monorepo](https://github.com/orgs/webman-tech/components-monorepo) 自动 split 出来的，请勿直接修改

## 简介

webman 定时任务管理插件，基于 [workerman/crontab](https://www.workerman.net/doc/webman/components/crontab.html) 实现，提供更加便捷的定时任务管理方式。

该插件解决了 webman 默认定时任务配置的一些问题：

1. 默认的单进程下起多个定时任务会存在阻塞问题
2. 缺乏统一的日志记录机制
3. 定时任务的管理不够灵活

## 功能特性

- **灵活的进程管理**：支持单进程单个定时任务和单进程多个定时任务的配置
- **完善的日志支持**：提供定时任务开始、结束、异常等常规日志记录
- **事件机制**：支持任务执行前后的事件处理
- **内存管理**：支持任务执行后自动释放内存
- **命令行工具**：提供查看和创建定时任务的命令
- **异常处理**：完善的异常处理机制，防止任务异常影响整个进程

## 安装

```bash
composer require webman-tech/crontab-task
```

## 快速开始

### 1. 创建定时任务

创建一个继承 [BaseTask](src/BaseTask.php) 的定时任务类：

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
        // 实际业务逻辑
        echo date('Y-m-d H:i:s') . PHP_EOL;
    }
}
```

### 2. 配置定时任务进程

在 `config/plugin/webman-tech/crontab-task/process.php` 中配置：

```php
<?php

use WebmanTech\CrontabTask\Schedule;

return (new Schedule())
    // 添加单个定时任务，独立进程
    ->addTask('task1', '*/1 * * * * *', \app\crontab\tasks\SampleTask::class)
    // 添加多个定时任务，在同个进程中（注意会存在阻塞）
    ->addTasks('task2', [
        ['*/1 * * * * *', \app\crontab\tasks\SampleTask::class],
        ['*/2 * * * * *', \app\crontab\tasks\SampleTask::class],
    ])
    ->buildProcesses();
```

### 3. 启动服务

```bash
php start.php start
```

## 核心组件

### BaseTask 基础任务类

[BaseTask](src/BaseTask.php) 是所有定时任务的基类，提供了任务执行的完整生命周期管理：

- `taskExec()`: 任务执行入口，处理异常和日志记录
- `handle()`: 抽象方法，由子类实现具体业务逻辑
- 集成日志、事件、内存管理等特性

### Schedule 调度器

[Schedule](src/Schedule.php) 用于配置和管理定时任务进程：

- `addTask()`: 添加单个定时任务（独立进程）
- `addTasks()`: 添加多个定时任务（同一进程）
- `buildProcesses()`: 构建进程配置数组

### TaskProcess 任务进程

[TaskProcess](src/TaskProcess.php) 是实际的任务进程类：

- 验证任务配置的正确性
- 在进程启动时注册定时任务
- 管理任务的执行

## 特性详解

### 日志记录

通过 [LogTrait](src/Traits/LogTrait.php) 提供日志记录功能：

```php
class MyTask extends BaseTask
{
    public function handle()
    {
        $this->log('这是一条信息日志');
        $this->log('这是一条警告日志', 'warning');
        $this->log('这是一条错误日志', 'error');
    }
}
```

### 事件机制

通过 [TaskEventTrait](src/Traits/TaskEventTrait.php) 提供任务执行前后的事件处理：

```php
class MyTask extends BaseTask
{
    protected function initEvents()
    {
        parent::initEvents();
        
        $this->addBeforeEvent(function() {
            // 任务执行前的操作
        });
        
        $this->addAfterEvent(function() {
            // 任务执行后的操作
        });
    }
}
```

### 内存管理

通过 [TaskAutoFreeMemoryTrait](src/Traits/TaskAutoFreeMemoryTrait.php) 提供自动内存释放功能：

```php
class MyTask extends BaseTask
{
    // 启用自动内存释放（也可通过配置文件全局启用）
    protected ?bool $isAutoFreeMemory = true;
}
```

### 异常处理

定时任务中的异常分为两类：

1. [TaskException](src/Exceptions/TaskException.php): 业务异常，记录为 warning 级别日志
2. 其他异常: 系统异常，记录为 error 级别日志

```php
class MyTask extends BaseTask
{
    public function handle()
    {
        if ($someCondition) {
            // 业务异常
            throw new \WebmanTech\CrontabTask\Exceptions\TaskException('业务异常信息');
        }
        
        if ($otherCondition) {
            // 系统异常
            throw new \Exception('系统异常信息');
        }
    }
}
```

## 命令行工具

### 查看定时任务列表

```bash
php webman crontab-task:list
```

### 创建定时任务

```bash
php webman make:crontab-task MyTask
```

## 配置说明

### 进程配置

在 `config/plugin/webman-tech/crontab-task/process.php` 中配置：

```php
return (new Schedule([
    'process_name_prefix' => 'cron_task_', // 进程名称前缀
]))
    ->addTask('my_task', '*/5 * * * * *', \app\crontab\tasks\MyTask::class)
    ->buildProcesses();
```

### 应用配置

在 `config/plugin/webman-tech/crontab-task/app.php` 中配置：

```php
return [
    'log' => [
        'channel' => 'task',     // 日志通道
        'type' => 'info',        // 默认日志级别
        'log_class' => true,     // 是否记录类名
    ],
    'event' => [
        'before_exec' => null,   // 全局任务执行前事件
        'after_exec' => null,    // 全局任务执行后事件
    ],
    'auto_free_memory' => false, // 是否自动释放内存
];
```

## 最佳实践

1. **合理规划进程**：根据任务的重要性和执行频率决定使用独立进程还是共享进程
2. **完善的日志记录**：记录关键业务信息和异常情况，便于问题排查
3. **异常处理**：区分业务异常和系统异常，采用不同的处理方式
4. **内存管理**：对于长时间运行或内存消耗较大的任务，启用自动内存释放
5. **监控告警**：对重要任务的执行情况进行监控，设置告警机制
6. **任务幂等性**：确保任务可以重复执行而不产生副作用