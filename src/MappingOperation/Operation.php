<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\MappingOperation\Implementations\FromProperty;
use AutoMapperPlus\MappingOperation\Implementations\Ignore;
use AutoMapperPlus\MappingOperation\Implementations\MapFrom;
use AutoMapperPlus\MappingOperation\Implementations\MapFromWithMapper;
use AutoMapperPlus\MappingOperation\Implementations\MapTo;
use AutoMapperPlus\MappingOperation\Implementations\SetTo;

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
     * @param bool $assumeCollection
     *   Indicates whether or not an array as source value should be treated as
     *   a collection of elements, or as an array representing an object.
     * @return MapTo
     */
    public static function mapTo(
        string $destinationClass,
        bool $assumeCollection = true
    ): MapTo {
        return new MapTo($destinationClass, $assumeCollection);
    }

    /**
     * Maps a collection (array, iterator, ...) of mappable objects to a
     * collection of destination objects.
     *
     * @param string $destinationClass
     * @return MapTo
     */
    public static function mapCollectionTo(string $destinationClass): MapTo
    {
        return new MapTo($destinationClass, true);
    }

    /**
     * Maps an array representing an object to the destination class. If you're
     * looking to map a list of objects, use `mapCollectionTo`.
     *
     * @param string $destinationClass
     * @return MapTo
     */
    public static function mapArrayTo(string $destinationClass): MapTo
    {
        return new MapTo($destinationClass, false);
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

    /**
     * @param mixed $value
     * @return SetTo
     */
    public static function setTo($value): SetTo
    {
        return new SetTo($value);
    }
}
