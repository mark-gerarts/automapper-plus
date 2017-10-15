<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\Configuration\AutoMapperConfigInterface;

/**
 * Class MapTo
 *
 * @package AutoMapperPlus\MappingOperation
 */
class MapTo implements MappingOperationInterface
{
    /**
     * @var string
     */
    private $destinationClassName;

    /**
     * MapTo constructor.
     *
     * @param string $destinationClassName
     */
    public function __construct(string $destinationClassName)
    {
        $this->destinationClassName = $destinationClassName;
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
    ): void
    {

    }
}
