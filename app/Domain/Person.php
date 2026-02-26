<?php

namespace App\Domain;

class Person
{
    public function __construct(public string $name)
    {
    }

    public function equals(Person $other): bool
    {
        return $this->name === $other->name;
    }

}
