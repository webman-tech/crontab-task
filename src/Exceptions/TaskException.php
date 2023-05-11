<?php

namespace WebmanTech\CrontabTask\Exceptions;

use Exception;
use Throwable;

class TaskException extends Exception implements TaskExceptionInterface
{
    protected $data = [];

    public function __construct($message = "Task Error", array $data = [], $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getDataAsString(): string
    {
        $data = $this->getData();
        return $data ? json_encode($data, JSON_UNESCAPED_UNICODE) : '';
    }
}