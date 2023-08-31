<?php

namespace App\Domain\Core\Exception;

use App\Domain\Shared\Exception\DomainException;

class SupervisorDoesNotHaveAuthorityException extends DomainException
{
    protected $message = "Supervisor doesn't have authority for this standard.";
}
