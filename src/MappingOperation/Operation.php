<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\MappingOperation\Implementations\FromProperty;
use AutoMapperPlus\MappingOperation\Implementations\Ignore;
use AutoMapperPlus\MappingOperation\Implementations\MapFrom;
use AutoMapperPlus\MappingOperation\Implementations\MapFromWithMapper;
use AutoMapperPlus\MappingOperation\Implementations\MapTo;

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
     * Set a property's value from a callback.
     *
     * @param callable $valueCallback
     * @return MapFrom
     */
    public static function mapFrom(callable $valueCallback): MapFrom
    {
        return new MapFrom($valueCallback);
    }

    /**
     * Set a property's value from callback, callback should contain 2 parameters
     *
     * @param callable $valueCallback
     *      Callback definition:
     *
     *      function(AutoMapperInterface, mixed){

     *      }
     * @return MapFromWithMapper
     */
    public static function mapFromWithMapper(callable $valueCallback): MapFromWithMapper
    {
        return new MapFromWithMapper($valueCallback);
    }

    /**
     * Ignore a property.
     *
     * @return Ignore
     */
    public static function ignore(): Ignore
    {
        return new Ignore();
    }

    /**
     * Map a property to a class.
     *
     * @param string $destinationClass
     * @return MapTo
     */
    public static function mapTo(string $destinationClass): MapTo
    {
        return new MapTo($destinationClass);
    }

    /**
     * Allows the source property name to be explicitly stated.
     *
     * @param string $propertyName
     * @return FromProperty
     */
    public static function fromProperty(string $propertyName): FromProperty
    {
        return new FromProperty($propertyName);
    }
}
