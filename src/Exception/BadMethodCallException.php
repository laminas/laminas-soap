<?php

namespace Laminas\Soap\Exception;

use BadMethodCallException as SPLBadMethodCallException;

/**
 * Exception thrown when unrecognized method is called via overloading
 */
class BadMethodCallException extends SPLBadMethodCallException implements ExceptionInterface
{
}
