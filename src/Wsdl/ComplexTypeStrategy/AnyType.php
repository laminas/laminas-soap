<?php

namespace Laminas\Soap\Wsdl\ComplexTypeStrategy;

use Laminas\Soap\Wsdl;

class AnyType implements ComplexTypeStrategyInterface
{
    /**
     * Not needed in this strategy.
     */
    public function setContext(Wsdl $context)
    {
    }

    /**
     * Returns xsd:anyType regardless of the input.
     *
     * @param  string $type
     * @return string
     */
    public function addComplexType($type)
    {
        return Wsdl::XSD_NS . ':anyType';
    }
}
