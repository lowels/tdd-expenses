<?php

namespace App\Domain;

use Exception;

class Expense
{
    public array $participants = [];

    public function __construct(public Person $person, public string $description, public float $amount) {}

    public function addParticipants(array $participants): void
    {
        foreach ($participants as $participant) {
            if (!in_array($participant, $this->participants)) {
                $this->participants[] = $participant;
            }
        }
    }

    public function calculateShare(): float
    {
        $num_participants = count($this->participants);

        if ($num_participants === 0) {
            throw new Exception("To calculate the share is needed at least one participant");
        }

        $share = $this->amount / $num_participants;

        return $share;
    }
}
