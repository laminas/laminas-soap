<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 */

namespace Laminas\Soap\Exception;

use RuntimeException as SPLRuntimeException;

/**
 * Exception thrown when there is an error during program execution
 */
class RuntimeException extends SPLRuntimeException implements ExceptionInterface
{
}
