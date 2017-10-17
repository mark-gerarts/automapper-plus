<?php

namespace AutoMapperPlus\PropertyAccessor;

/**
 * Class PropertyAccessor
 *
 * @package AutoMapperPlus\PropertyAccessor
 */
class PropertyAccessor implements PropertyAccessorInterface
{
    /**
     * @inheritdoc
     */
    public function hasProperty($object, string $propertyName): bool
    {
        if ($object instanceof \stdClass) {
            return isset(get_object_vars($object)[$propertyName]);
        }

        return (new \ReflectionClass($object))->hasProperty($propertyName);
    }

    /**
     * @inheritdoc
     */
    public function getProperty($object, string $propertyName)
    {
        if ($this->isPublic($propertyName, $object)) {
            return $object->{$propertyName};
        }

        return $this->getPrivate($object, $propertyName);
    }

    /**
     * @inheritdoc
     */
    public function setProperty($object, string $propertyName, $value): void
    {
        if ($this->isPublic($propertyName, $object)) {
            $object->{$propertyName} = $value;
            return;
        }

        $this->setPrivate($object, $propertyName, $value);
    }

    /**
     * Adapted from https://gist.github.com/githubjeka/153e5a0f6d15cf20512e.
     *
     * @param $object
     * @param string $propertyName
     * @return mixed
     */
    protected function getPrivate($object, string $propertyName)
    {
        $getter = function() use ($propertyName) {
            return $this->{$propertyName};
        };
        $boundGetter = \Closure::bind($getter, $object, get_class($object));

        return $boundGetter();
    }

    /**
     * @param $object
     * @param string $propertyName
     * @param $value
     */
    protected function setPrivate($object, string $propertyName, $value): void
    {
        $setter = function($value) use ($propertyName) {
            $this->{$propertyName} = $value;
        };
        $boundSetter = \Closure::bind($setter, $object, get_class($object));
        $boundSetter($value);
    }

    /**
     * @param string $propertyName
     * @param $object
     * @return bool
     */
    private function isPublic(string $propertyName, $object): bool
    {
        if ($object instanceof \stdClass) {
            return true;
        }

        $reflectionClass = new \ReflectionClass($object);
        $property = $reflectionClass->getProperty($propertyName);

        return $property->isPublic();
    }
}
