<?php

namespace AutoMapperPlus\Exception;

/**
 * Class UnregisteredMappingException
 *
 * @package AutoMapperPlus\Exception
 */
class UnregisteredMappingException extends AutoMapperPlusException
{
    /**
     * @param string $from
     * @param string $to
     * @return UnregisteredMappingException
     */
    public static function fromClasses(string $from, string $to): UnregisteredMappingException
    {
        return new static("No mapping registered for converting an instance of class {$from} into one of {$to}");
    }
}
