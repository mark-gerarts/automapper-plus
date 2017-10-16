<?php

namespace AutoMapperPlus\NameConverter\NamingConvention;

use function Functional\map;

/**
 * Class BaseNamingConvention
 *
 * @package AutoMapperPlus\NameConverter\NamingConvention
 */
abstract class BaseNamingConvention implements NamingConventionInterface
{
    /**
     * @param array $parts
     * @return array
     */
    public function normalize(array $parts): array
    {
        return map($parts, function (string $part) {
            return strtolower($part);
        });
    }
}
