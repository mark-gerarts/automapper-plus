<?php

namespace AutoMapperPlus\MappingOperation;

/**
 * Class Operation
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
