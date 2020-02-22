<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\PropertyAccessor\PropertyAccessorInterface;
use AutoMapperPlus\PropertyAccessor\PropertyReaderInterface;

class DefaultMappingOperationChild extends DefaultMappingOperation {
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    protected function getPropertyReader(): PropertyReaderInterface
    {
        return $this->propertyAccessor;
    }

};
