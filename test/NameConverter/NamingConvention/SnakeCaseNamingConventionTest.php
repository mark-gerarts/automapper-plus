<?php

namespace AutoMapperPlus\NameConverter\NamingConvention;

use PHPUnit\Framework\TestCase;

/**
 * Class SnakeCaseNamingConventionTest
 *
 * @package AutoMapperPlus\NameConverter\NamingConvention
 * @group namingConventions
 */
class SnakeCaseNamingConventionTest extends TestCase
{
    public function testItParsesASnakeCaseName(): void
    {
        $converter = new SnakeCaseNamingConvention();
        $name = 'snake_case_name';
        $expected = ['snake', 'case', 'name'];

        $this->assertEquals($expected, $converter->toParts($name));
    }

    public function testItConstructsACamelcaseName(): void
    {
        $converter = new SnakeCaseNamingConvention();
        $parts = ['snake', 'case', 'name'];
        $expected = 'snake_case_name';

        $this->assertEquals($expected, $converter->fromParts($parts));
    }
}
