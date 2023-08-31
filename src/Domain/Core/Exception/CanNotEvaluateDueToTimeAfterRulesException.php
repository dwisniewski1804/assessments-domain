<?php

namespace App\Domain\Core\Exception;

use App\Domain\Shared\Exception\DomainException;

class CanNotEvaluateDueToTimeAfterRulesException extends DomainException
{
    protected $message = "Assessment can not be evaluated due to last evaluation done on it. You need to wait %s days after the last evaluation if negative and %s days if positive.";

    public function __construct(int $negativeTime, int $positiveTime)
    {
        parent::__construct(printf($this->message, $negativeTime, $positiveTime));
    }
}