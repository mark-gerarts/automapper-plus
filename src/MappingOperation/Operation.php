<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\MappingOperation\Implementations\Ignore;
use AutoMapperPlus\MappingOperation\Implementations\MapFrom;

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
     * @param callable $valueCallback
     * @return MappingOperationInterface
     */
    public static function mapFrom(callable $valueCallback): MappingOperationInterface
    {
        return new MapFrom($valueCallback);
    }

    /**
     * @return MappingOperationInterface
     */
    public static function ignore(): MappingOperationInterface
    {
        return new Ignore();
    }
}
