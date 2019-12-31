<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Soap\Client;

use Laminas\Soap\Client as SOAPClient;
use Laminas\Soap\Exception;

/**
 * .NET SOAP client
 *
 * Class is intended to be used with .Net Web Services.
 *
 * Important! Class is at experimental stage now.
 * Please leave your notes, compatibility issues reports or
 * suggestions in fw-webservices@lists.zend.com or fw-general@lists.com
 *
 * @category   Laminas
 * @package    Laminas_Soap
 * @subpackage Client
 */
class DotNet extends SOAPClient
{
    /**
     * Constructor
     *
     * @param string $wsdl
     * @param array $options
     */
    public function __construct($wsdl = null, $options = null)
    {
        // Use SOAP 1.1 as default
        $this->setSoapVersion(SOAP_1_1);

        parent::__construct($wsdl, $options);
    }


    /**
     * Perform arguments pre-processing
     *
     * My be overridden in descendant classes
     *
     * @param array $arguments
     * @throws Exception\RuntimeException
     * @return array
     */
    protected function _preProcessArguments($arguments)
    {
        if (count($arguments) > 1  ||
            (count($arguments) == 1  &&  !is_array(reset($arguments)))
           ) {
            throw new Exception\RuntimeException('.Net webservice arguments have to be grouped into array: array(\'a\' => $a, \'b\' => $b, ...).');
        }

        // Do nothing
        return $arguments;
    }

    /**
     * Perform result pre-processing
     *
     * My be overridden in descendant classes
     *
     * @param object $result
     * @return mixed
     */
    protected function _preProcessResult($result)
    {
        $resultProperty = $this->getLastMethod() . 'Result';

        return $result->$resultProperty;
    }

}
