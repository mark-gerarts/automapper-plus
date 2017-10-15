<?php

namespace AutoMapperPlus\PropertyAccessor;

use PHPUnit\Framework\TestCase;
use Test\Models\Visibility\Visibility;

class PropertyAccessorTest extends TestCase
{
    public function testItGetsAPublicProperty()
    {
        $accessor = new PropertyAccessor();
        $visibility = new Visibility();

        $this->assertTrue($accessor->getProperty($visibility, 'publicProperty'));
    }

    public function testItGetsAProtectedProperty()
    {
        $accessor = new PropertyAccessor();
        $visibility = new Visibility();

        $this->assertTrue($accessor->getProperty($visibility, 'protectedProperty'));
    }

    public function testItGetsAPrivateProperty()
    {
        $accessor = new PropertyAccessor();
        $visibility = new Visibility();

        $this->assertTrue($accessor->getProperty($visibility, 'privateProperty'));
    }

    public function testItSetsAPublicProperty()
    {
        $accessor = new PropertyAccessor();
        $visibility = new Visibility();
        $accessor->setProperty($visibility, 'publicProperty', false);

        $this->assertFalse($visibility->getPublicProperty());
    }

    public function testItSetsAProtectedProperty()
    {
        $accessor = new PropertyAccessor();
        $visibility = new Visibility();
        $accessor->setProperty($visibility, 'protectedProperty', false);

        $this->assertFalse($visibility->getProtectedProperty());
    }

    public function testItSetsAPrivateProperty()
    {
        $accessor = new PropertyAccessor();
        $visibility = new Visibility();
        $accessor->setProperty($visibility, 'privateProperty', false);

        $this->assertFalse($visibility->getPrivateProperty());
    }
}
