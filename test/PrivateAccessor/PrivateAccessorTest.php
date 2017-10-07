<?php

namespace AutoMapperPlus\PrivateAccessor;

use PHPUnit\Framework\TestCase;
use Test\Models\SimpleProperties\HasPrivateProperties;

/**
 * Class PrivateAccessorTest
 *
 * @package AutoMapperPlus\PrivateAccessor
 */
class PrivateAccessorTest extends TestCase
{
    public function testItCanAccessAPrivateProperty()
    {
        $object = new HasPrivateProperties('Username', 'hunter2');
        $password = PrivateAccessor::getPrivate($object, 'password');

        $this->assertEquals('hunter2', $password);
    }

    public function testItCanSetAPrivateProperty()
    {
        $object = new HasPrivateProperties('Username', 'hunter2');
        PrivateAccessor::setPrivate($object, 'password', 'changed');

        $this->assertEquals('changed', $object->getPassword());
    }
}
