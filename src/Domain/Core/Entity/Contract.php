<?php

namespace App\Domain\Core\Entity;

use App\Domain\Shared\ValueObjects\Uuid;
use DateTime;

class Contract
{
    public readonly Uuid $id;

    public readonly Supervisor $supervisor;

    public readonly Client $client;
    public readonly DateTime $start;
    public readonly DateTime $end;

    public function __construct(
        Uuid $id,
        DateTime $start,
        DateTime $end,
        Supervisor $supervisor,
        Client $client
    ) {
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;
        $this->supervisor = $supervisor;
        $this->client = $client;
    }
}