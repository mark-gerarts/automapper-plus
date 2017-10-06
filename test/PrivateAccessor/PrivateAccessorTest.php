<?php

namespace AutoMapperPlus\PrivateAccessor;

use Test\Models\SimpleProperties\HasPrivateProperties;

/**
 * Class PrivateAccessorTest
 *
 * @package AutoMapperPlus\PrivateAccessor
 */
class PrivateAccessorTest extends \PHPUnit\Framework\TestCase
{
    public function testItCanAccessAPrivateProperty()
    {
        $object = new HasPrivateProperties('Username', 'hunter2');
        $privateAccessor = new PrivateAccessor();

        $password = $privateAccessor->getPrivate($object, 'password');
        $this->assertEquals('hunter2', $password);
    }

    public function testItCanSetAPrivateProperty()
    {
        $object = new HasPrivateProperties('Username', 'hunter2');
        $privateAccessor = new PrivateAccessor();

        $privateAccessor->setPrivate($object, 'password', 'changed');
        $this->assertEquals('changed', $object->getPassword());
    }
}
