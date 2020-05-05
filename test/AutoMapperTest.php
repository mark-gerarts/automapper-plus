<?php

namespace AutoMapperPlus;

use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\Exception\UnregisteredMappingException;
use AutoMapperPlus\Test\CustomMapper\EmployeeMapper;
use AutoMapperPlus\MappingOperation\Operation;
use AutoMapperPlus\NameConverter\NamingConvention\CamelCaseNamingConvention;
use AutoMapperPlus\NameConverter\NamingConvention\SnakeCaseNamingConvention;
use AutoMapperPlus\NameResolver\CallbackNameResolver;
use AutoMapperPlus\Test\CustomMapper\EmployeeMapperWithMapperAware;
use AutoMapperPlus\Test\Models\Inheritance\DestinationChild;
use AutoMapperPlus\Test\Models\Inheritance\DestinationParent;
use AutoMapperPlus\Test\Models\Inheritance\SourceChild;
use AutoMapperPlus\Test\Models\Inheritance\SourceParent;
use AutoMapperPlus\Test\Models\Issues\Issue33\User;
use AutoMapperPlus\Test\Models\Issues\Issue33\UserDto;
use AutoMapperPlus\Test\Models\Nested\Address;
use AutoMapperPlus\Test\Models\Nested\AddressDto;
use AutoMapperPlus\Test\Models\Nested\Person;
use AutoMapperPlus\Test\Models\Nested\PersonDto;
use AutoMapperPlus\Test\Models\SimpleProperties\HasPrivateProperties;
use AutoMapperPlus\Test\Models\SimpleProperties\NoProperties;
use PHPUnit\Framework\TestCase;
use AutoMapperPlus\Test\Models\Employee\Employee;
use AutoMapperPlus\Test\Models\Employee\EmployeeDto;
use AutoMapperPlus\Test\Models\NamingConventions\CamelCaseSource;
use AutoMapperPlus\Test\Models\NamingConventions\SnakeCaseSource;
use AutoMapperPlus\Test\Models\Nested\ChildClass;
use AutoMapperPlus\Test\Models\Nested\ChildClassDto;
use AutoMapperPlus\Test\Models\Nested\ParentClass;
use AutoMapperPlus\Test\Models\Nested\ParentClassDto;
use AutoMapperPlus\Test\Models\Post\CreatePostViewModel;
use AutoMapperPlus\Test\Models\Post\Post;
use AutoMapperPlus\Test\Models\Prefix\PrefixedSource;
use AutoMapperPlus\Test\Models\Prefix\UnPrefixedSource;
use AutoMapperPlus\Test\Models\SimpleProperties\Destination;
use AutoMapperPlus\Test\Models\SimpleProperties\Source;
use AutoMapperPlus\Test\Models\SpecialConstructor\Source as ConstructorSource;
use AutoMapperPlus\Test\Models\SpecialConstructor\Destination as ConstructorDestination;
use AutoMapperPlus\Test\Models\Visibility\Visibility;
use AutoMapperPlus\Test\Models\SimilarPropertyNames\Source as SimilarSource;
use AutoMapperPlus\Test\Models\SimilarPropertyNames\Destination as SimilarDestination;
use AutoMapperPlus\Exception\InvalidArgumentException;

/**
 * Class AutoMapperTest
 *
 * @package AutoMapperPlus
 */
class AutoMapperTest extends TestCase
{
    protected $source;
    protected $destination;

    /**
     * @var AutoMapperConfig
     */
    protected $config;

    protected function setUp()
    {
        $this->config = new AutoMapperConfig();
    }

    public function testItCanBeInstantiatedStatically()
    {
        $mapper = AutoMapper::initialize(function (AutoMapperConfigInterface $config) {
            $config->registerMapping(Source::class, Destination::class);
        });

        $destination = $mapper->map(new Source(), Destination::class);
        $this->assertInstanceOf(Destination::class, $destination);
    }

    public function testItMapsAPublicProperty()
    {
        $this->config->registerMapping(Source::class, Destination::class);
        $mapper = new AutoMapper($this->config);
        $source = new Source();
        $source->name = 'Hello';
        /** @var Destination $destination */
        $destination = $mapper->map($source, Destination::class);

        $this->assertInstanceOf(Destination::class, $destination);
        $this->assertEquals($source->name, $destination->name);
    }

    public function testItCanMapToAnExistingObject()
    {
        $this->config->registerMapping(Source::class, Destination::class);
        $mapper = new AutoMapper($this->config);

        $source = new Source();
        $source->name = 'Hello';
        $destination = new Destination();
        $destination->anotherProperty = 'another';
        $destination = $mapper->mapToObject($source, $destination);

        $this->assertEquals($source->name, $destination->name);
        // Make sure we don't override other properties.
        $this->assertEquals('another', $destination->anotherProperty);
    }

    public function testMapToAnExistingObjectContext()
    {
        $source = new Source();
        $source->name = 'Hello';
        $destination = new Destination();
        $destination->anotherProperty = 'another';

        $this->config
            ->registerMapping(Source::class, Destination::class)
            ->forMember('name', Operation::mapFrom(function(Source $source, AutoMapperInterface $mapper, array $context) use ($destination) {
                $this->assertArrayHasKey(AutoMapper::DESTINATION_CONTEXT, $context);
                $this->assertInstanceOf(Destination::class, $context[AutoMapper::DESTINATION_CONTEXT]);
                $this->assertEquals($destination, $context[AutoMapper::DESTINATION_CONTEXT]);

                return $source->name;
            }))
        ;
        $mapper = new AutoMapper($this->config);
        $mapper->mapToObject($source, $destination);
    }

    public function testSourceDoesntGetOverridden()
    {
        $this->config->registerMapping(Source::class, Destination::class);
        $mapper = new AutoMapper($this->config);

        $source = new Source();
        $source->name = 'John';
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('John', $source->name);
    }

    public function testItCanMapWithACallback()
    {
        $this->config->registerMapping(Source::class, Destination::class)
            ->forMember('name', function () {
                return 'NewName';
            });
        $mapper = new AutoMapper($this->config);
        $source = new Source();
        $destination = $mapper->map($source, Destination::class);

        $this->assertEquals('NewName', $destination->name);
    }

    public function testTheConfigurationCanBeRetrieved()
    {
        $config = new AutoMapperConfig();
        $mapper = new AutoMapper($config);

        $this->assertEquals($config, $mapper->getConfiguration());
    }

    public function testItCanMapMultiple()
    {
        $this->config->registerMapping(Source::class, Destination::class);
        $mapper = new AutoMapper($this->config);

        $sourceCollection = [
            new Source('One'),
            new Source('Two'),
            new Source('Three')
        ];

        $destinationCollection = [
            new Destination('One'),
            new Destination('Two'),
            new Destination('Three')
        ];

        $this->assertEquals(
            $destinationCollection,
            $mapper->mapMultiple($sourceCollection, Destination::class)
        );

        // Now let's see if we can handle traversables.
        $traversable = new \ArrayIterator($sourceCollection);
        $this->assertEquals(
            $destinationCollection,
            $mapper->mapMultiple($traversable, Destination::class)
        );
    }

    public function testItCanMapToAnObjectWithLessProperties()
    {
        $this->config->registerMapping(
            CreatePostViewModel::class,
            Post::class
        );
        $mapper = new AutoMapper($this->config);

        $source = new CreatePostViewModel();
        $source->title = 'Im a title';
        $source->body = 'Im a body';

        $expected = new Post(null, 'Im a title', 'Im a body');
        $this->assertEquals($expected, $mapper->map($source, Post::class));
    }

    public function testItCanSkipTheConstructor()
    {
        $this->config->registerMapping(
            ConstructorSource::class,
            ConstructorDestination::class
        );
        $mapper = new AutoMapper($this->config);

        $source = new ConstructorSource();
        /** @var ConstructorDestination $result */
        $result = $mapper->map($source, ConstructorDestination::class);

        $this->assertFalse($result->constructorRan);
    }

    public function testItCanMapNestedProperties()
    {
        $this->config->registerMapping(ChildClass::class, ChildClassDto::class);
        $this->config->registerMapping(ParentClass::class, ParentClassDto::class)
            ->forMember('child', Operation::mapTo(ChildClassDto::class));
        $mapper = new AutoMapper($this->config);

        $parent = new ParentClass();
        $child = new ChildClass();
        $child->name = 'ChildName';
        $parent->child = $child;

        $result = $mapper->map($parent, ParentClassDto::class);

        $this->assertInstanceOf(ChildClassDto::class, $result->child);
        $this->assertEquals('ChildName', $result->child->name);
    }

    public function testItCanResolveNamingConventions()
    {
        $this->config->registerMapping(CamelCaseSource::class, SnakeCaseSource::class)
            ->withNamingConventions(
                new CamelCaseNamingConvention(),
                new SnakeCaseNamingConvention()
            )
            // Test if fromProperty overrides the naming convention.
            ->forMember('some_other_property', Operation::fromProperty('anotherProperty'))
            ->reverseMap();
        $mapper = new AutoMapper($this->config);

        $camel = new CamelCaseSource();
        $camel->propertyName = 'camel';
        $camel->anotherProperty = 'someOther';

        /** @var SnakeCaseSource $snake */
        $snake = $mapper->map($camel, SnakeCaseSource::class);

        $this->assertEquals('camel', $snake->property_name);
        $this->assertEquals('someOther', $snake->some_other_property);

        // Let's try the reverse as well.
        $snake->property_name = 'snake';
        $snake->some_other_property = 'snakeprop';
        $camel = $mapper->map($snake, CamelCaseSource::class);

        $this->assertEquals('snake', $camel->propertyName);
        // Let's see if reverseMap() takes fromProperty into account.
        $this->assertEquals('snakeprop', $camel->anotherProperty);
    }

    /**
     * @group stdClass
     */
    public function testItCanMapFromAStdClass()
    {
        $this->config->registerMapping(\stdClass::class, Destination::class);
        $mapper = new AutoMapper($this->config);

        $source = new \stdClass();
        $source->name = 'sourceName';
        /** @var Destination $destination */
        $destination = $mapper->map($source, Destination::class);

        $this->assertEquals('sourceName', $destination->name);
    }

    /**
     * Will test if we can map by providing the source property name.
     */
    public function testItMapsFromAProperty()
    {
        $this->config->registerMapping(Visibility::class, Destination::class)
            ->forMember('name', Operation::fromProperty('privateProperty'));
        $mapper = new AutoMapper($this->config);

        $source = new Visibility();
        $result = $mapper->map($source, Destination::class);

        $this->assertTrue($result->name);
    }

    public function testItCanSetARestrictedProperties()
    {
        $this->config->registerMapping(\stdClass::class, Visibility::class);
        $mapper = new AutoMapper($this->config);

        $source = new \stdClass();
        $source->protectedProperty = 'protected';
        $source->privateProperty = 'private';

        /** @var Visibility $result */
        $result = $mapper->map($source, Visibility::class);

        $this->assertEquals('protected', $result->getProtectedProperty());
        $this->assertEquals('private', $result->getPrivateProperty());
    }

    public function testItCanReverseMapWithReversibles()
    {
        $this->config->registerMapping(Source::class, Visibility::class)
            ->forMember('privateProperty', Operation::fromProperty('name'))
            ->reverseMap();
        $mapper = new AutoMapper($this->config);

        $source = new Source();
        $source->name = 'Hello';
        /** @var Visibility $result */
        $result = $mapper->map($source, Visibility::class);

        // Assert the initial mapping succeeds.
        $this->assertEquals('Hello', $result->getPrivateProperty());

        // The privateProperty of $source is now true.
        $source = new Visibility();
        $result = $mapper->map($source, Source::class);

        $this->assertTrue($result->name);
    }

    public function testItCanResolveNamesWithACallbackNameResolver()
    {
        $resolver = new CallbackNameResolver(function ($target) {
            return 'prefix' . ucfirst($target);
        });

        $this->config->registerMapping(PrefixedSource::class, UnPrefixedSource::class)
            ->withNameResolver($resolver);
        $mapper = new AutoMapper($this->config);

        $source = new PrefixedSource('Hello', 'world!');
        /** @var UnPrefixedSource $result */
        $result = $mapper->map($source, UnPrefixedSource::class);

        $this->assertEquals('Hello', $result->getName());
        $this->assertEquals('world!', $result->getPrivateProperty());
    }

    public function testACustomMapperCanBeUsed()
    {
        $this->config->registerMapping(Employee::class, EmployeeDto::class)
            ->useCustomMapper(new EmployeeMapper());
        $mapper = new AutoMapper($this->config);

        $employee = new Employee(10, 'John', 'Doe', 1980);
        $result = $mapper->map($employee, EmployeeDto::class);

        $this->assertEquals('Mapped by EmployeeMapper', $result->notes);
    }

    public function testACustomMapperWithMapperAwareCanBeUsed()
    {
        $this->config->registerMapping(Address::class, AddressDto::class)
            ->forMember('streetAndNumber', function($item){
                /** @var Address $item */
                return $item->street . '-' . $item->number;
            });
        $this->config->registerMapping(Employee::class, EmployeeDto::class)
                     ->useCustomMapper(new EmployeeMapperWithMapperAware());
        $mapper = new AutoMapper($this->config);

        $address = new Address();
        $address->street = 'main street';
        $address->number = 120;
        $expectedStreetAndNumber = $address->street . '-' . $address->number;

        $employee = new Employee(10, 'John', 'Doe', 1980, $address);

        /** @var EmployeeDto $result */
        $result = $mapper->map($employee, EmployeeDto::class);

        $this->assertEquals('Mapped by EmployeeMapperWithMapperAware', $result->notes);
        $this->assertInstanceOf(AddressDto::class, $result->address);
        $this->assertEquals($expectedStreetAndNumber, $result->address->streetAndNumber);
    }

    public function testItCanMapADifferentlyNamedPropertyWithACallback()
    {
        $this->config->registerMapping(
            SnakeCaseSource::class,
            CamelCaseSource::class
        )->forMember('anotherProperty', function () {
            return 'Test';
        });

        $mapper = new AutoMapper($this->config);

        $source = new SnakeCaseSource();
        $result = $mapper->map($source, CamelCaseSource::class);

        $this->assertEquals('Test', $result->anotherProperty);
    }

    public function testItMapsInheritedProperty()
    {
        $this->config->registerMapping(
            SourceChild::class,
            Destination::class
        );
        $mapper = new AutoMapper($this->config);

        $source = new SourceChild('Name');
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('Name', $result->name);
    }

    public function testItMapsInheritedPublicProperty()
    {
        $this->config->registerMapping(
            SourceChild::class,
            Destination::class
        );
        $mapper = new AutoMapper($this->config);

        $source = new SourceChild('Name');
        $source->anotherProperty = 'other';
        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('other', $result->anotherProperty);
    }

    public function testACustomConstructorCallbackCanBeProvided()
    {
        $this->config->registerMapping(Source::class, Destination::class)
            ->beConstructedUsing(function (Source $source, AutoMapperInterface $mapper, array $context): Destination {
                return new Destination('Set during construct');
            })
            ->forMember('name', Operation::ignore());
        $mapper = new AutoMapper($this->config);

        $source = new Source('Initial Name');

        $result = $mapper->map($source, Destination::class);
        $this->assertEquals('Set during construct', $result->name);

        $mapper->getConfiguration()
            ->getMappingFor(Source::class, Destination::class)
            ->skipConstructor()
            ->forMember('name', Operation::fromProperty('name'));

        $result = $mapper->map($source, Destination::class);
        $this->assertEquals('Initial Name', $result->name);
    }

    public function testItCanMapToStdClassInTheMostBasicCase()
    {
        $this->config->registerMapping(Source::class, \stdClass::class);
        $mapper = new AutoMapper($this->config);

        $source = new Source('Some name');
        $result = $mapper->map($source, \stdClass::class);

        $this->assertTrue(isset($result->name));
        $this->assertEquals('Some name', $result->name);
    }

    public function testItCanMapToStdClassWhileConvertingNames()
    {
        $this->config->registerMapping(CamelCaseSource::class, \stdClass::class)
            ->withNamingConventions(
                new CamelCaseNamingConvention(),
                new SnakeCaseNamingConvention()
            );
        $mapper = new AutoMapper($this->config);

        $source = new CamelCaseSource();
        $source->propertyName = 'Some Name';
        $result = $mapper->map($source, \stdClass::class);

        $this->assertTrue(isset($result->property_name));
        $this->assertEquals('Some Name', $result->property_name);
    }

    public function testItCanMapToAnyObjectCrate()
    {
        $config =  new AutoMapperConfig();
        $config->getOptions()->registerObjectCrate(NoProperties::class);
        $config->registerMapping(Source::class, NoProperties::class);
        $mapper = new AutoMapper($config);

        $source = new Source('Some name');
        $result = $mapper->map($source, NoProperties::class);

        $this->assertTrue(isset($result->name));
        $this->assertEquals('Some name', $result->name);
    }

    public function testObjectCratesStillRespectMappingOperations()
    {
        $this->config->registerMapping(CamelCaseSource::class, \stdClass::class)
            ->forMember('propertyName', Operation::ignore())
            ->forMember('anotherProperty', function () {
                return 'a value';
            });
        $mapper = new AutoMapper($this->config);
        $source = new CamelCaseSource();
        $source->propertyName = 'property name';
        $source->anotherProperty = 'another one';

        $result = $mapper->map($source, \stdClass::class);

        $this->assertTrue(isset($result->anotherProperty));
        $this->assertEquals('a value', $result->anotherProperty);
        $this->assertFalse(isset($result->propertyName));
    }

    public function testANullObjectReturnsNull()
    {
        $mapper = new AutoMapper();

        $source = null;
        $result = $mapper->map($source, Destination::class);
        $this->assertEquals(null, $result);
    }

    public function testInvalidWithMappingCallback_ThrowsException()
    {
        // Arrange
        $source = new CamelCaseSource();
        $error = null;

        // Act
        $this->config->registerMapping(CamelCaseSource::class, \stdClass::class)
            ->forMember('propertyName', Operation::mapFromWithMapper(function($source, AutoMapperInterface $mapping){
                return 13;
            }));
        $mapper = new AutoMapper($this->config);
        $result = $mapper->map($source, \stdClass::class);

        // Assert
        $this->assertEquals($result, $result);
    }

    public function testInstanceWithMappingCallback_InstanceIsCorrect()
    {
        // Arrange
        $propertyStdClass = new \stdClass();
        $propertyStdClass->value = "TestValue";

        $testSuffix = "MAPPED";
        $expectedResult = $propertyStdClass->value . $testSuffix;

        $this->config->registerMapping(\stdClass::class, Destination::class)
            ->forMember('name', function($source) use ($testSuffix){
                return $source->value . $testSuffix;
            });

        $this->config->registerMapping(CamelCaseSource::class, \stdClass::class)
            ->forMember('propertyName', Operation::mapFromWithMapper(function($source, AutoMapperInterface $mapping){
                // if the $mapping isn't a instance of AutoMapperInterface, it wouldn't return anything

                return $mapping->map($source->propertyName, Destination::class);
            }));
        $mapper = new AutoMapper($this->config);
        $source = new CamelCaseSource();
        $source->propertyName = $propertyStdClass;

        // Act
        $result = $mapper->map($source, \stdClass::class);

        // Assert
        $this->assertEquals($expectedResult, $result->propertyName->name);
    }

    /**
     * @todo: move this to fromPropertyTest.
     */
    public function testFromPropertyCanBeChained()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(Address::class, AddressDto::class)
            ->forMember('streetAndNumber', function (Address $source) {
                return $source->street . ' ' . $source->number;
            });
        $config->registerMapping(Person::class, PersonDto::class)
            ->forMember('address', Operation::fromProperty('adres')->mapTo(AddressDto::class))
        ;
        $mapper = new AutoMapper($config);

        $address = new Address;
        $address->street = "Main Street";
        $address->number = 12;
        $person = new Person;
        $person->adres = $address;

        $result = $mapper->map($person, PersonDto::class);

        $this->assertEquals("Main Street 12", $result->address->streetAndNumber);
    }

    public function testSubstitutionPrincipleSource()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(SourceParent::class, DestinationParent::class);
        $mapper = new AutoMapper($config);

        $source = new SourceChild('Some name');
        $result = $mapper->map($source, DestinationParent::class);

        $this->assertEquals('Some name', $result->name);
    }

    public function testSubstitutionPrincipleDestination()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(SourceParent::class, DestinationChild::class);
        $mapper = new AutoMapper($config);

        $source = new SourceParent('Some name');
        $result = $mapper->map($source, DestinationChild::class);

        $this->assertEquals('Some name', $result->name);
    }

    /**
     * https://github.com/mark-gerarts/automapper-plus/issues/25
     */
    public function testItMapsPrivatePropertiesWithTheSameSuffix()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(SimilarSource::class, SimilarDestination::class);
        $mapper = new AutoMapper($config);

        $source = new SimilarSource('id_1', 'id_2');
        $result = $mapper->map($source, SimilarDestination::class);

        $this->assertEquals('id_1', $result->id);
        $this->assertEquals('id_2', $result->second_id);
    }

    /**
     * @see https://github.com/mark-gerarts/automapper-plus/issues/33
     */
    public function testItMapsPrivatePropertiesWithTheSameSuffixOnTheTarget()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(UserDto::class, User::class);
        $mapper = new AutoMapper($config);

        $source = new UserDto();
        $source->id = 'id-value';
        $source->cellphone = 'cellphone-value';
        $source->phone = 'phone-value';
        /** @var User $result */
        $result = $mapper->map($source, User::class);

        $this->assertEquals('id-value', $result->getId());
        $this->assertEquals('cellphone-value', $result->getCellphone());
        $this->assertEquals('phone-value',  $result->getPhone());
    }

    /**
     * https://github.com/mark-gerarts/automapper-plus/issues/25
     */
    public function testitMapsPrivatePropertiesToStdClass()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(HasPrivateProperties::class, \stdClass::class);
        $mapper = new AutoMapper($config);

        $source = new HasPrivateProperties('AzureDiamond', 'hunter2');
        $result = $mapper->map($source, \stdClass::class);

        $this->assertEquals($result->username, 'AzureDiamond');
        $this->assertEquals($result->password, 'hunter2');
    }

    public function testAnExceptionIsThrownForUnregisteredMappings()
    {
        $this->expectException(UnregisteredMappingException::class);

        $mapper = new AutoMapper();
        $source = new Source('a name');

        $mapper->map($source, Destination::class);
    }

    public function testMappingsCanBeGeneratedOnTheFlyIfOptionIsSet()
    {
        $config = new AutoMapperConfig();
        $config->getOptions()->createUnregisteredMappings();
        $mapper = new AutoMapper($config);
        $source = new Source('a name');

        $result = $mapper->map($source, Destination::class);

        $this->assertEquals('a name', $result->name);
    }

    public function testAnExceptionIsThrownForNoIterableSourceInMultpleMappings()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->config->registerMapping(Source::class, Destination::class);
        $mapper = new AutoMapper($this->config);

        $sourceCollection = new \stdClass();

        $mapper->mapMultiple($sourceCollection, Destination::class);
    }

    public function testItMapsANullObjectReturnedFromConstructorToNull()
    {
        $this->config->registerMapping(DataType::ARRAY, Address::class)
            ->beConstructedUsing(function () { return null; });
        $this->config->registerMapping(DataType::ARRAY, Person::class)
            ->forMember('adres', Operation::mapTo(Address::class, true));
        $mapper = new AutoMapper($this->config);

        $source = [
            'adres' => [
                'street' => 'Main Street',
                'number' => '314'
            ]
        ];

        $result = $mapper->map($source, Person::class);

        $this->assertNull($result->adres);
    }
}
