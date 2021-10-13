<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 */

namespace Laminas\Soap\Exception;

use RuntimeException;

/**
 * Exception thrown when SOAP PHP extension is not loaded
 */
class ExtensionNotLoadedException extends RuntimeException
{
}
