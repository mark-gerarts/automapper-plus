<?php
/**
 * Created by PhpStorm.
 * User: Veaceslav Vasilache <veaceslav.vasilache@amdaris.com>
 * Date: 3/14/18
 * Time: 1:16 PM
 */

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\MappingOperation\DefaultMappingOperation;


/**
 * Class MapFromWithMapper
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class MapFromWithMapper extends DefaultMappingOperation
{
    /** @var callable */
    private $valueCallback;

    /** @var AutoMapperInterface */
    private $mapper;

    public function __construct(callable $valueCallback)
    {
        $this->valueCallback = $valueCallback;
    }

    /**
     * @param AutoMapperInterface $mapper
     */
    public function setMapper(AutoMapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @inheritdoc
     */
    protected function getSourceValue($source, string $propertyName)
    {
        return ($this->valueCallback)($this->mapper, $source);
    }

    /**
     * @inheritdoc
     */
    protected function canMapProperty(string $propertyName, $source): bool
    {
        // Mapping with a callback is always possible, regardless of the source
        // properties.
        return true;
    }
}