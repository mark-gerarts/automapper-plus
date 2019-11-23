<?php

namespace AutoMapperPlus;

/**
 * Class DataType
 *
 * @package AutoMapperPlus
 */
final class DataType
{
    public const ARRAY = 'array';
    public const INTEGER = 'integer';
    public const FLOAT = 'float';
    public const STRING = 'string';

    public static function isDataType(string $type): bool
    {
        return \in_array(
            $type,
            [self::ARRAY, self::INTEGER, self::FLOAT, self::STRING],
            true
        );
    }
}
