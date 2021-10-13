<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 */

namespace Laminas\Soap\Exception;

use UnexpectedValueException as SPLUnexpectedValueException;

/**
 * Exception thrown when provided arguments are invalid
 */
class UnexpectedValueException extends SPLUnexpectedValueException implements ExceptionInterface
{
}
