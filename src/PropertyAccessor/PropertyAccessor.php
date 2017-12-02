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
        if (isset($object->{$propertyName})) {
            $object->{$propertyName} = $value;
            return;
        }

        $this->setPrivate($object, $propertyName, $value);
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
     * @param string $propertyName
     * @return string
     */
    private function getRealName(string $propertyName): string
    {
        return preg_replace('/\x00.*\x00/', '', $propertyName);
    }
}
