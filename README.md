# AutoMapper+
An automapper for PHP inspired by [.NET's automapper](https://github.com/AutoMapper/AutoMapper).
Transfers data from one object to another, allowing custom mapping operations.

## Table of Contents
* [Installation](#installation)
* [Why?](#why)
* [Example usage](#example-usage)
* [In depth](#in-depth)
    * [Instantiating the AutoMapper](#instantiating-the-automapper)
    * [Using the AutoMapper](#using-the-automapper)
    * [Registering mappings](#registering-mappings)
        * [Custom callbacks](#custom-callbacks)
        * [Operations](#operations)
        * [Naming conventions](#naming-conventions)
        * [ReverseMap](#reversemap)
    * [The Options object](#the-options-object)
    * [Setting the options](#setting-the-options)
        * [For the AutoMapperConfig](#for-the-automapperconfig)
        * [For the mappings](#for-the-mappings)
* [See also](#see-also)
* [Roadmap](#roadmap)

## Installation
This library is available on [packagist](https://packagist.org/packages/mark-gerarts/auto-mapper-plus):

```bash
$ composer require "mark-gerarts/auto-mapper-plus"
```

If you're using Symfony, check out the [AutoMapper+ bundle](https://github.com/mark-gerarts/automapper-plus-bundle).

## Why?
When you need to transfer data from one object to another, you'll have to write 
a lot of boilerplate code. For example when using view models, CommandBus 
commands, working with API responses, etc.

Automapper+ helps you by automatically transferring properties from one object 
to another, **including private ones**. By default, properties with the same name
will be transferred. This can be overridden as you like.

## Example usage
Suppose you have a class Employee and an associated DTO.

```php
<?php

class Employee
{
    private $id;
    private $firstName;
    private $lastName;
    private $birthYear;
    
    public function __construct($firstName, $lastName, $birthYear)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->birthYear = $birthYear;
    }

    public function getId()
    {
        return $this->id;
    }

    // And so on...
}

class EmployeeDto
{
    public $firstName;
    public $lastName;
    public $age;
}
```

The following snippet provides a quick overview on how the mapper can be 
configured and used.

```php
<?php

use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\AutoMapper;

$config = new AutoMapperConfig();

// Simply registering the mapping is enough to convert properties with the same
// name. Custom actions can be registered for each individual property.
$config
    ->registerMapping(Employee::class, EmployeeDto::class)
    ->forMember('age', function (Employee $source) {
        return date('Y') - $source->getBirthYear();
    })
    ->reverseMap(); // Register the reverse mapping as well.
                            
$mapper = new AutoMapper($config);

// With this configuration we can start converting our objects.
$john = new Employee(10, "John", "Doe", 1980);
$dto = $mapper->map($john, EmployeeDto::class);

echo $dto->firstName; // => "John"
echo $dto->lastName; // => "Doe"
echo $dto->age; // => 37
```

## In depth

### Instantiating the AutoMapper
The AutoMapper has to be provided with an `AutoMapperConfig`, which holds the
registered mappings. This can be done in 2 ways:

**Passing it to the constructor:**
```php
<?php

use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\AutoMapper;

$config = new AutoMapperConfig();
$config->registerMapping(Source::class, Destination::class);
$mapper = new AutoMapper($config);
```

**Using the static constructor**
```php
<?php

$mapper = AutoMapper::initialize(function (AutoMapperConfig $config) {
    $config->registerMapping(Source::class, Destination::class);
    $config->registerMapping(AnotherSource::class, Destination::class);
    // ...
});
```

### Using the AutoMapper
Once configured, using the AutoMapper is pretty straightforward:

```php
<?php

$john = new Employee("John", "Doe", 1980);

// Map the source object to a new instance of the destination class.
$mapper->map($john, EmployeeDto::class);

// Mapping to an existing object is possible as well.
$mapper->mapToObject($john, new EmployeeDto());

// Map a collection using mapMultiple
$mapper->mapMultiple($employees, EmployeeDto::class);
```

### Registering mappings
Mappings are defined using the `AutoMapperConfig`'s `registerMapping()` method.
Every mapping has to be explicitly defined before you can use it.

A mapping is defined by providing the source class and the destination class.
The most basic definition would be as follows:

```php
<?php

$config->registerMapping(Employee::class, EmployeeDto::class);
```

This will allow objects of the `Employee` class to be mapped to `EmployeeDto`
instances. Since no extra configuration is provided, the mapping will only 
transfer properties with the same name.

#### Custom callbacks
With the `forMember()` method, you can specify what should happen for the given
property of the destination class. When you pass a callback to this method, the
return value will be used to set the property.

The callback receives the source object as argument.

```php
<?php

$config->registerMapping(Employee::class, EmployeeDto::class)
    ->forMember('fullName', function (Employee $source) {
        return $source->getFirstName() . ' ' . $source->getLastName();
    });
```

#### Operations
Behind the scenes, the callback in the previous example is wrapped in a 
`MapFrom` operation. Operations represent the action that should be performed
for the given property.

The following operations are provided:

| Name  | Explanation |
| ------------- | ------------- |
| MapFrom | Maps the property from the value returned from the provided callback. |
| Ignore | Ignores the property. |
| MapTo | Maps the property to another class. Allows for nested mappings. |
| DefaultMappingOperation | Simply transfers the property, taking into account the provided naming conventions (if there are any). |

You can use them with the same `forMember()` method. The `Operation` class can
be used for clarity.

```php
<?php

$getName = function() { return 'John'; };

$mapping->forMember('name', $getName);
// The above is a shortcut for the following:
$mapping->forMember('name', Operation::mapFrom($getName));
// Which in turn is equivalent to:
$mapping->forMember('name', new MapFrom($getName));

// Other examples:
$mapping->forMember('id', Operation::ignore());
$mapping->forMember('employee', Operation::mapTo(EmployeeDto::class));
```

You can create your own operations by implementing the 
`MappingOperationInterface`. Take a look at the
[provided implementations](https://github.com/mark-gerarts/automapper-plus/tree/master/src/MappingOperation)
for some inspiration.

#### Naming conventions
By default, a mapping will try to transfer data between properties of the same
name. You can, however, specify the naming conventions followed by the source &
destination classes. The mapper will take this into account.

For example:

```php
<?php

use AutoMapperPlus\NameConverter\NamingConvention\CamelCaseNamingConvention;
use AutoMapperPlus\NameConverter\NamingConvention\SnakeCaseNamingConvention;

$config->registerMapping(CamelCaseSource::class, SnakeCaseDestination::class)
    ->withNamingConventions(
        new CamelCaseNamingConvention(), // The naming convention of the source class.
        new SnakeCaseNamingConvention() // The naming convention of the destination class.
    );

$source = new CamelCaseSource();
$source->propertyName = 'camel';

$result = $mapper->map($source, SnakeCaseDestination::class);
echo $result->property_name; // => "camel"
```

The following conventions are provided (more to come):
- CamelCaseNamingConvention
- PascalCaseNamingConvention
- SnakeCaseNamingConvention

You can implement your own by using the `NamingConventionInterface`.

#### ReverseMap
Since it is a common usecase to map in both directions, the `reverseMap()`
method has been provided. This creates a new mapping in the alternate direction.

`reverseMap` will keep the registered naming conventions into account, if there
are any.

```php
<?php

// reverseMap() returns the new mapping, allowing to continue configuring the 
// new mapping.
$config->registerMapping(Employee::class, EmployeeDto::class)
    ->reverseMap()
    ->forMember('id', Operation::ignore());

$config->hasMappingFor(Employee::class, EmployeeDto::class); // => True
$config->hasMappingFor(EmployeeDto::class, Employee::class); // => True
```

**Note**: `reverseMap()` simply creates a completely new mapping in the reverse 
direction, using the default options. 

### The Options object
The `Options` object is a value object containing the possible options for both
the `AutoMapperConfig` and the `Mapping` instances.

The `Options` you set for the `AutoMapperConfig` will act as the default options
for every `Mapping` you register. These options can be overridden for every
mapping.

For example:

```php
<?php

$config = new AutoMapperConfig();
$config->getOptions()->setDefaultMappingOperation(Operation::ignore());

$defaultMapping = $config->registerMapping(Source::class, Destination::class);
$overriddenMapping = $config->registerMapping(AnotherSource::class, Destination::class)
    ->withDefaultOperation(new DefaultMappingOperation());

$defaultMapping->getOptions()->getDefaultMappingOperation(); // => Ignore
$overriddenMapping->getOptions()->getDefaultMappingOperation(); // => DefaultMappingOperation
```

The available options that can be set are:

| Name  | Default value | Comments |
| ------------- | ------------- | ------------- |
| Source naming convention | `null` | The naming convention of the source class (e.g. `CamelCaseNamingConversion`). Also see [naming conventions](#naming-conventions). |
| Destination naming convention | `null` | See above. |
| Skip constructor | `true` | whether or not the constructor should be skipped when instantiating a new class. Use `$options->skipConstructor()` and `$options->dontSkipConstructor()` to change. |
| Property accessor | `PropertyAccessor` | Use this to provide an alternative implementation of the property accessor. |
| Default mapping operation | `DefaultMappingOperation` | the default operation used when mapping a property. Also see [mapping operations](#operations) |

### Setting the options

#### For the AutoMapperConfig
You can set the options for the `AutoMapperConfig` by retrieving the object:

```php
<?php

$config = new AutoMapperConfig();
$config->getOptions()->dontSkipConstructor();
```

Alternatively, you can set the options by providing a callback to the 
constructor. The callback will be passed an instance of the default `Options`:

```php
<?php

// This will set the options for this specific mapping.
$config = new AutoMapperConfig(function (Options $options) {
    $options->dontSkipConstructor();
    $options->setDefaultMappingOperation(Operation::ignore());
    // ...
});
```

#### For the Mappings
A mapping also has the `getOptions` method available. However, chainable helper 
methods exists for more convenient overriding of the options:

```php
<?php

$config->registerMapping(Source::class, Destination::class)
    ->skipConstructor()
    ->withDefaultOperation(Operation::ignore());
```

Setting options via a callable has been provided for mappings as well, using the
`setDefaults()` method:

```php
<?php

$config->registerMapping(Source::class, Destination::class)
    ->setDefaults(function (Options $options) {
        $options->dontSkipConstructor();
        // ...
    });
```

## See also
- [The Symfony bundle](https://github.com/mark-gerarts/automapper-plus-bundle)
- [The Symfony demo app (WIP)](https://github.com/mark-gerarts/automapper-plus-demo-app)

## Roadmap
- [x] Provide a more detailed tutorial
- [ ] Create a sample app demonstrating the automapper
- [ ] Provide options to copy a mapping
- [ ] Allow setting of prefix for name resolver (see [automapper](https://github.com/AutoMapper/AutoMapper/wiki/Configuration#recognizing-prepostfixes))
- [ ] Create operation to copy value from property
- [ ] Allow configuring of options in AutoMapperConfig -> error when trying with a registered mapping
- [ ] Consider: passing of options to a single mapping operation
- [ ] MapTo: allow mapping of collection
