<?php

namespace App\Domain\Core\Exception;

class AssessmentAlreadyLockedException extends \DomainException
{
    protected $message = 'Assessment is already lock, you have to unlock it in advance';
}
