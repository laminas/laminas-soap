<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Soap\Wsdl\ComplexTypeStrategy;

use Laminas\Soap\Exception;
use Laminas\Soap\Wsdl;
use Laminas\Soap\Wsdl\ComplexTypeStrategy\ComplexTypeStrategyInterface as ComplexTypeStrategy;

/**
 * Laminas_Soap_Wsdl_Strategy_Composite
 */
class Composite implements ComplexTypeStrategy
{
    /**
     * Typemap of Complex Type => Strategy pairs.
     *
     * @var array
     */
    protected $typeMap = array();

    /**
     * Default Strategy of this composite
     *
     * @var string|ComplexTypeStrategy
     */
    protected $defaultStrategy;

    /**
     * Context WSDL file that this composite serves
     *
     * @var \Laminas\Soap\Wsdl|null
     */
    protected $context;

    /**
     * Construct Composite WSDL Strategy.
     *
     * @param array $typeMap
     * @param string|ComplexTypeStrategy $defaultStrategy
     */
    public function __construct(array $typeMap=array(), $defaultStrategy='\Laminas\Soap\Wsdl\ComplexTypeStrategy\DefaultComplexType')
    {
        foreach ($typeMap AS $type => $strategy) {
            $this->connectTypeToStrategy($type, $strategy);
        }
        $this->defaultStrategy = $defaultStrategy;
    }

    /**
     * Connect a complex type to a given strategy.
     *
     * @throws Exception\InvalidArgumentException
     * @param  string $type
     * @param  string|ComplexTypeStrategy $strategy
     * @return Composite
     */
    public function connectTypeToStrategy($type, $strategy)
    {
        if (!is_string($type)) {
            throw new Exception\InvalidArgumentException('Invalid type given to Composite Type Map.');
        }
        $this->typeMap[$type] = $strategy;
        return $this;
    }

    /**
     * Return default strategy of this composite
     *
     * @throws  Exception\InvalidArgumentException
     * @return ComplexTypeStrategy
     */
    public function getDefaultStrategy()
    {
        $strategy = $this->defaultStrategy;
        if (is_string($strategy) && class_exists($strategy)) {
            $strategy = new $strategy;
        }
        if ( !($strategy instanceof ComplexTypeStrategy) ) {
            throw new Exception\InvalidArgumentException(
                'Default Strategy for Complex Types is not a valid strategy object.'
            );
        }
        $this->defaultStrategy = $strategy;
        return $strategy;
    }

    /**
     * Return specific strategy or the default strategy of this type.
     *
     * @throws  Exception\InvalidArgumentException
     * @param  string $type
     * @return ComplexTypeStrategy
     */
    public function getStrategyOfType($type)
    {
        if (isset($this->typeMap[$type])) {
            $strategy = $this->typeMap[$type];

            if (is_string($strategy) && class_exists($strategy)) {
                $strategy = new $strategy();
            }

            if ( !($strategy instanceof ComplexTypeStrategy) ) {
                throw new Exception\InvalidArgumentException(
                    "Strategy for Complex Type '$type' is not a valid strategy object."
                );
            }
            $this->typeMap[$type] = $strategy;
        } else {
            $strategy = $this->getDefaultStrategy();
        }
        return $strategy;
    }

    /**
     * Method accepts the current WSDL context file.
     *
     * @param \Laminas\Soap\Wsdl $context
     * @return Composite
     */
    public function setContext(Wsdl $context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Create a complex type based on a strategy
     *
     * @throws  Exception\InvalidArgumentException
     * @param  string $type
     * @return string XSD type
     */
    public function addComplexType($type)
    {
        if (!($this->context instanceof Wsdl) ) {
            throw new Exception\InvalidArgumentException(
                "Cannot add complex type '$type', no context is set for this composite strategy."
            );
        }

        $strategy = $this->getStrategyOfType($type);
        $strategy->setContext($this->context);
        return $strategy->addComplexType($type);
    }
}
