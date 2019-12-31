<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Soap\Wsdl\ComplexTypeStrategy;

/**
 * Laminas_Soap_Wsdl_Strategy_AnyType
 *
 * @category   Laminas
 * @package    Laminas_Soap
 * @subpackage WSDL
 */
class AnyType implements ComplexTypeStrategyInterface
{
    /**
     * Not needed in this strategy.
     *
     * @param \Laminas\Soap\Wsdl $context
     */
    public function setContext(\Laminas\Soap\Wsdl $context)
    {

    }

    /**
     * Returns xsd:anyType regardless of the input.
     *
     * @param string $type
     * @return string
     */
    public function addComplexType($type)
    {
        return 'xsd:anyType';
    }
}
