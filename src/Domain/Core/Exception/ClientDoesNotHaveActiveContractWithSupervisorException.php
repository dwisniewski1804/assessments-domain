<?php

namespace App\Domain\Core\Exception;

class ClientDoesNotHaveActiveContractWithSupervisorException extends \DomainException
{
    protected $message = "Contract between these two units in not active or does not exist";
}