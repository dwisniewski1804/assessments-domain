<?php

namespace App\Domain\Core\Entity\Enum;

enum LockType: string
{
    case SUSPENDED = 'suspended';
    case WITHDRAWN = 'withdrawn';
}
