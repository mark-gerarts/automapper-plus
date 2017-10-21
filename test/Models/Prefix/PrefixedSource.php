<?php

namespace AutoMapperPlus\Test\Models\Prefix;

class PrefixedSource
{
    public $prefixName;
    private $prefixPrivateProperty;

    function __construct($prefixName, $prefixPrivateProperty)
    {
        $this->prefixName = $prefixName;
        $this->prefixPrivateProperty = $prefixPrivateProperty;
    }

    /**
     * @return mixed
     */
    public function getPrefixName()
    {
        return $this->prefixName;
    }

    /**
     * @param mixed $prefixName
     */
    public function setPrefixName($prefixName)
    {
        $this->prefixName = $prefixName;
    }

    /**
     * @return mixed
     */
    public function getPrefixPrivateProperty()
    {
        return $this->prefixPrivateProperty;
    }

    /**
     * @param $prefixPrivateProperty
     */
    public function setPrefixPrivateProperty($prefixPrivateProperty)
    {
        $this->prefixPrivateProperty = $prefixPrivateProperty;
    }
}
