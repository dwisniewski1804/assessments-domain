<?php

namespace App\Domain\Core\Exception;

use App\Domain\Shared\Exception\DomainException;

class InvalidAssessmentDataException extends DomainException
{
    protected $message = 'Invalid Assessment data passed.';
}