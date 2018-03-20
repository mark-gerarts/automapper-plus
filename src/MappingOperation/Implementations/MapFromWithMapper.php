<?php
/**
 * Created by PhpStorm.
 * User: Veaceslav Vasilache <veaceslav.vasilache@gmail.com>
 * Date: 3/14/18
 * Time: 1:16 PM
 */

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\AutoMapperInterface;

/**
 * Class MapFromWithMapper
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class MapFromWithMapper extends MapFrom
{
    /** @var AutoMapperInterface */
    private $mapper;

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
        return ($this->valueCallback)($source, $this->mapper);
    }
}