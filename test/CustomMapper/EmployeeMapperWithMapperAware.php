<?php

namespace AutoMapperPlus\Test\CustomMapper;

use AutoMapperPlus\CustomMapper\CustomMapper;
use AutoMapperPlus\MappingOperation\MapperAwareOperation;
use AutoMapperPlus\MappingOperation\MapperAwareTrait;
use AutoMapperPlus\Test\Models\Employee\Employee;
use AutoMapperPlus\Test\Models\Employee\EmployeeDto;
use AutoMapperPlus\Test\Models\Nested\AddressDto;

/**
 * Class EmployeeMapper
 *
 * @package AutoMapperPlus\Test\CustomMapper
 */
class EmployeeMapperWithMapperAware extends CustomMapper implements MapperAwareOperation
{
    use MapperAwareTrait;

    /**
     * @param Employee    $source
     * @param EmployeeDto $destination
     *
     * @return EmployeeDto
     * @throws \AutoMapperPlus\Exception\UnregisteredMappingException
     */
    public function mapToObject($source, $destination)
    {
        $destination->id = $source->getId();
        $destination->firstName = $source->getFirstName();
        $destination->lastName = $source->getLastName();
        $destination->age = date('Y') - $source->getBirthYear();
        $destination->notes = 'Mapped by EmployeeMapperWithMapperAware';

        $destination->address = $this->mapper->map($source->getAddress(), AddressDto::class);

        return $destination;
    }
}
