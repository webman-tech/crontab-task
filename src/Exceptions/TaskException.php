<?php

namespace WebmanTech\CrontabTask\Exceptions;

use Exception;
use Throwable;

class TaskException extends Exception implements TaskExceptionInterface
{
    protected array $data = [];

    public function __construct(string $message = "Task Error", array $data = [], int $code = 0, ?Throwable $previous = null)
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
        return $data ? (string)json_encode($data, JSON_UNESCAPED_UNICODE) : '';
    }
}
