<?php

namespace AutoMapperPlus\MappingOperation;

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
     * @return callable
     */
    public static function getProperty(): callable
    {
        return new GetProperty();
    }
}
