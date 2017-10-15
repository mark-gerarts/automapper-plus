<?php

namespace AutoMapperPlus\NameResolver\NamingConvention;

/**
 * Class SnakeCaseNamingConvention
 *
 * @package AutoMapperPlus\NameResolver\NamingConvention
 */
class SnakeCaseNamingConvention extends BaseNamingConvention
{
    /**
     * @inheritdoc
     */
    public function toParts(string $name): array
    {
        return explode('_', $name);
    }

    /**
     * @inheritdoc
     */
    public function fromParts(array $parts): string
    {
        return implode('_', $parts);
    }

}
