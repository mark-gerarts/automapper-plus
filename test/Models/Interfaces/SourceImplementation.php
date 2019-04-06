<?php

namespace AutoMapperPlus\Test\Models\Interfaces;

class SourceImplementation implements SourceInterface
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
