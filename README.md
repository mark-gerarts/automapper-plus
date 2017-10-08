# AutoMapper+
An automapper for PHP inspired by [.NET's automapper](https://github.com/AutoMapper/AutoMapper).
Transfers data from one object to another, allowing custom mapping operations.

## Table of Contents
* [Installation](#installation)
* [Why?](#why)
* [Usage](#usage)
    * [Basic example](#basic-example)
    * [Custom callbacks](#custom-callbacks)
    * [Operations](#operations)
    * [Changing the defaults](#changing-the-defaults)
    * [Instantiating using the static constructor](#instantiating-using-the-static-constructor)
    * [Mapping to an existing object](#mapping-to-an-existing-object)
* [Roadmap](#roadmap)

## Installation
This library is available via composer:

```bash
$ composer require "mark-gerarts/auto-mapper-plus"
```

## Why?
When you need to transfer data from one object to another, you'll have to write 
a lot of boilerplate code. For example when using view models, CommandBus 
commands, response classes, etc.

Automapper+ helps you by automatically transferring properties from one object 
to another, **including private ones**. By default, properties with the same name
will be transferred. This can be overridden as you like.

## Usage

### Basic example
Suppose you have a class Employee for which you want to create a DTO.

```php
<?php

class Employee
{
    private $id;
    private $firstName;
    private $lastName;
    
    public function __construct($id, $firstName, $lastName)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
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
}
```

The most basic thing AutoMapper+ can do, is transferring properties with the
same name. After registering the mapping, there's no additional configuration
needed to do this.

```php
<?php

use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\AutoMapper;

$config = new AutoMapperConfig();
// If we only need to convert properties with the same name, simply registering
// the mapping is enough.
$config->registerMapping(Employee::class, EmployeeDto::class);
$mapper = new AutoMapper($config);

// With this configuration we can start converting our objects.
$john = new Employee(10, "John", "Doe");
$dto = $mapper->map($john, EmployeeDto::class);

echo $dto->firstName; // => "John"
echo $dto->lastName; // => "Doe"
```

### Custom callbacks
If you need something more complex than transferring properties with the same
name, you can provide a custom callback. This can be done on a per-property
basis with the `forMember` method:

```php
<?php

class EmployeeDto
{
    public $fullName;
}

$config = new \AutoMapperPlus\Configuration\AutoMapperConfig();
$config->registerMapping(Employee::class, EmployeeDto::class)
    ->forMember('fullName', function (Employee $source) {
        // You can put some custom conversions here.
        return $source->getFirstName() . ' ' . $source->getLastName();
    });

echo $mapper->map($john, EmployeeDto::class)->fullName; // => "John Doe"
```

The function passed will receive the following arguments:
- The source object
- The destination object
- The target property name
- The configuration object

### Operations
Behind the scenes, the callback in the previous examples is wrapped in an
`Operation`. Some default operations have been provided. For example, the
`Ignore` operation, which leaves the destination property alone:

```php
<?php

use AutoMapperPlus\MappingOperation\Operation;

$config->registerMapping(Employee::class, EmployeeDto::class)
    ->forMember('fullName', Operation::ignore());
```

The available operations are `mapFrom`, `ignore` and `getProperty`. You can
create your own by implementing the `MappingOperationInterface`.

### Changing the defaults
By default, properties with the same name will be transferred. You can change
this behaviour by passing it as the first argument to the `AutoMapperConfig`.
Classes to convert snake_case to camelCase and vice versa have been provided.
You can write your own by implementing the `NameResolverInterface`.

```php
<?php

use AutoMapperPlus\NameResolver\SnakeCaseToCamelCaseResolver;

$config = new AutoMapperConfig(new SnakeCaseToCamelCaseResolver());
```

### Instantiating using the static constructor
An alternative way to initialize the mapper is using the `::initialize` static
method. This allows you to configure the mapper using a callback.

```php
<?php

use AutoMapperPlus\Configuration\AutoMapperConfigInterface;

$mapper = AutoMapper::initialize(function (AutoMapperConfigInterface $config) {
    $config->registerMapping(Employee::class, EmployeeDto::class);
});
```

### Mapping to an existing object

By default, the method `map` creates a new instance of the target class. It is
possible to map to an existing object using the `mapToObject` method.

```php
<?php

$employee = new Employee();
$viewModel = new EmployeeDto();
$mapper->mapToObject($employee, $viewModel);
```

## Roadmap
- [x] Add tests
- [x] Add PHP7.1 dependency to composer
- [x] Add comments (more than just PHPDoc blocks)
- [x] Add the ability to change the name resolver
- [ ] Make `GetProperty` use the config's resolver if none provided
- [x] Add the 'Operation' type, @see Mapping.php
- [x] Allow transferring data to an existing object
- [ ] Copy as many usages from .net's automapper as possible
- [ ] Provide a more detailed tutorial
- [ ] Create a Symfony bundle
- [ ] Create a sample app demonstrating the automapper
- [ ] Check if Reflectionclass PrivateAccessor implementation is faster (https://gist.github.com/samsamm777/7230159)
