<?php

namespace AutoMapperPlus\PropertyAccessor;

use function Functional\map;

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
        if (isset($object->{$propertyName})) {
            return true;
        }

        $objectArray = (array) $object;
        foreach ($objectArray as $name => $value) {
            if ($this->getRealName($name) == $propertyName) {
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
    public function setProperty($object, string $propertyName, $value): void
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
        return map((array) $object, function ($_, $name) {
            return $this->getRealName($name);
        });
    }

    /**
     * Abuses PHP's internal representation of properties when casting an object
     * to an array.
     *
     * @param $object
     * @param string $propertyName
     * @return mixed
     */
    protected function getPrivate($object, string $propertyName)
    {
        $objectArray = (array) $object;
        foreach ($objectArray as $name => $value) {
            if ($this->getRealName($name) == $propertyName) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Adapted from https://gist.github.com/githubjeka/153e5a0f6d15cf20512e.
     *
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
     * Checks if the given property is public.
     *
     * @param $object
     * @param string $propertyName
     * @return bool
     */
    private function isPublic($object, string $propertyName) {
        $objectArray = (array) $object;

        return array_key_exists($propertyName, $objectArray);
    }

    /**
     * @param string $propertyName
     * @return string
     */
    private function getRealName(string $propertyName): string
    {
        return preg_replace('/\x00.*\x00/', '', $propertyName);
    }
}
