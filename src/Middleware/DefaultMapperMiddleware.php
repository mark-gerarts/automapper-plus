<?php


namespace AutoMapperPlus\Middleware;


use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\MappingOperation\ContextAwareOperation;
use AutoMapperPlus\MappingOperation\MapperAwareOperation;

class DefaultMapperMiddleware implements MapperMiddleware, DefaultMiddleware
{
    protected function doMap($source, $destination, AutoMapperInterface $mapper, MappingInterface $mapping, array $context)
    {
        $propertyNames = $mapping->getTargetProperties($destination, $source);
        foreach ($propertyNames as $propertyName) {
            $this->push(AutoMapper::PROPERTY_STACK_CONTEXT, $propertyName, $context);
            try {
                $operation = $mapping->getMappingOperationFor($propertyName);

                if ($operation instanceof MapperAwareOperation) {
                    $operation->setMapper($mapper);
                }
                if ($operation instanceof ContextAwareOperation) {
                    $operation->setContext($context);
                }

                $mapper->getConfiguration()->getDefaultPropertyMiddleware()->mapProperty(
                    $propertyName,
                    $source,
                    $destination,
                    $mapper,
                    $mapping,
                    $operation,
                    $context, function () {
                    // NOOP
                });

                $mapProperty = function () {
                    // NOOP
                };

                foreach (array_reverse($this->getPropertyMiddleware($mapper->getConfiguration())) as $middleware) {
                    $mapProperty = function () use ($middleware, $propertyName, $source, $destination, $mapper, $mapping, $operation, $context, $mapProperty) {
                        /** @var PropertyMiddleware $middleware */
                        return $middleware->mapProperty($propertyName, $source, $destination, $mapper, $mapping, $operation, $context, $mapProperty);
                    };
                }

                $mapProperty();
            } finally {
                $this->pop(AutoMapper::PROPERTY_STACK_CONTEXT, $context);
            }
        }
    }

    public function map($source, $destination, AutoMapperInterface $mapper, MappingInterface $mapping, array $context, callable $next)
    {
        $this->doMap($source, $destination, $mapper, $mapping, $context);
        $next();
    }

    private function push($key, $value, &$context)
    {
        if (!array_key_exists($key, $context)) {
            $stack = [];
        } else {
            $stack = $context[$key];
        }
        $stack[] = $value;
        $context[$key] = $stack;
    }

    private function pop($key, &$context)
    {
        array_pop($context[$key]);
    }

    private function getPropertyMiddleware(AutoMapperConfigInterface $configuration)
    {
        return array_filter($configuration->getPropertyMiddlewares(), function ($middleware) use ($configuration) {
            return $middleware !== $configuration->getDefaultPropertyMiddleware();
        });
    }
}