<?php

namespace AutoMapperPlus\NameResolver;

use AutoMapperPlus\NameConverter\NameConverter;
use AutoMapperPlus\NameConverter\NamingConvention\CamelCaseNamingConvention;
use AutoMapperPlus\NameConverter\NamingConvention\SnakeCaseNamingConvention;
use PHPUnit\Framework\TestCase;

/**
 * Class NameConverterTest
 *
 * @package AutoMapperPlus\NameResolver
 */
class NameConverterTest extends TestCase
{
    public function testItResolvesConventions()
    {
        $input = 'camelCaseNotation';
        $expectedOutput = 'camel_case_notation';

        $output = NameConverter::convert(
            new CamelCaseNamingConvention(),
            new SnakeCaseNamingConvention(),
            $input
        );

        $this->assertEquals($expectedOutput, $output);
    }
}
