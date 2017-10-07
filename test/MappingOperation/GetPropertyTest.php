<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\NameResolver\IdentityNameResolver;
use PHPUnit\Framework\TestCase;
use Test\Models\SimpleProperties\HasPrivatePropertiesDto;
use Test\Models\SimpleProperties\Destination;
use Test\Models\SimpleProperties\HasPrivateProperties;
use Test\Models\SimpleProperties\Source;

/**
 * Class GetPropertyTest
 *
 * @package AutoMapperPlus\MappingOperation
 */
class GetPropertyTest extends TestCase
{
    public function testItTransfersAProperty()
    {
        $source = new Source();
        $source->name = 'SourceName';
        $destination = new Destination();
        $getProperty = new GetProperty(new IdentityNameResolver());
        $getProperty($source, $destination, 'name', new AutoMapperConfig());

        $this->assertEquals($destination->name, 'SourceName');
    }

    public function testItCanTransferAPrivateProperty()
    {
        $source = new HasPrivateProperties('user', 'pass');
        $destination = new HasPrivatePropertiesDto();
        $getProperty = new GetProperty(new IdentityNameResolver());
        $getProperty($source, $destination, 'password', new AutoMapperConfig());

        $this->assertEquals($destination->password, 'pass');
    }
}
