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
    $oscar = new Person("Oscar");
    $miguel = new Person("Miguel");

    // Act & Assert
    expect($oscar === $miguel)->toBeFalse();
    expect($miguel === $miguel)->toBeTrue();
});
