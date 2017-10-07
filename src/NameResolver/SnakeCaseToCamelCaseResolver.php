<?php

namespace AutoMapperPlus\NameResolver;

use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

/**
 * Class SnakeCaseToCamelCaseResolver
 *
 * @package AutoMapperPlus\NameResolver
 */
class SnakeCaseToCamelCaseResolver implements NameResolverInterface
{
    /**
     * @inheritdoc
     */
    public function resolve(string $name): string
    {
        $converter = new CamelCaseToSnakeCaseNameConverter();
        return $converter->denormalize($name);
    }
}
