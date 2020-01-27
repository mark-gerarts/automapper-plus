<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\MappingOperation\DefaultMappingOperation;

/**
 * Class SetTo
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class SetTo extends DefaultMappingOperation
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * SetTo constructor.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    protected function getSourceValue($source, string $propertyName)
    {
        return $this->value;
    }
}
