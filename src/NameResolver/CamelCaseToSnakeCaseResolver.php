<?php

namespace AutoMapperPlus\NameResolver;

use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

/**
 * Class CamelCaseToSnakeCaseResolver
 *
 * @package AutoMapperPlus\NameResolver
 */
class CamelCaseToSnakeCaseResolver implements NameResolverInterface
{
    /**
     * @inheritdoc
     */
    public function resolve(string $name): string
    {
        $converter = new CamelCaseToSnakeCaseNameConverter();
        return $converter->normalize($name);
    }
}
