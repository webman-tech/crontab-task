# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## 项目概述

webman 定时任务管理插件，基于 [workerman/crontab](https://www.workerman.net/doc/webman/components/crontab.html) 实现，提供更加便捷的定时任务管理方式。

**解决的问题**：
1. 单进程下多个定时任务会阻塞
2. 缺乏统一的日志记录
3. 定时任务管理不够灵活

**核心功能**：
- **灵活的进程管理**：单进程单个/多个任务配置
- **完善的日志支持**：开始、结束、异常日志
- **事件机制**：任务执行前后事件处理
- **内存管理**：自动释放内存
- **命令行工具**：任务列表查看、创建和手动执行
- **异常处理**：防止任务异常影响进程

## 开发命令

测试、静态分析等通用命令与根项目一致，详见根目录 [CLAUDE.md](../../CLAUDE.md)。

## 命令行工具

组件提供了以下命令行工具用于定时任务管理：

```bash
# 查看定时任务列表
php webman crontab-task:list

# 创建新的定时任务
php webman make:crontab-task <name>

# 手动执行指定任务
php webman crontab-task:exec <taskClassName>
```

## 项目架构

### 核心组件
- **Schedule**：调度器，管理定时任务
- **BaseTask**：任务基类
- **CronParser**：Cron 表达式解析
- **TaskViewer**：任务查看器
- **Commands**：
  - `CrontabTaskListCommand`：查看任务列表
  - `MakeTaskCommand`：创建新任务
  - `CrontabTaskExecCommand`：手动执行任务

### 目录结构
- `src/`：
  - `Schedule.php`：调度器
  - `BaseTask.php`：任务基类
  - `TaskProcess.php`：任务进程
  - `Components/`：组件
    - `CronParser.php`：Cron 解析器
    - `TaskViewer.php`：任务查看器
  - `Commands/`：命令行工具
  - `Helper/`：助手类
  - `Traits/`：特性
  - `Exceptions/`：异常类
- `copy/`：配置文件模板
- `src/Install.php`：Webman 安装脚本

测试文件位于项目根目录的 `tests/Unit/CrontabTask/`。

## 代码风格

与根项目保持一致，详见根目录 [CLAUDE.md](../../CLAUDE.md)。

## 注意事项

1. **阻塞问题**：单进程多任务时，注意任务执行时间
2. **日志记录**：任务开始、结束、异常都会记录日志
3. **内存管理**：任务执行后会自动释放内存
4. **事件机制**：可以通过事件监听任务执行状态
5. **测试位置**：单元测试在项目根目录的 `tests/Unit/CrontabTask/` 下，而非包内
