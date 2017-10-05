<?php

namespace AutoMapperPlus\Exception;

/**
 * Class UnregisteredMappingException
 *
 * @package AutoMapperPlus\Exception
 */
class UnregisteredMappingException extends \Exception
{
    /**
     * @param $from
     * @param $to
     * @return UnregisteredMappingException
     */
    public static function fromClasses($from, $to)
    {
        return new static("No mapping registered for converting an instance of class {$from} into one of {$to}");
    }
}
