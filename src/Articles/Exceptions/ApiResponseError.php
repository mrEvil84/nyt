<?php

namespace App\Articles\Exceptions;

use JetBrains\PhpStorm\Pure;

class ApiResponseError extends \Exception
{
    #[Pure] public static function apiResponseError($message): self
    {
        return new self($message);
    }
}