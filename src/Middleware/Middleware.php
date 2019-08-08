<?php


namespace AutoMapperPlus\Middleware;


interface Middleware
{
    /**
     * The middleware implementation will replace the standard behavior.
     * 
     * @see MapperMiddleware::supports()
     */
    const OVERRIDE = PHP_INT_MAX;

    /**
     * The middleware implementation will not be used at all.
     * 
     * @see MapperMiddleware::supports()
     */
    const SKIP = 0;

    /**
     * The middleware implementation will be invoked after the standard behavior.
     * 
     * @see MapperMiddleware::supportsMap()
     * @see PropertyMiddleware::supportsMapProperty()
     */
    const AFTER = 1;

    /**
     * The middleware implementation will be invoked before the standard behavior.
     * 
     * @see MapperMiddleware::supportsMap()
     * @see PropertyMiddleware::supportsMapProperty()
     */
    const BEFORE = -1;
}