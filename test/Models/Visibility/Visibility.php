<?php

namespace Test\Models\Visibility;

/**
 * Class Visibility
 *
 * @package Test\Models
 */
class Visibility
{
    public $publicProperty = true;
    protected $protectedProperty = true;
    private $privateProperty = true;

    public function getPublicProperty()
    {
        return $this->publicProperty;
    }

    public function getProtectedProperty()
    {
        return $this->protectedProperty;
    }

    public function getPrivateProperty()
    {
        return $this->privateProperty;
    }

}
