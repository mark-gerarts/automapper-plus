<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\Test\Models\SimpleProperties\Source;
use PHPUnit\Framework\TestCase;

/**
 * Class OptionsTest
 *
 * @package AutoMapperPlus\Configuration
 */
class OptionsTest extends TestCase
{
    public function testItCanRegisterAnObjectCrate()
    {
        $options = new Options();

        $this->assertFalse($options->isObjectCrate(Source::class));
        $options->registerObjectCrate(Source::class);
        $this->assertTrue($options->isObjectCrate(Source::class));
    }
}
