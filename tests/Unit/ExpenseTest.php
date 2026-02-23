<?php

use App\Domain\Expense;
use App\Domain\Person;

it('creates an expense', function() {
    // Arrange
    $oscar = new Person('Oscar');

    // Act
    $expense = new Expense($oscar, 'Taxi', 30);

    // Assert
    expect($expense->person->name)->toBe('Oscar');
    expect($expense->description)->toBe('Taxi');
    expect($expense->amount)->toBe(30.0);
});

it('add participants to an expense', function() {

    // Arrange
    $oscar = new Person('Oscar');
    $miguel = new Person('Miguel');
    $pablo = new Person('Pablo');

    $expense = new Expense($oscar, 'Taxi', 30);

    // Act
    $expense->addParticipants([$oscar, $miguel, $pablo]);

    // Assert
    expect(count($expense->participants))->toBe(3);

});

it('should calculate the share per person', function() {
    // Arrange

    // Act

    // Assert
})->todo();

test('a person is not a participant of an expense', function() {
    // Arrange

    // Act

    // Assert
})->todo();

it('should throw an exception if calculating share with no participants', function() {
    // Arrange

    // Act

    // Assert
})->todo();
