<?php

namespace WebmanTech\CrontabTask\Exceptions;

interface TaskExceptionInterface
{
    /**
     * 获取异常数据
     * @return string
     */
    public function getDataAsString(): string;
}