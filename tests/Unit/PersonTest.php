<?php

use App\Domain\Person;

it('should create a Person', function () {
    // Arrange & Act
    $oscar = new Person('Oscar');

    // Assert
    expect($oscar->name)->toBe('Oscar');
});

it('should compare two Persons for equality', function () {
    // Arrange
    $oscar1 = new Person('Oscar');
    $oscar2 = new Person('Oscar');
    $miguel = new Person('Miguel');


    // Act & Assert
    expect($oscar1->equals($oscar2))->toBeTrue();
    expect($oscar1->equals($miguel))->toBeFalse();
});
