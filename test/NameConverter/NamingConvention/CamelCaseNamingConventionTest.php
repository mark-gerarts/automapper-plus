<?php

namespace AutoMapperPlus\NameConverter\NamingConvention;

use PHPUnit\Framework\TestCase;

/**
 * Class CamelCaseNamingConventionTest
 *
 * @package AutoMapperPlus\NameConverter\NamingConvention
 * @group namingConventions
 */
class CamelCaseNamingConventionTest extends TestCase
{
    public function testItParsesACamelcaseName()
    {
        $converter = new CamelCaseNamingConvention();
        $name = 'camelCaseName';
        $expected = ['camel', 'case', 'name'];

        $this->assertEquals($expected, $converter->toParts($name));
    }

    public function testItConstructsACamelcaseName()
    {
        $converter = new CamelCaseNamingConvention();
        $parts = ['camel', 'case', 'name'];
        $expected = 'camelCaseName';

        $this->assertEquals($expected, $converter->fromParts($parts));
    }
}
