<?php

namespace AutoMapperPlus\NameConverter\NamingConvention;

/**
 * Class CamelCaseNamingConvention
 *
 * @package AutoMapperPlus\NameConverter\NamingConvention
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
        $parts = array_map('ucfirst', $parts);

        return lcfirst(implode('', $parts));
    }
}
