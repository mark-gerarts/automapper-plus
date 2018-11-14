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
     * @param $from
     * @param $to
     * @return UnregisteredMappingException
     */
    public static function fromClasses($from, $to): UnregisteredMappingException
    {
        return new static("No mapping registered for converting an instance of class {$from} into one of {$to}");
    }
}
