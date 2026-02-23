<?php

namespace App\Domain;

use Exception;

class ExpenseGroup
{
    /**
     * @var Person[]
     */
    public array $members = [];
    /**
     * @var Expense[]
     */
    public array $expenses = [];

    public function __construct(public string $name) {}

    public function addMembers(array $members)
    {
        foreach ($members as $member) {
            if (!in_array($member, $this->members)) {
                $this->members[] = $member;
            }
        }
    }

    public function addExpense(Expense $expense)
    {
        $expensePayer = $expense->person;

        if (!in_array($expensePayer, $this->members)) {
            throw new Exception("The expense must be added by a member");
        }

        foreach ($expense->participants as $participant) {
            if (!in_array($participant, $this->members)) {
                throw new Exception("All expense participants must belong to the expense group.");
            }
        }

        $this->expenses[] = $expense;
    }

    public function totalPaidBy(Person $person): float
    {
        $totalPaid = 0;

        foreach ($this->expenses as $expense) {
            if ($expense->person === $person) {
                $totalPaid += $expense->amount;
            }
        }

        return $totalPaid;
    }

    public function totalOwedBy(Person $person): float
    {
        $totalOwed = 0;

        foreach ($this->expenses as $expense) {
            if (in_array($person, $expense->participants)) {
                $totalOwed += $expense->calculateShare();
            }
        }

        return $totalOwed;
    }

    public function balanceFor(Person $person): float
    {
        return $this->totalPaidBy($person) - $this->totalOwedBy($person);
    }
}
