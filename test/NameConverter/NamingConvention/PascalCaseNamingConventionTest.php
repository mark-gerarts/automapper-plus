<?php

namespace AutoMapperPlus\NameConverter\NamingConvention;

use PHPUnit\Framework\TestCase;

/**
 * Class PascalCaseNamingConventionTest
 *
 * @package AutoMapperPlus\NameConverter\NamingConvention
 * @group namingConventions
 */
class PascalCaseNamingConventionTest extends TestCase
{
    public function testItParsesAPascalCaseName(): void
    {
        $converter = new PascalCaseNamingConvention();
        $name = 'PascalCaseName';
        $expected = ['pascal', 'case', 'name'];

        $this->assertEquals($expected, $converter->toParts($name));
    }

    public function testItConstructsACamelcaseName(): void
    {
        $converter = new PascalCaseNamingConvention();
        $parts = ['pascal', 'case', 'name'];
        $expected = 'PascalCaseName';

        $this->assertEquals($expected, $converter->fromParts($parts));
    }
}
