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
        if (property_exists($object, $propertyName)) {
            return true;
        }

        // property_exists doesn't return true for inherited properties.
        $objectArray = (array) $object;
        foreach ($objectArray as $name => $value) {
            if (substr($name, - \strlen($propertyName) - 1) === "\x00" . $propertyName) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getProperty($object, string $propertyName)
    {
        if (isset($object->{$propertyName})) {
            return $object->{$propertyName};
        }

        return $this->getPrivate($object, $propertyName);
    }

    /**
     * @inheritdoc
     */
    public function setProperty(&$object, string $propertyName, $value): void
    {
        if ($this->isPublic($object, $propertyName)) {
            $object->{$propertyName} = $value;
            return;
        }

        $this->setPrivate($object, $propertyName, $value);
    }

    /**
     * @inheritdoc
     */
    public function getPropertyNames($object): array
    {
        $propertyNames = [];
        foreach ((array) $object as $propertyPath => $value) {
            $propertyNames[] = $this->getRealName($propertyPath);
        }

        return $propertyNames;
    }

    /**
     * Abuses PHP's internal representation of properties when casting an object
     * to an array.
     *
     * @param mixed $object
     * @param string $propertyName
     * @return mixed
     */
    protected function getPrivate($object, string $propertyName)
    {
        $objectArray = (array) $object;
        foreach ($objectArray as $name => $value) {
            if (substr($name, - \strlen($propertyName) - 1) === "\x00" . $propertyName) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param mixed $object
     * @param string $propertyName
     * @param mixed $value
     */
    protected function setPrivate($object, string $propertyName, $value): void
    {
        $reflectionClass = new \ReflectionClass($object);

        // Parent properties are not included in the reflection class, so we'll
        // go up the inheritance chain and check if the property exists in one
        // of the parents.
        while (
            !$reflectionClass->hasProperty($propertyName)
            && $parent = $reflectionClass->getParentClass()
        ) {
            $reflectionClass = $parent;
        }
        $reflectionProperty = $reflectionClass->getProperty($propertyName);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }

    /**
     * Checks if the given property is public.
     *
     * @param mixed $object
     * @param string $propertyName
     * @return bool
     */
    private function isPublic($object, string $propertyName): bool
    {
        $objectArray = (array) $object;

        return array_key_exists($propertyName, $objectArray);
    }

    /**
     * @param string $propertyPath
     * @return string
     */
    private function getRealName(string $propertyPath): string
    {
        $currChar = \strlen($propertyPath) - 1;
        $realName = '';
        while ($currChar >= 0 && $propertyPath[$currChar] !== "\x00") {
            $realName = $propertyPath[$currChar] . $realName;
            $currChar--;
        }

        return $realName;
    }
}
