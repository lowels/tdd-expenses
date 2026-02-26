<?php

namespace App\Domain;

class ExpenseGroup
{
    public function __construct(public string $name, public array $members = [], public array $expenses = [])
    {

    }

    public function addMembers(array $members): void
    {
        $this->members = [...$this->members, ...$members];
        $this->members = array_unique($this->members, SORT_REGULAR);
    }

    public function addExpense(Expense $expense, array $participants): void
    {
        if (!in_array($expense->person, $this->members, true)) {
            throw new \Exception('The payer must be a member of the group.');
        }

        foreach ($participants as $participant) {
            if (!in_array($participant, $this->members, true)) {
                throw new \Exception('All participants must be members of the group.');
            }
        }

        $expense->addParticipants($participants);
        $this->expenses[] = $expense;
    }

    public function calculateTotalPaidFor(Person $person): float
    {
        $totalPaid = 0.0;

        foreach ($this->expenses as $expense) {
            if ($expense->person === $person) {
                $totalPaid += $expense->amount;
            }
        }

        return $totalPaid;
    }

    public function calculateTotalOwedFor(Person $person): float
    {
        $totalOwed = 0.0;

        foreach ($this->expenses as $expense) {
            if (in_array($person, $expense->participants, true)) {
                $totalOwed += $expense->sharePerPerson();
            }
        }

        return $totalOwed;
    }

}
