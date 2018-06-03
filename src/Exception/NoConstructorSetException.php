<?php

namespace AutoMapperPlus\Exception;

use AutoMapperPlus\Configuration\MappingInterface;

/**
 * Class NoConstructorSetException
 *
 * @package AutoMapperPlus\Exception
 */
class NoConstructorSetException extends AutoMapperPlusException
{
    /**
     * @param MappingInterface $mapping
     * @return NoConstructorSetException
     */
    public static function fromMapping(MappingInterface $mapping): self
    {
        $message = sprintf(
            'No custom constructor set for the mapping %s -> %s. Check using hasCustomConstructor() first.',
            $mapping->getSourceClassName(),
            $mapping->getDestinationClassName()
        );

        return new static($message);
    }
}
