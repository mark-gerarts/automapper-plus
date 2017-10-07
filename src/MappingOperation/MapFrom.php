<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\Configuration\AutoMapperConfigInterface;

/**
 * Class MapFrom
 *
 * @package AutoMapperPlus\MappingOperation
 */
class MapFrom implements MappingOperationInterface
{
    /**
     * @var callable
     */
    private $mappingCallback;

    /**
     * MapFrom constructor.
     *
     * @param callable $mappingCallback
     */
    function __construct(callable $mappingCallback)
    {
        $this->mappingCallback = $mappingCallback;
    }

    /**
     * @inheritdoc
     */
    public function __invoke
    (
        $from,
        $to,
        string $propertyName,
        AutoMapperConfigInterface $config
    ):void
    {
        $callback = $this->mappingCallback;
        $to->{$propertyName} = $callback($from, $to, $propertyName, $config);
    }
}
