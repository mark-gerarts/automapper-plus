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
     * @param string ...$to
     * @return UnregisteredMappingException
     */
    public static function fromClasses(string $from, string ...$to): UnregisteredMappingException
    {
        $message = sprintf(
            'No mapping registered for converting an instance of class %s into one of %s',
            $from,
            implode(", ", $to)
        );
        return new static($message);
    }
}
