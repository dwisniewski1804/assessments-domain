<?php

namespace App\Domain\Core\Exception;

class CanNotEvaluateDueToTimeAfterRulesException extends \DomainException
{
    protected $message = 'Assessment can not be evaluated due to last evaluation done on it. You need to wait specified time after the last evaluation';
}