<?php

namespace App\Domain\Core\Exception;

class CanNotAddContractForOtherClientException extends \DomainException
{
    protected $message = 'Contract can be only added to the client that own the contract';
}