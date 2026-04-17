---
name: webman-tech-crontab-task-best-practices
description: webman-tech/crontab-task 最佳实践。使用场景：用户配置定时任务时，给出明确的推荐写法。
---

# webman-tech/crontab-task 最佳实践

## 核心原则

1. **耗时任务独立进程**：用 `addTask` 给每个耗时任务独立进程，避免阻塞
2. **轻量任务可合并**：多个快速任务用 `addTasks` 放同一进程，节省资源
3. **业务异常用 TaskException**：可预期的业务异常抛 `TaskException`，不会记录 error 日志

> **注意**：此包基于 workerman/crontab，**只适用于单节点部署**。多节点部署时每个节点都会独立执行任务，无法做到分布式调度（如任务去重、节点选主等）。分布式场景请使用 xxl-job 等专业调度服务。

---

## 配置定时任务

```php
// config/plugin/webman-tech/crontab-task/process.php
use WebmanTech\CrontabTask\Schedule;

return (new Schedule())
    // 单个任务，独立进程（推荐耗时任务）
    ->addTask('sync-orders', '0 * * * * *', SyncOrderTask::class)
    ->addTask('send-notify', '*/5 * * * * *', SendNotifyTask::class)

    // 多个轻量任务，共用一个进程（注意：任务间会阻塞）
    ->addTasks('light-tasks', [
        ['0 0 * * * *', CleanCacheTask::class],
        ['0 0 * * * *', UpdateStatsTask::class],
    ])
    ->buildProcesses();
```

Cron 表达式支持 6 位（秒级）：`秒 分 时 日 月 周`。

---

## 编写任务类

```php
use WebmanTech\CrontabTask\BaseTask;
use WebmanTech\CrontabTask\Exceptions\TaskException;

class SyncOrderTask extends BaseTask
{
    // 指定日志 channel（覆盖全局配置）
    protected ?string $logChannel = 'crontab';

    public function handle(): void
    {
        $orders = Order::where('status', 'pending')->get();

        foreach ($orders as $order) {
            try {
                OrderService::sync($order);
            } catch (\Throwable $e) {
                // 业务异常：抛 TaskException，记录 warning，不中断整个任务
                throw new TaskException('同步失败: ' . $e->getMessage(), ['order_id' => $order->id]);
            }
        }
    }
}
```

`handle()` 执行流程：
1. 记录 `start` 日志
2. 触发 `before` 事件
3. 执行 `handle()`
4. 触发 `after` 事件（无论成功失败）
5. 自动释放内存
6. 记录 `end` 日志

---

## 异常处理

| 异常类型 | 日志级别 | 是否继续执行 |
|---------|---------|------------|
| `TaskException` | `warning` | 中断当前任务，记录 warning |
| 其他 `Throwable` | `error` | 中断当前任务，记录 error |

```php
// 可预期的业务异常 → TaskException
throw new TaskException('数据不存在', ['id' => $id]);

// 不可预期的异常 → 直接抛出，框架记录 error
throw new \RuntimeException('数据库连接失败');
```

---

## 日志配置

```php
// config/plugin/webman-tech/crontab-task/app.php
return [
    'enable' => true,
    'log' => [
        'channel' => 'crontab',  // 指定日志 channel，null 表示不记录
    ],
];
```

任务类可以覆盖全局配置：

```php
class MyTask extends BaseTask
{
    protected ?string $logChannel = 'my-task';  // 覆盖全局 channel
    protected ?string $logType = 'debug';        // 覆盖默认日志级别
    protected ?bool $logClass = false;           // 不在日志中记录类名
}
```

---

## 事件机制

在任务执行前后挂载钩子（如数据库连接管理）：

```php
// 全局事件（对所有任务生效）
// config/plugin/webman-tech/crontab-task/app.php
return [
    'enable' => true,
    'event' => [
        'before_exec' => function (BaseTask $task) {
            // 任务执行前：如重新连接数据库
            \support\Db::reconnect();
        },
        'after_exec' => function (BaseTask $task) {
            // 任务执行后：如释放连接
        },
    ],
];
```

任务级别事件（在任务类中添加）：

```php
class MyTask extends BaseTask
{
    protected bool $disableGlobalBeforeEvent = true;  // 禁用全局 before 事件

    protected function initEvents(): void
    {
        parent::initEvents();
        $this->addBeforeEvent(function () {
            // 该任务专属的 before 逻辑
        });
    }
}
```

---

## 命令行工具

```bash
# 查看所有已注册的定时任务
php webman crontab-task:list

# 手动执行指定任务（调试用）
php webman crontab-task:exec "app\task\SyncOrderTask"

# 生成新任务类
php webman make:crontab-task SyncOrder
```

---

## 常见错误

| 错误 | 原因 | 解决 |
|------|------|------|
| 任务互相阻塞 | 多个耗时任务放在同一进程 | 用 `addTask` 给每个耗时任务独立进程 |
| 日志不记录 | `log.channel` 为 null 或 channel 未配置 | 设置 `channel` 并在 log.php 中注册 |
| 任务不执行 | process.php 未正确返回 | 确认 `buildProcesses()` 有返回值 |
| 数据库连接断开 | 长时间运行后连接超时 | 在 `before_exec` 事件中重连数据库 |
