<?php

namespace AutoMapperPlus\Test\Models\Prefix;

class UnPrefixedSource
{
    public $name;
    private $privateProperty;

    public function __construct($name, $privateProperty)
    {
        $this->name = $name;
        $this->privateProperty = $privateProperty;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPrivateProperty()
    {
        return $this->privateProperty;
    }

    /**
     * @param mixed $privateProperty
     */
    public function setPrivateProperty($privateProperty)
    {
        $this->privateProperty = $privateProperty;
    }
}
