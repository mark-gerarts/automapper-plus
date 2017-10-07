<?php

namespace AutoMapperPlus\Exception;

/**
 * Class InvalidPropertyException
 *
 * @package AutoMapperPlus\Exception
 */
class InvalidPropertyException extends \Exception
{
    /**
     * @param $name
     * @param $class
     * @return InvalidPropertyException
     */
    public static function fromNameAndClass($name, $class)
    {
        return new static("Property {$name} does not exist for class {$class}");
    }
}
