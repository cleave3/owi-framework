<?php

namespace App\core;

use Exception;
use ReflectionClass;
use ReflectionNamedType;

class Container
{
    private $bindings = [];
    private $instances = [];

    /**
     * Bind a dependency
     * 
     * @param string $id
     * @param callable $concrete
     */
    public function bind(string $id, callable $concrete)
    {
        $this->bindings[$id] = $concrete;
    }

    /**
     * Get a resolved dependency
     * 
     * @param string $id
     * @return mixed
     */
    public function get(string $id)
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (isset($this->bindings[$id])) {
            $object = $this->bindings[$id]($this);
            $this->instances[$id] = $object;
            return $object;
        }

        return $this->resolve($id);
    }

    /**
     * Resolve a class using Reflection
     * 
     * @param string $id
     * @return object
     */
    public function resolve(string $id)
    {
        try {
            $reflector = new ReflectionClass($id);
        } catch (Exception $e) {
            throw new Exception("Target class [$id] does not exist.", 0, $e);
        }

        if (!$reflector->isInstantiable()) {
            throw new Exception("Class [$id] is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $id;
        }

        $parameters = $constructor->getParameters();
        $dependencies = $this->getDependencies($parameters);

        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * Resolve dependencies recursively
     * 
     * @param array $parameters
     * @return array
     */
    public function getDependencies(array $parameters)
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                    continue;
                }
                throw new Exception("Unresolvable dependency resolving [$parameter] in class {$parameter->getDeclaringClass()->getName()}");
            }

            $name = $type->getName();
            $dependencies[] = $this->get($name);
        }

        return $dependencies;
    }
}
