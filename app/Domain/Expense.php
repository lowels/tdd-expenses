<?php

namespace App\Domain;

class Expense
{
    public array $participants = [];

    public function __construct(public Person $person, public string $description, public float $amount)
    {
    }

    public function addParticipants(array $participants): void
    {
        $this->participants = array_merge($this->participants, $participants);
    }
    public function sharePerPerson(): float
    {
        if (count($this->participants) === 0) {
            throw new \Exception('No participants added to the expense.');
        }
        
        return $this->amount / count($this->participants);
    }
    public function NotParticipants(Person $person): bool
    {
        return !in_array($person, $this->participants);
    }

}