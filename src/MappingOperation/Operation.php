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
     * Set a property's value from callback, callback should contain 2
     * parameters
     *
     * @param callable $valueCallback
     *      Callback definition:
     *
     *      function(AutoMapperInterface, mixed){

     *      }
     * @return MapFromWithMapper
     *
     * @deprecated Will be removed. See MapFromWithMapper for more information.
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
     * @param bool $sourceIsObjectArray
     *   Indicates whether or not an array as source value should be treated as
     *   a collection of elements, or as an array representing an object.
     * @param array $context
     *   Arbitrary values that will be passed the the mapper as context. See
     *   MapperInterface::nap() as well.
     * @return MapTo
     */
    public static function mapTo(
        string $destinationClass,
        bool $sourceIsObjectArray = false,
        array $context = []
    ): MapTo {
        return new MapTo($destinationClass, $sourceIsObjectArray, $context);
    }

    /**
     * Maps a collection (array, iterator, ...) of mappable objects to a
     * collection of destination objects.
     *
     * @param string $destinationClass
     * @param array $context
     *   Arbitrary values that will be passed the the mapper as context. See
     *   MapperInterface::nap() as well.
     * @return MapTo
     */
    public static function mapCollectionTo(
        string $destinationClass,
        array $context = []
    ): MapTo {
        return new MapTo($destinationClass, false, $context);
    }

    /**
     * Maps an array representing an object to the destination class. If you're
     * looking to map a list of objects, use `mapCollectionTo`.
     *
     * @param string $destinationClass
     * @param array $context
     *   Arbitrary values that will be passed the the mapper as context. See
     *   MapperInterface::nap() as well.
     * @return MapTo
     */
    public static function mapArrayTo(
        string $destinationClass,
        array $context = []
    ): MapTo {
        return new MapTo($destinationClass, true, $context);
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
