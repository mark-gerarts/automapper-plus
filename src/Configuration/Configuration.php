<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\NameResolver\NamingConvention\NamingConventionInterface;

/**
 * Class Configuration
 *
 * @package AutoMapperPlus\Configuration
 */
class Configuration
{
    /**
     * @var NamingConventionInterface
     */
    private $sourceMemberNamingConvention;

    /**
     * @var NamingConventionInterface
     */
    private $destinationMemberNamingConvention;

    /**
     * @var bool
     */
    private $shouldSkipConstructor;
}
