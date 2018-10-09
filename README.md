# AutoMapper+
An automapper for PHP inspired by [.NET's automapper](https://github.com/AutoMapper/AutoMapper).
Transfers data from one object to another, allowing custom mapping operations.

[![Build Status](https://travis-ci.org/mark-gerarts/automapper-plus.svg?branch=master)](https://travis-ci.org/mark-gerarts/automapper-plus)

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
        * [Dealing with nested mappings](#dealing-with-nested-mappings)
        * [Handling object construction](#handling-object-construction)
        * [ReverseMap](#reversemap)
        * [Copying a mapping](#copying-a-mapping)
    * [Resolving property names](#resolving-property-names)
        * [Naming conventions](#naming-conventions)
        * [Explicitly state source property](#explicitly-state-source-property)
        * [Resolving names with a callback](#resolving-names-with-a-callback)
    * [The Options object](#the-options-object)
    * [Setting the options](#setting-the-options)
        * [For the AutoMapperConfig](#for-the-automapperconfig)
        * [For the mappings](#for-the-mappings)
    * [Mapping with stdClass](#mapping-with-stdclass)
    * [The concept of object crates](#the-concept-of-object-crates)
    * [Using a custom mapper](#using-a-custom-mapper)
    * [Misc](#misc)
* [Similar libraries](#similar-libraries)
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
    
    public function __construct($id, $firstName, $lastName, $birthYear)
    {
        $this->id = $id;
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
    // While the properties are public for this example, we can map to private
    // or protected properties just the same.
    public $firstName;
    public $lastName;
    public $age;
}
```

The following snippet provides a quick overview on how the mapper can be 
configured and used:

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

**Using the static constructor:**
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
| MapTo | Maps the property to another class. Allows for [nested mappings](#dealing-with-nested-mappings). Supports both single values and collections. |
| FromProperty | Use this to explicitly state the source property name. |
| DefaultMappingOperation | Simply transfers the property, taking into account the provided naming conventions (if there are any). |
| MapFromWithMapper | Similar to MapFrom.<br>Compared to `mapFrom`, the callback has access to a instance of `AutoMapperInterface`. Define the second callback argument of `AutoMapperInterface` type. Accessible by using <br> - `Operation::mapFromWithMapper(function($source, AutoMapperInterface $mapper){ ... })`<br>- `new mapFromWithMapper(function($source, AutoMapperInterface $mapper){ ... })`

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
// Ignore this property.
$mapping->forMember('id', Operation::ignore());
// Map this property to the given class.
$mapping->forMember('employee', Operation::mapTo(EmployeeDto::class));
// Explicitly state what the property name is of the source object.
$mapping->forMember('name', Operation::fromProperty('unconventially_named_property'));
// The `FromProperty` operation can be chained with `MapTo`, allowing a
// differently named property to be mapped to a class.
$mapping->forMember(
    'address',
    Operation::fromProperty('adres')->mapTo(Address::class)
);
```

Example of using `MapFromWithMapper`:

```php
<?php

$getColorPalette = function(SimpleXMLElement $XMLElement, AutoMapperInterface $mapper) { 
    /** @var SimpleXMLElement $palette */
    $palette = $XMLElement->xpath('/product/specification/palette/colour');

    return $mapper->mapMultiple($palette, Color::class);
};

$mapping->forMember('palette', Operation::mapFromWithMapper($getColorPalette));

// Or another Example using inline function
$mapping->forMember('palette', new MapFromWithMapper(function(SimpleXMLElement $XMLElement, AutoMapperInterface $mapper) { 
    /** @var SimpleXMLElement $palette */
    $palette = $XMLElement->xpath('/product/specification/palette/colour');

    return $mapper->mapMultiple($palette, Color::class);
}));
```

You can create your own operations by implementing the 
`MappingOperationInterface`. Take a look at the
[provided implementations](https://github.com/mark-gerarts/automapper-plus/tree/master/src/MappingOperation)
for some inspiration.

If you need to have the automapper available in your operation, you can
implement the `MapperAwareInterface`, and use the `MapperAwareTrait`. The
default `MapTo` and `MapFromWithMapper` operations use these.

#### Dealing with nested mappings
Nested mappings can be registered using the `MapTo` operation. Keep in mind that the mapping for the
child class has to be registered as well.

`MapTo` supports both single entities and collections.


```php
<?php

$config->createMapping(Child::class, ChildDto::class);
$config->createMapping(Parent::class, ParentDto::class)
    ->forMember('child', Operation::mapTo(ChildDto::class));
```

#### Handling object construction
You can specify how the new destination object will be constructed (this isn't
relevant if you use `mapToObject`). You can do this by registering a *factory
callback*. This callback will be passed both the source object and an instance
of the AutoMapper.

```php
<?php

$config->registerMapping(Source::class, Destination::class)
    ->beConstructedUsing(function (Source $source, AutoMapperInterface $mapper): Destination {
        return new Destination($source->getProperty());
    });
```

Another option is to skip the constructor all together. This can be set using
the options.

```php
<?php

// Either set it in the options:
$config->getOptions()->skipConstructor();
$mapper = new AutoMapper($config);

// Or set it on the mapping directly:
$config->registerMapping(Source::class, Destination::class)->skipConstructor();
```

#### ReverseMap
Since it is a common use case to map in both directions, the `reverseMap()`
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

<!-- I feel this can be explained better. Any help appreciated! -->
**Note**: `reverseMap()` simply creates a completely new mapping in the reverse
direction, using the default options. However, every operation you defined with
`forMember` that implements the `Reversible` interface, gets defined for the new
mapping as well. Currently, only `fromProperty` supports being reversed.

To make things more clear, take a look at the following example:

```php
<?php

// Source class properties:         Destination class properties:
// - 'some_property',               - 'some_property'
// - 'some_alternative_property'    - 'some_other_property'
// - 'the_last_property'            - 'the_last_property'
//
$config->registerMapping(Source::class, Destination::class)
    ->forMember('some_property', Operation::ignore())
    ->forMember('some_other_property', Operation::fromProperty('some_alternative_property'))
    ->reverseMap();

// When mapping from Source to Destination, the following will happen:
// - some_property gets ignored
// - some_other_property gets mapped by using the value form some_alternative_property
// - the_last_property gets mapped because the names are equal.
//
// Now, when we go in the reverse direction things are different:
// - some_property gets mapped, because Ignore is not reversible
// - some_alternative_property gets mapped because FromProperty is reversible
// - the_last_property gets mapped as well
```

#### Copying a mapping
When defining different view models, it can occur that you have lots of similar
properties. For example, with a ListViewModel and a DetailViewModel. This means
that the mapping configuration will be similar as well.

For this reason, it is possible to copy a mapping. In practice this means that
all the options will be copied, and all the explicitly defined mapping
operations.

After copying the mapping, you're free to override operations or options on the
new mapping.

```php
<?php

$detailMapping = $config->registerMapping(Employee::class, EmployeeDetailView::class)
    // Define operations and options ...
    ->forMember('age', function () {
        return 20;
    });

// You can copy a mapping by passing source and destination class. This will
// search the config for the relevant mapping.
$listMapping = $config->registerMapping(Employee::class, EmployeeListView::class)
    ->copyFrom(Employee::class, EmployeeDetailView::class)
    // Alternatively, copy a mapping by passing it directly.
    // ->copyFromMapping($detailMapping)
    //
    // You can now go ahead and define new operations, or override existing
    // ones.
    ->forMember('name', Operation::ignore())
    ->skipConstructor();
```

### Resolving property names
Unless you define a specific way to fetch a value (e.g. `mapFrom`), the mapper
has to have a way to know which source property to map from. By default, it will
try to transfer data between properties of the same name. There are, however, a
few ways to alter this behaviour.

If a source property is [specifically defined](#explicitly-state-source-property)
(e.g. `FromProperty`), this will be used in all cases.

#### Naming conventions
You can specify the naming conventions followed by the source & destination 
classes. The mapper will take this into account when resolving names.

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

#### Explicitly state source property
As mentioned earlier, the operation `FromProperty` allows you to explicitly
state what property of the source object should be used.

```php
<?php

$config->registerMapping(Source::class, Destination::class)
    ->forMember('id', Operation::fromProperty('identifier'));
```

You should read the previous snippet as follows: *"For the property named 'id'
on the destination object, use the value of the 'identifier' property of the
source object"*.

`FromProperty` is `Reversible`, meaning that when you apply `reverseMap()`,
AutoMapper will know how to map between the two properties. For more info, read
the [section about `reverseMap`](#reversemap).

#### Resolving names with a callback
Should naming conventions and explicitly stating property names not be 
sufficient, you can resort to a `CallbackNameResolver` (or implement your own
`NameResolverInterface`).

This `CallbackNameResolver` takes a callback as an argument, and will use this 
to transform property names.

```php
<?php

class Uppercase
{
    public $IMAPROPERTY;
}

class Lowercase
{
    public $imaproperty;
}

$uppercaseResolver = new CallbackNameResolver(function ($targetProperty) {
    return strtolower($targetProperty);
});

$config->registerMapping(Uppercase::class; Lowercase::class)
    ->withNameResolver($uppercaseResolver);

$uc = new Uppercase();
$uc->IMAPROPERTY = 'value';

$lc = $mapper->map($uc, Lowercase::class);
echo $lc->imaproperty; // => "value"
```

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
| Default name resolver | `NameResolver` | The default class to resolve property names |
| Custom Mapper | `null` | Grants the ability to use a [custom mapper](#using-a-custom-mapper). |
| Object crates | `[\stdClass::class]` | See [the dedicated section](#the-concept-of-object-crates). |

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
methods exist for more convenient overriding of the options:

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

### Mapping with stdClass
As a side note it is worth mentioning that it is possible to map from and to
`stdClass`. Mapping *from* `stdClass` happens as you would expect, copying
properties to the new object.

```php
<?php

// Register the mapping.
$config->registerMapping(\stdClass::class, Employee::class);
$mapper = new AutoMapper($config);

$employee = new \stdClass();
$employee->firstName = 'John';
$employee->lastName = 'Doe';

$result = $mapper->map($employee, Employee::class);
echo $result->firstName; // => "John"
echo $result->lastName; // => "Doe"
```

Mapping *to* `\stdClass` requires some explanation. All properties available on
the provided source object are copied to the `\stdClass` as public properties.
It's still possible to define operations for individual properties (for example,
to ignore a property).

```php
<?php

// Operations can still be registered.
$config->registerMapping(Employee::class, \stdClass::class)
    ->forMember('id', Operation::ignore());
$mapper = new AutoMapper($config);

$employee = new Employee(5, 'John', 'Doe', 1978);
$result = $mapper->map($employee, \stdClass::class);

echo $result->firstName; // => "John"
echo $result->lastName; // => "Doe"
var_dump(isset($result->id)); // => bool(false)
```

Naming conventions will be taken into account, so keep this in mind when
defining operations. The property name has to match the naming convention of the
target.

```php
<?php

$config->registerMapping(CamelCaseSource::class, \stdClass::class)
    ->withNamingConventions(
        new CamelCaseNamingConvention(),
        new SnakeCaseNamingConvention()
    )
    // Operations have to be defined using the target property name.
    ->forMember('some_property', function () { return 'new value'; });
$mapper = new AutoMapper($config);

$source = new CamelCaseSource();
$source->someProperty = 'original value';
$source->anotherProperty = 'Another value';
$result = $mapper->map($employee, \stdClass::class);

var_dump(isset($result->someProperty)); // => bool(false)
echo $result->some_property; // => "new value"
echo $result->another_property; // => "Another value"
```

### The concept of object crates
As suggested and explained in [this issue](https://github.com/mark-gerarts/automapper-plus/issues/3),
AutoMapper+ uses *object crates* to allow mapping to `\stdClass`. This means you
can register your own classes as well to be an object crate. This makes the
mapper handle it exactly as `\stdClass`, writing all source properties to public
properties on the target.

Registering object crates can be done using the `Options`.

```php
<?php

class YourObjectCrate { }

$config = new AutoMapperConfig(); // (Or pass a callable to the constructor)
$config->getOptions()->registerObjectCrate(YourObjectCrate::class);
$config->registerMapping(Employee::class, YourObjectCrate::class);
$mapper = new AutoMapper($config);

$employee = new Employee(5, 'John', 'Doe', 1978);
$result = $mapper->map($employee, YourObjectCrate::class);

echo $result->firstName; // => "John"
echo $result->lastName; // => "Doe"
echo get_class($result); // => "YourObjectCrate"
```

### Using a custom mapper
This library attempts to make registering mappings painless, with as little 
configuration as possible. However, cases exist where a mapping requires a lot
of custom code. This code would look a lot cleaner if put in its own class.
Another reason to resort to a custom mapper would be [performance](#similar-libraries).

It is therefore possible to specify a custom mapper class for a mapping. This
mapper has to implement the `MapperInterface`. For your convenience, a
`CustomMapper` class has been provided that implements this interface.

```php
<?php

// You can either extend the CustomMapper, or just implement the MapperInterface
// directly.
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

        return $destination;
    }
}

$config->registerMapping(Employee::class, EmployeeDto::class)
    ->useCustomMapper(new EmployeeMapper());
$mapper = new AutoMapper($config);

// The AutoMapper can now be used as usual, but your custom mapper class will be
// called to do the actual mapping.
$employee = new Employee(10, 'John', 'Doe', 1980);
$result = $mapper->map($employee, EmployeeDto::class);
```

### Misc

- Passing `NULL` as an argument for the source object to `map` returns `NULL`.

## Similar libraries
When picking a library, it's important to see what options are available. No
library is perfect, and they all have their pro's and con's.

A few other object mappers exist for PHP. They're listed here with a short
description, and are definitely worth checking out!

- **[Nylle/PHP-AutoMapper](https://github.com/Nylle/PHP-AutoMapper):**
    - Only maps public properties
    - Requires some conventions to be met
    - Does some interesting stuff with types
- **[Papper](https://github.com/idr0id/Papper):**
    - Convention based
    - High performance
    - Lacks in documentation
- **[BCCAutoMapperBundle](https://github.com/michelsalib/BCCAutoMapperBundle):**
    - Only available as a Symfony bundle (<3.0)
    - Very similar to this project
    - Does some cool stuff with graph mapping

Performance benchmarks (credit goes to [idr0id](https://github.com/idr0id/php-mappers-benchmarks)):

Runtime: PHP 7.1.8-1<br>
Host: Linux 4.13.0-1-amd64 #1 SMP Debian 4.13.4-2 (2017-10-15) x86_64<br>
Collection size: 10000

| package                       | duration (MS) | MEM (B)  |
| ----------------------------- | ------------- | -------- |
| native php                    | 3             | 6291456  |
| idr0id/papper                 | 39            | 6291456  |
| trismegiste/alkahest          | 116           | 16777216 |
| nylle/php-automapper          | 188           | 16777216 |
| mark-gerarts/auto-mapper-plus | 291           | 16777216 |
| bcc/auto-mapper-bundle        | 716           | 6291456  |

*Up-to-date benchmarks can be found [here](https://travis-ci.org/idr0id/php-mappers-benchmarks).*

## See also
- [The Symfony bundle](https://github.com/mark-gerarts/automapper-plus-bundle)
- [The Symfony demo app (WIP)](https://github.com/mark-gerarts/automapper-plus-demo-app)

## Roadmap
- [x] Provide a more detailed tutorial
- [x] Create a sample app demonstrating the automapper
- [x] Allow mapping from `stdClass`,
- [ ] or perhaps even an associative array (could have)
- [x] Allow mapping to `stdClass`
- [x] Provide options to copy a mapping
- [ ] Allow setting of prefix for name resolver (see [automapper](https://github.com/AutoMapper/AutoMapper/wiki/Configuration#recognizing-prepostfixes))
- [x] Create operation to copy value from property
- [x] Allow passing of contructor function
- [ ] Allow configuring of options in AutoMapperConfig -> error when trying with a registered mapping
- [ ] Consider: passing of options to a single mapping operation
- [x] MapTo: allow mapping of collection
- [ ] Clean up the property checking in the Mapping::forMember() method.
- [ ] Refactor tests
- [ ] Allow setting a maximum depth, see #14
- [ ] Provide a NameResolver that accepts an array mapping, as an alternative to multiple `FromProperty`s
