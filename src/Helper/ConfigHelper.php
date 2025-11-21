<?php

namespace WebmanTech\CrontabTask\Helper;

use function WebmanTech\CommonUtils\config;

/**
 * @internal
 */
final class ConfigHelper
{
    /**
     * 获取配置
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null)
    {
        return config("plugin.webman-tech.crontab-task.{$key}", $default);
    }
}
