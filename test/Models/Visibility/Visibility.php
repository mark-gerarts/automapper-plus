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

    /**
     * @return bool
     */
    public function getPublicProperty(): bool
    {
        return $this->publicProperty;
    }

    /**
     * @return bool
     */
    public function getProtectedProperty(): bool
    {
        return $this->protectedProperty;
    }

    /**
     * @return bool
     */
    public function getPrivateProperty(): bool
    {
        return $this->privateProperty;
    }

}
