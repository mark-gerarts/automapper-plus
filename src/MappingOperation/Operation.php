<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\NameResolver\NameResolverInterface;

/**
 * Class Operation
 *
 * This is merely a helper class to make life easier, grouping all available
 * operations.
 *
 * @package AutoMapperPlus\MappingOperation
 */
class Operation
{
    /**
     * @param callable $f
     * @return callable
     */
    public static function mapFrom(callable $f): callable
    {
        return new MapFrom($f);
    }

    /**
     * @return callable
     */
    public static function ignore(): callable
    {
        return new Ignore();
    }

    /**
     * @param NameResolverInterface $nameResolver
     * @return callable
     */
    public static function getProperty(NameResolverInterface $nameResolver): callable
    {
        return new GetProperty($nameResolver);
    }
}
