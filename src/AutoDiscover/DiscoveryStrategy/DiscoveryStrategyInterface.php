<?php

namespace Laminas\Soap\AutoDiscover\DiscoveryStrategy;

use Laminas\Server\Reflection\AbstractFunction;
use Laminas\Server\Reflection\Prototype;
use Laminas\Server\Reflection\ReflectionParameter;

/**
 * Describes how types, return values and method details are detected during
 * AutoDiscovery of a WSDL.
 */
interface DiscoveryStrategyInterface
{
    /**
     * Get the function parameters php type.
     *
     * Default implementation assumes the default param doc-block tag.
     *
     * @return string
     */
    public function getFunctionParameterType(ReflectionParameter $param);

    /**
     * Get the functions return php type.
     *
     * Default implementation assumes the value of the return doc-block tag.
     *
     * @return string
     */
    public function getFunctionReturnType(AbstractFunction $function, Prototype $prototype);

    /**
     * Detect if the function is a one-way or two-way operation.
     *
     * Default implementation assumes one-way, when return value is "void".
     *
     * @return bool
     */
    public function isFunctionOneWay(AbstractFunction $function, Prototype $prototype);

    /**
     * Detect the functions documentation.
     *
     * Default implementation uses docblock description.
     *
     * @return string
     */
    public function getFunctionDocumentation(AbstractFunction $function);
}
