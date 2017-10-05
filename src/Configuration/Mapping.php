<?php

namespace AutoMapperPlus\Configuration;

/**
 * Class Mapping
 *
 * @package AutoMapperPlus\Configuration
 */
class Mapping implements MappingInterface
{
    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @var array
     */
    private $mappingCallbacks = [];

    /**
     * Mapping constructor.
     *
     * @param string $from
     * @param string $to
     */
    public function __construct(string $from, string $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @inheritdoc
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @inheritdoc
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * @inheritdoc
     */
    public function forMember
    (
        string $propertyName,
        callable $mapCallback
    ): MappingInterface
    {
        $this->mappingCallbacks[$propertyName] = $mapCallback;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMappingCallbackFor(string $propertyName): ?callable
    {
        // @todo:
        // Don't work with callbacks, but with an 'Operation' class. You could,
        // for example haven an Operation::ignore(), an
        // Operation::mapFrom(<callback>), or an Operation::getPrivate(<name>).
        return $this->mappingCallbacks[$propertyName] ?? null;
    }
}
