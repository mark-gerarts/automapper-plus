<?php

namespace AutoMapperPlus;

use AutoMapperPlus\Exception\UnregisteredMappingException;

/**
 * Interface ObjectMapperInterface
 *
 * @package AutoMapperPlus
 */
interface DestinationMapperInterface
{
    /**
     * Maps properties of object $from to an existing object $to.
     *
     * @param array|object $source
     *   The source object.
     * @param object $destination
     *   The target object.
     * @param array $context
     *   See MapperInterface::map()
     * @return mixed
     *   $to, with properties copied from $from.
     * @throws UnregisteredMappingException
     */
    public function mapToObject($source, $destination/**, array $context = [] */);
}
