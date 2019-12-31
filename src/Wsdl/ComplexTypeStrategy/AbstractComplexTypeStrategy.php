<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Soap\Wsdl\ComplexTypeStrategy;

/**
 * Abstract class for Laminas_Soap_Wsdl_Strategy.
 */
abstract class AbstractComplexTypeStrategy implements ComplexTypeStrategyInterface
{
    /**
     * Context object
     *
     * @var \Laminas\Soap\Wsdl
     */
    protected $context;

    /**
     * Set the Laminas_Soap_Wsdl Context object this strategy resides in.
     *
     * @param \Laminas\Soap\Wsdl $context
     * @return void
     */
    public function setContext(\Laminas\Soap\Wsdl $context)
    {
        $this->context = $context;
    }

    /**
     * Return the current Laminas_Soap_Wsdl context object
     *
     * @return \Laminas\Soap\Wsdl
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Look through registered types
     *
     * @param string $phpType
     * @return string
     */
    public function scanRegisteredTypes($phpType)
    {
        if (array_key_exists($phpType, $this->getContext()->getTypes())) {
            $soapTypes = $this->getContext()->getTypes();
            return $soapTypes[$phpType];
        }

        return null;
    }
}
