# Upgrade from 1.x to 2.0

## Changes to the interface

The interface has been adjusted to be more in line to the one proposed in
[symfony's AutoMapper PR](https://github.com/symfony/symfony/pull/30248). The
only difference with this PR is the fact that the context remains an array.

The idea is that you no longer need `mapToObject`. You can now just pass either
a string or an object the the `map` method, and the mapper will change its
behaviour based on the argument. This might impact you in the following cases:

- If you used `mapToObject` you should switch to using `map` instead.
`mapToObject` has been removed from the interface, but still exists on the
`AutoMapper` for BC reasons. It is deprecated and will be removed in the next
major though.
- If you implemented the `AutoMapperInterface` or the `MapperInterface`: the
`map` signature has changed, so you should update this.
- If you use a custom mapper that extends the `CustomMapper` *and* overrides
the `map` method, you should update the signature. It is encouraged to just
implement the interface instead of extending from now on.

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
