<?php

namespace Laminas\Soap\Wsdl\DocumentationStrategy;

use ReflectionClass;
use ReflectionProperty;

/**
 * Implement this interface to provide contents for <xsd:documentation> elements on complex types
 */
interface DocumentationStrategyInterface
{
    /**
     * Returns documentation for complex type property
     *
     * @param ReflectionProperty $property
     * @return string
     */
    public function getPropertyDocumentation(ReflectionProperty $property);

    /**
     * Returns documentation for complex type
     *
     * @param ReflectionClass $class
     * @return string
     */
    public function getComplexTypeDocumentation(ReflectionClass $class);
}
