<?php

namespace AutoMapperPlus\Exception;

/**
 * Class InvalidPropertyException
 *
 * @package AutoMapperPlus\Exception
 */
class InvalidPropertyException extends AutoMapperPlusException
{
    /**
     * @param string $name
     * @param string $class
     * @return InvalidPropertyException
     */
    public static function fromNameAndClass(string $name, string $class): InvalidPropertyException
    {
        return new static("Property {$name} does not exist for class {$class}");
    }
}
