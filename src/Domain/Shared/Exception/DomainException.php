<?php

namespace App\Domain\Shared\Exception;

class DomainException extends \Exception
{
    protected $message = 'This is domain exception. You should not see this message. It means that specific domain exception was not served properly';
}