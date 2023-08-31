<?php

namespace App\Domain\Shared\Exception;

class IdGenerationAttemptsExceededException extends \DomainException
{
    protected $message = "Maximium number of attempts exceeded.";
}
