<?php

namespace Reinvent\Tests\Mock;

class Person
{
}

class Dependent extends Person
{
    private $dependee;
    
    public function __construct(Person $dependee)
    {
        $this->dependee = $dependee;
    }

    public function getDependee()
    {
        return $this->dependee;
    }
}
