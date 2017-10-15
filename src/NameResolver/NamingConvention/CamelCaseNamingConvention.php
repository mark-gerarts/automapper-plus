<?php

namespace AutoMapperPlus\NameResolver\NamingConvention;

use function Functional\map;

/**
 * Class CamelCaseNamingConvention
 *
 * @package AutoMapperPlus\NameResolver\NamingConvention
 */
class CamelCaseNamingConvention extends BaseNamingConvention
{
    /**
     * @inheritdoc
     */
    public function toParts(string $name): array
    {
        $parts = preg_split('/(?=[A-Z])/', $name, -1, PREG_SPLIT_NO_EMPTY);

        return $this->normalize($parts);
    }

    /**
     * @inheritdoc
     */
    public function fromParts(array $parts): string
    {
        $parts = map($parts, function (string $part) {
            return ucfirst($part);
        });

        return lcfirst(implode('', $parts));
    }
}
