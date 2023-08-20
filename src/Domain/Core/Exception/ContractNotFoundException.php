<?php

namespace App\Domain\Core\Exception;

use App\Domain\Shared\Exception\DomainException;

class ContractNotFoundException extends DomainException
{
    protected $message = 'No active contract between client and supervisor';
}