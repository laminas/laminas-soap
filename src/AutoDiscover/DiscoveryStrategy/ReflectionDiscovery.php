<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Soap\AutoDiscover\DiscoveryStrategy;

use Laminas\Server\Reflection\AbstractFunction;
use Laminas\Server\Reflection\Prototype;
use Laminas\Server\Reflection\ReflectionParameter;

/**
 * Describes how types, return values and method details are detected during AutoDiscovery of a WSDL.
 */

class ReflectionDiscovery implements DiscoveryStrategyInterface
{
    public function getFunctionDocumentation(AbstractFunction $function)
    {
        return $function->getDescription();
    }

    public function getFunctionParameterType(ReflectionParameter $param)
    {
        return $param->getType();
    }

    public function getFunctionReturnType(AbstractFunction $function, Prototype $prototype)
    {
        return $prototype->getReturnType();
    }

    public function isFunctionOneWay(AbstractFunction $function, Prototype $prototype)
    {
        return $prototype->getReturnType() == 'void';
    }
}
