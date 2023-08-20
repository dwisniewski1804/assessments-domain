<?php

namespace App\Domain\Core\Entity;

use DateTime;

class Contract
{
    public readonly string $id;

    public readonly Supervisor $supervisor;

    public readonly Client $client;
    public readonly DateTime $start;
    public readonly DateTime $end;

    public function __construct(string $id, DateTime $start, DateTime $end, Supervisor $supervisor, Client $client) {
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;
        $this->supervisor = $supervisor;
        $this->client = $client;
    }
}