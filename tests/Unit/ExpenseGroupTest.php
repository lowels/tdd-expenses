<?php
use App\Domain\ExpenseGroup;
use App\Domain\Person;
use App\Domain\Expense;

it('creates an expense group with a name and empty participants list', function () {
    // Arrange & Act
        $expenseGroup = new ExpenseGroup('Trip to Spain');
    
    // Assert
        expect($expenseGroup->name)->toBe('Trip to Spain');
        expect(count($expenseGroup->members))->toBe(0);
});

it('adds members to the group', function () {
    // Arrange
        $expenseGroup = new ExpenseGroup('Trip to Spain');
        $oscar = new Person('Oscar');
        $miguel = new Person('Miguel');
    // Act
        $expenseGroup->addMembers([$oscar, $miguel]);

    // Assert
        expect(count($expenseGroup->members))->toBe(2);
});

it('prevents duplicate members in a expense group', function () {
    // Arrange
        $expenseGroup = new ExpenseGroup('Trip to Spain');
        $oscar = new Person('Oscar');
    // Act
        $expenseGroup->addMembers([$oscar]);
        $expenseGroup->addMembers([$oscar]);

    // Assert
        expect(count($expenseGroup->members))->toBe(1);

});

it('records a simple expense split equally', function () {
    // Arrange
        $expenseGroup = new ExpenseGroup('Trip to Spain');
        $oscar = new Person('Oscar');
        $miguel = new Person('Miguel');
        $expenseGroup->addMembers([$oscar, $miguel]);
        $expense = new Expense($oscar, 'Dinner', 100);

    // Act
        $expenseGroup->addExpense($expense, [$oscar, $miguel]);

    // Assert
        expect($expenseGroup->expenses)->toContain($expense);
});

it('prevents adding an expense with a payer that is not a member of the group', function () {
    // Arrange
        $expenseGroup = new ExpenseGroup('Trip to Spain');
        $oscar = new Person('Oscar');
        $expense = new Expense($oscar, 'Dinner', 100);

    // Act & Assert
        expect(fn() => $expenseGroup->addExpense($expense, [$oscar]))->toThrow(\Exception::class, 'The payer must be a member of the group.');
});


it('prevents adding an expense with a participant that is not a member of the group', function () {
    // Arrange
        $expenseGroup = new ExpenseGroup('Trip to Spain');
        $oscar = new Person('Oscar');
        $miguel = new Person('Miguel');
        $pablo = new Person('Pablo');
        $expenseGroup->addMembers([$oscar, $miguel]);
        $expense = new Expense($oscar, 'Dinner', 100);

    // Act & Assert
        expect(fn() => $expenseGroup->addExpense($expense, [$oscar, $pablo]))->toThrow(\Exception::class, 'All participants must be members of the group.');
});

it('calculates the total paid amount for a member', function () {
    // Arrange
        $expenseGroup = new ExpenseGroup('Trip to Spain');
        $oscar = new Person('Oscar');
        $miguel = new Person('Miguel');
        $expenseGroup->addMembers([$oscar, $miguel]);
        $expense1 = new Expense($oscar, 'Dinner', 100.0);
        $expense2 = new Expense($miguel, 'Lunch', 50.0);
        $expenseGroup->addExpense($expense1, [$oscar, $miguel]);
        $expenseGroup->addExpense($expense2, [$oscar, $miguel]);

    // Act & Assert
        expect($expenseGroup->calculateTotalPaidFor($oscar))->toBe(100.0);
});

it('calculates the total owed amount for a member', function () {
    // Arrange
        $expenseGroup = new ExpenseGroup('Trip to Spain');
        $oscar = new Person('Oscar');
        $miguel = new Person('Miguel');
        $expenseGroup->addMembers([$oscar, $miguel]);
        $expense1 = new Expense($oscar, 'Dinner', 100.0);
        $expense2 = new Expense($miguel, 'Lunch', 50.0);
        $expenseGroup->addExpense($expense1, [$oscar, $miguel]);
        $expenseGroup->addExpense($expense2, [$oscar, $miguel]);

    // Act & Assert
        expect($expenseGroup->calculateTotalOwedFor($oscar))->toBe(75.0);
});

it('calculates the balance for a member', function () {
    // Arrange
        $expenseGroup = new ExpenseGroup('Trip to Spain');
        $oscar = new Person('Oscar');
        $miguel = new Person('Miguel');
        $expenseGroup->addMembers([$oscar, $miguel]);
        $expense1 = new Expense($oscar, 'Dinner', 100.0);
        $expense2 = new Expense($miguel, 'Lunch', 50.0);
        $expenseGroup->addExpense($expense1, [$oscar, $miguel]);
        $expenseGroup->addExpense($expense2, [$oscar, $miguel]);


    // Act & Assert
        expect($expenseGroup->calculateTotalPaidFor($oscar) - $expenseGroup->calculateTotalOwedFor($oscar))->toBe(25.0);
});