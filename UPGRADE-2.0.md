# Upgrade from 1.x to 2.0

## Objects are now passed by reference

To be able to map to arrays, some parameters are being passed by reference now.
If you extended some of these classes, or implemented the interfaces, you
should update your code.

- `MappingOperationInterface::mapProperty(string $propertyName, $source, &$destination)`
- `MappingOperationInterface::setDestinationValue($destination, &$destination, string $propertyName, $value)`
- `MappingOperationInterface::mapProperty(string $propertyName, $source, &$destination)`
- `PropertyWriterInterface::setProperty(&$object, string $propertyName, $value)`

Affected classes are all your custom mapping operations or property writers.

## Context parameters

The context was previously not part of the function specification of some 
classes. These have been updates to make this explicit.

- `AutoMapper`
- `CustomMapper`
- `MapperInterface`
- `AutoMapperInterface`

## Removed deprecations

- `DefaultMappingOperation::getPropertyAccessor` has been split out in `getPropertyReader` and `getPropertyWriter`
- `MapFromWithMapper` has been replaced. The regular `MapFrom` operation now has the same behaviour
