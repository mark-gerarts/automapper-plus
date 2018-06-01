<?php
/**
 * Created by PhpStorm.
 * User: Veaceslav Vasilache <veaceslav.vasilache@gmail.com>
 * Date: 6/1/18
 * Time: 8:31 AM
 */

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;

class FromKey implements MappingOperationInterface
{
    private $keyName;

    public function __construct(string $keyName)
    {
        $this->keyName = $keyName;
    }

    public function mapProperty(string $propertyName, $source, $destination): void
    {
        // Set the array value only if there is such key/value defined in array
        // Avoid overwriting property value, in case the it has a default value
        if (isset($source[$this->keyName])) {
            $destination->{$propertyName} = $source[$this->keyName];
        }
    }

    public function setOptions(Options $options): void
    {
        // TODO: Implement setOptions() method.
    }
}