<?php
/**
 * Created by PhpStorm.
 * User: yimeng
 * Date: 2016/12/12
 * Time: 下午4:41
 */

namespace MyDI\Container;


use MongoDB\Driver\Exception\ConnectionException;
use MyDI\Container\Reference\ParameterReference;
use MyDI\Container\Reference\ServiceReference;
use MyDI\exception\Exception\ContainerException;
use MyDI\exception\Exception\ParameterNotFoundException;
use MyDI\exception\Exception\ServiceNotFoundException;

class Container implements InteropContainerInterface {

    private $services;
    private $parameters;
    private $serviceStore;


    public function __construct(array $services=[], array $parameters=[])
    {

        $this->services = $services;
        $this->parameters = $parameters;
        $this->serviceStore = [];
    }

    public function get($name)
    {

        if (!$this->has($name)) {
            throw  new ServiceNotFoundException();
        }

        if (!isset($this->serviceStore[$name])) {
            $this->serviceStore[$name] = $this->createService($name);
        }

        return $this->serviceStore[$name];
    }

    public function getParameter($name)
    {

        $tokens = explode('.', $name);
        $context = $this->parameters;


        while (null !== ($token = array_shift($tokens)))
        {
            if (!isset($context[$token])) {
                throw new ParameterNotFoundException('Parameter not found: ' . $name);
            }

            $context = $context[$token];
        }

        return $context;
    }

    public function has($name)
    {
        return isset($this->services[$name]);
    }

    private function createService($name)
    {

        $entry = &$this->services[$name];


        if (!is_array($entry) || !isset($entry['class'])) {
            throw new ContainerException($name . ' service entry must be an array containing a \'class\' key');
        } elseif (!class_exists($entry['class'])) {
            throw new ContainerException($name . ' service class does not exist: ' . $entry['class']);
        } elseif (isset($entry['lock'])) {
            throw new ContainerException($name . ' service contains a circular reference');
        }

        $entry['lock'] = true;

        $arguments = isset($entry['arguments']) ? $this->resolveArguments($name, $entry['arguments']) : [];

        $reflector = new \ReflectionClass($entry['class']);
        $service = $reflector->newInstanceArgs($arguments);

        if (isset($entry['calls'])) {
            $this->initializeService($service, $name, $entry['calls']);
        }

        return $service;
    }

    private function resolveArguments($name, array $argumentDefinitions)
    {

        $arguments = [];

        foreach ($argumentDefinitions as $argumentDefinition) {
            if ($argumentDefinition instanceof ServiceReference) {
                $argumentServiceName = $argumentDefinition->getName();

                $arguments[] = $this->get($argumentServiceName);
            } elseif ($argumentDefinition instanceof ParameterReference) {
                $argumentParameterName = $argumentDefinition->getName;

                $arguments[] = $argumentParameterName;
            } else {
                $arguments[] = $argumentDefinition;
            }
        }

        return $arguments;
    }

    private function initializeService($service, $name, array $callDefinitions) {
        foreach ($callDefinitions as $callDefinition) {
            if (!is_array($callDefinition) || !isset($callDefinition['method'])) {
                throw new ConnectionException($name . ' service calls must be arrays containing a \'method\' key');
            }
        }
    }



}