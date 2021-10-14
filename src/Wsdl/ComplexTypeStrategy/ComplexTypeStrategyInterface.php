<?php

namespace Laminas\Soap\Wsdl\ComplexTypeStrategy;

use Laminas\Soap\Wsdl;

/**
 * Interface strategies that generate an XSD-Schema for complex data types in WSDL files.
 */
interface ComplexTypeStrategyInterface
{
    /**
     * Method accepts the current WSDL context file.
     */
    public function setContext(Wsdl $context);

    /**
     * Create a complex type based on a strategy
     *
     * @param  string $type
     * @return string XSD type
     */
    public function addComplexType($type);
}
