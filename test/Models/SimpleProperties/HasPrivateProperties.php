<?php

namespace Test\Models\SimpleProperties;

/**
 * Class HasPrivateProperties
 *
 * An example class that consists of some private properties.
 *
 * @package Test\Models\SimpleProperties
 */
class HasPrivateProperties
{
    private $username;
    private $password;

    function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }
}
