<?php


namespace AutoMapperPlus\Middleware;


/**
 * Marker interface for default middlewares.
 *
 * When registering a middleware marked with this interface, it will replace the default mapping behavior.
 */
interface DefaultMiddleware extends Middleware
{
}