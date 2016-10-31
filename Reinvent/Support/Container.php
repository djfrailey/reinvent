<?php

declare (strict_types = 1);

namespace Reinvent\Support;

use \ReflectionClass;
use \ReflectionObject;
use \ReflectionFunction;
use \InvalidArgumentException;
use \Exception;
use \Throwable;
use \Closure;

class Container
{
    /**
     * Collection of bound instances
     * @var array
     */
    protected $boundInstances;

    /**
     * Collection of bound class references.
     * @var array
     */
    protected $boundClasses;

    public function __construct()
    {
        $this->boundInstances = new Collection();
        $this->boundClasses = new Collection();
    }

    /**
     * Registers a singleton instance with the container
     * @param  string $abstract
     * @param  mixed $concrete
     */
    public function singleton(string $abstract, $concrete)
    {
        if ($this->isInstance($concrete) === false) {
            $concrete = null;
        } else if ($this->isInstantiable($concrete)) {
            $concrete = $this->resolve($concrete);
        } else if ($this->isClosure($concrete)) {
            $concrete = $this->resolveClosure($concrete);
        } else {
            throw new InvalidArgumentException("Write me a message.");
        }

        $this->boundInstances[$abstract] = $concrete;
    }

    /**
     * Registers an object instance with the container
     * @param  string $abstract
     * @param  object $concrete
     */
    public function instance(string $abstract, $concrete)
    {
        if ($this->isInstance($concrete) === false) {
            throw new InvalidArgumentException('Concrete must be an instance.');
        }

        $this->boundInstances[$abstract] = $concrete;
    }

    /**
     * Register a class, closure, or object with the container
     * @param  string $abstract
     * @param  mixed $concrete
     */
    public function bind(string $abstract, $concrete)
    {
        $this->boundClasses[$abstract] = $concrete;
    }

    /**
     * Attempts to retreive the concrete implementation of the requested abstract identifer
     * @param  string $abstract
     * @return mixed
     */
    public function resolve(string $abstract)
    {
        $resolution = null;

        if ($this->boundInstances->has($abstract)) {
            $resolution = $this->boundInstances[$abstract];
        } elseif ($this->boundClasses->has($abstract)) {
            $concrete = $this->boundClasses[$abstract];

            if ($this->isClosure($concrete)) {
                $resolution = $this->resolveClosure($concrete);
            } else if ($this->isInstantiable($concrete)) {
                $resolution = $this->resolveInstantiable($concrete);
            }
        } else if ($this->isInstantiable($abstract)) {
            $resolution = $this->resolveInstantiable($abstract);
        }

        return $resolution;
    }

    /**
     * Resolves a bound closures dependencies
     * @param  Closure $concrete 
     * @return mixed
     */
    protected function resolveClosure(Closure $concrete)
    {
        $reflection = new ReflectionFunction($concrete);
        
        $parameters = $reflection->getParameters();
        $resolvedDependencies = $this->resolveDependencies($parameters);
        
        return call_user_func_array($concrete, $resolvedDependencies);
    }

    /**
     * Resolves a bound instantiables dependencies
     * @param  mixed $concrete
     * @return mixed
     */
    protected function resolveInstantiable($concrete)
    {
        $reflection = new ReflectionClass($concrete);
        $constructor = $reflection->getConstructor();

        $parameters = [];

        if ($constructor) {
            $parameters = $constructor->getParameters();
        }
        
        $resolvedDependencies = $this->resolveDependencies($parameters);

        return $reflection->newInstanceArgs($resolvedDependencies);
    }

    /**
     * Iterates through the passed in parameters list and resolves known dependencies one at a time.
     * @param  array  $dependencies [description]
     * @return array  $resolvedDependencies
     */
    protected function resolveDependencies(array $dependencies = []) : array
    {
        $resolved = [];

        if ($dependencies) {
            foreach($dependencies as $dependency) {
                if ($dependency->hasType() === false) {
                    continue;
                }

                $type = $dependency->getType();
                $resolved[] = $this->resolve((string) $type);
            }
        }

        return $resolved;
    }

    public function isBound(string $abstract) : bool
    {
        $isBound = $this->boundInstances->has($abstract);

        if ($isBound === false) {
            $isBound = $this->boundClasses->has($abstract);
        }

        return $isBound;
    }

    protected function isClosure($abstract) : bool
    {
        $closure = false;
        
        try {
            $reflectionFunction = new ReflectionFunction($abstract);
            $closure = $reflectionFunction->isClosure();
        } catch (Throwable $e) {
            //
        }

        return $closure;
    }

    protected function isInstantiable($abstract) : bool
    {
        $instantiable = false;

        try {
            $reflectionClass = new ReflectionClass($abstract);
            $instantiable = $reflectionClass->isInstantiable();
        } catch (Throwable $e) {
            //
        }

        return $instantiable;
    }

    protected function isInstance($abstract) : bool
    {
        $instance = false;

        try {
            $reflectionClass = new ReflectionObject($abstract);
            $instance = true;
        } catch (Throwable $e) {
            //
        }

        return $instance;
    }
}
