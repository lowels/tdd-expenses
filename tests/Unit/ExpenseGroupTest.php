<?php

use App\Domain\Expense;
use App\Domain\ExpenseGroup;
use App\Domain\Person;

it('creates an expense group with a name and empty participants list', function () {
    // Arrange & Act
    $dinnerGroup = new ExpenseGroup('Dinner');

    // Assert
    expect($dinnerGroup->members)->toBe([]);
    expect($dinnerGroup->name)->toBe('Dinner');
});

it('adds members to the group', function () {
    // Arrange
    $dinnerGroup = new ExpenseGroup('Dinner');
    $oscar = new Person("Oscar");
    $pablo = new Person("Pablo");

    // Act
    $dinnerGroup->addMembers([$oscar, $pablo]);

    // Assert
    expect(count($dinnerGroup->members))->toBe(2);
});

it('prevents duplicate members in a expense group', function () {
    // Arrange
    $dinnerGroup = new ExpenseGroup('Dinner');
    $oscar = new Person("Oscar");
    $pablo = new Person("Pablo");

    // Act
    $dinnerGroup->addMembers([$oscar, $pablo]);
    $dinnerGroup->addMembers([$pablo]);

    // Assert
    expect(count($dinnerGroup->members))->toBe(2);
});

it('records a simple expense split equally', function () {
    // Arrange
    $dinnerGroup = new ExpenseGroup('Dinner');
    $oscar = new Person("Oscar");
    $taxi = new Expense($oscar, "Taxi", 20);

    $dinnerGroup->addMembers([$oscar]);

    // Act
    $dinnerGroup->addExpense($taxi);

    // Assert
    expect(count($dinnerGroup->expenses))->toBe(1);
});

it('prevents adding an expense with a payer that is not a member of the group', function () {
    // Arrange
    $dinnerGroup = new ExpenseGroup('Dinner');
    $oscar = new Person("Oscar");
    $taxi = new Expense($oscar, "Taxi", 20);

    // Act & Assert
    $dinnerGroup->addExpense($taxi);
})->throws(Exception::class);

it('prevents adding an expense with a participant that is not a member of the group', function () {
    // Arrange
    $dinnerGroup = new ExpenseGroup('Dinner');

    $oscar = new Person("Oscar");
    $pablo = new Person("Pablo");

    $taxi = new Expense($oscar, "Taxi", 20);
    $taxi->addParticipants([$pablo]);

    $dinnerGroup->addMembers([$oscar]);

    // Act & Assert
    $dinnerGroup->addExpense($taxi);
})->throws(Exception::class);

it('calculates the total paid amount for a member', function () {
    // Arrange
    $dinnerGroup = new ExpenseGroup('Dinner');
    $oscar = new Person("Oscar");
    $pablo = new Person("Pablo");
    $miguel = new Person("Miguel");

    $dinnerGroup->addMembers([$oscar, $pablo, $miguel]);

    $dinner = new Expense($oscar, "Dinner", 60);
    $dinner->addParticipants([$pablo, $miguel, $oscar]);

    $taxi = new Expense($pablo, "Taxi", 30);
    $taxi->addParticipants([$miguel, $pablo]);

    $dinnerGroup->addExpense($dinner);
    $dinnerGroup->addExpense($taxi);

    // Act & Assert
    expect($dinnerGroup->totalPaidBy($oscar))->toBe(60.0);
});

it('calculates the total owed amount for a member', function () {
    // Arrange
    $dinnerGroup = new ExpenseGroup('Dinner');
    $oscar = new Person("Oscar");
    $pablo = new Person("Pablo");
    $miguel = new Person("Miguel");

    $dinnerGroup->addMembers([$oscar, $pablo, $miguel]);

    $dinner = new Expense($oscar, "Dinner", 60); // 20 each (Oscar, Pablo, miguel)
    $dinner->addParticipants([$pablo, $miguel, $oscar]);

    $taxi = new Expense($pablo, "Taxi", 30); // 15 each (Pablo, miguel)
    $taxi->addParticipants([$miguel, $pablo]);

    $dinnerGroup->addExpense($dinner);
    $dinnerGroup->addExpense($taxi);

    // Act & Assert
    expect($dinnerGroup->totalOwedBy($pablo))->toBe(35.0);
});

it('calculates the balance for a member', function () {
    // Arrange
    $dinnerGroup = new ExpenseGroup('Dinner');
    $oscar = new Person("Oscar");
    $pablo = new Person("Pablo");
    $miguel = new Person("Miguel");

    $dinnerGroup->addMembers([$oscar, $pablo, $miguel]);

    $dinner = new Expense($oscar, "Dinner", 60); // 20 each
    $dinner->addParticipants([$pablo, $miguel, $oscar]);

    $taxi = new Expense($pablo, "Taxi", 30); // 15 each (Pablo, miguel)
    $taxi->addParticipants([$miguel, $pablo]);

    $dinnerGroup->addExpense($dinner);
    $dinnerGroup->addExpense($taxi);

    // Pablo paid 30 and owes 35 => balance -5
    // Act & Assert
    expect($dinnerGroup->balanceFor($pablo))->toBe(-5.0);
});
