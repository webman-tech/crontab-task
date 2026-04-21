# webman-tech/crontab-task

本项目是从 [webman-tech/components-monorepo](https://github.com/orgs/webman-tech/components-monorepo) 自动 split 出来的，请勿直接修改

## 简介

webman 定时任务管理插件，基于 [workerman/crontab](https://www.workerman.net/doc/webman/components/crontab.html) 实现，提供更加便捷的定时任务管理方式。

该插件解决了 webman 默认定时任务配置的一些问题：单进程下起多个定时任务存在阻塞问题、缺乏统一的日志记录机制、定时任务管理不够灵活。

## 功能特性

- **灵活的进程管理**：支持单任务独立进程和多任务共享进程两种配置方式
- **完善的日志支持**：提供任务开始、结束、异常等常规日志记录
- **事件机制**：支持任务执行前后的事件处理
- **内存管理**：支持任务执行后自动释放内存
- **命令行工具**：提供查看和创建定时任务的命令
- **异常处理**：完善的异常处理机制，防止任务异常影响整个进程

## 安装

```bash
composer require webman-tech/crontab-task
```

## 核心组件

### BaseTask 基础任务类

[BaseTask](src/BaseTask.php) 是所有定时任务的基类，提供任务执行的完整生命周期管理。子类需实现 `handle()` 抽象方法编写具体业务逻辑，框架负责异常捕获、日志记录、事件触发和内存管理。异常分为两类：`TaskException` 记录为 warning 级别（业务异常），其他异常记录为 error 级别（系统异常）。

### Schedule 调度器

[Schedule](src/Schedule.php) 用于配置和管理定时任务进程，通过 `addTask()` 添加单个任务（独立进程）、`addTasks()` 添加多个任务（同一进程，注意阻塞问题），最后调用 `buildProcesses()` 构建进程配置数组。

### TaskProcess 任务进程

[TaskProcess](src/TaskProcess.php) 是实际的任务进程类，负责验证任务配置的正确性，并在进程启动时注册和管理定时任务的执行。

## 命令行工具

- `php webman crontab-task:list`：查看当前所有定时任务列表
- `php webman make:crontab-task MyTask`：快速创建定时任务类

## AI 辅助

- **开发维护**：[AGENTS.md](AGENTS.md) — 面向 AI 的代码结构和开发规范说明
- **使用指南**：[skills/webman-tech-crontab-task-best-practices/SKILL.md](skills/webman-tech-crontab-task-best-practices/SKILL.md) — 面向 AI 的最佳实践，可安装到 Claude Code 的 skills 目录使用
