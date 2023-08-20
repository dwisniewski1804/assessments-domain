<?php

namespace App\Domain\Core\Exception;

class SupervisorDoesNotHaveAuthorityException extends \Exception
{
    protected $message = "Supervisor doesn't have authority for this standard.";
}