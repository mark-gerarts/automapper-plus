<?php
/**
 * Created by PhpStorm.
 * User: Veaceslav Vasilache <veaceslav.vasilache@gmail.com>
 * Date: 6/6/18
 * Time: 7:42 PM
 */

namespace AutoMapperPlus\Exception;


use Throwable;

class InvalidMappingSourceException extends \Exception
{
    public function __construct($source, string $message = "", int $code = 0, Throwable $previous = null)
    {
        $defaultMessage = 'Invalid mapping source ' . gettype($source) . ' specified. Only Array or Objects are accepted';

        parent::__construct($message ?: $defaultMessage, $code, $previous);
    }
}