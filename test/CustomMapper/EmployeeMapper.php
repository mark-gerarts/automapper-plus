<?php

namespace AutoMapperPlus\Test\CustomMapper;

use AutoMapperPlus\CustomMapper\CustomMapper;
use AutoMapperPlus\Test\Models\Employee\Employee;
use AutoMapperPlus\Test\Models\Employee\EmployeeDto;

/**
 * Class EmployeeMapper
 *
 * @package AutoMapperPlus\Test\CustomMapper
 */
class EmployeeMapper extends CustomMapper
{
    /**
     * @param Employee $source
     * @param EmployeeDto $destination
     * @return EmployeeDto
     */
    public function mapToObject($source, $destination)
    {
        $destination->id = $source->getId();
        $destination->firstName = $source->getFirstName();
        $destination->lastName = $source->getLastName();
        $destination->age = date('Y') - $source->getBirthYear();
        $destination->notes = 'Mapped by EmployeeMapper';

        return $destination;
    }
}
