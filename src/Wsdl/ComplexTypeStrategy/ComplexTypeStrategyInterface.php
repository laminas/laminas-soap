<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Soap\Wsdl\ComplexTypeStrategy;

/**
 * Interface strategies that generate an XSD-Schema for complex data types in WSDL files.
 *
 * @category   Laminas
 * @package    Laminas_Soap
 * @subpackage WSDL
 */
interface ComplexTypeStrategyInterface
{
    /**
     * Method accepts the current WSDL context file.
     *
     * @param <type> $context
     */
    public function setContext(\Laminas\Soap\Wsdl $context);

    /**
     * Create a complex type based on a strategy
     *
     * @param  string $type
     * @return string XSD type
     */
    public function addComplexType($type);
}
