<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Soap\Client;

use Laminas\Soap\Client as SOAPClient;
use Laminas\Soap\Server as SOAPServer;

/**
 * \Laminas\Soap\Client\Local
 *
 * Class is intended to be used as local SOAP client which works
 * with a provided Server object.
 *
 * Could be used for development or testing purposes.
 *
 * @category   Laminas
 * @package    Laminas_Soap
 * @subpackage Client
 */
class Local extends SOAPClient
{
    /**
     * Server object
     *
     * @var \Laminas\Soap\Server
     */
    protected $server;

    /**
     * Local client constructor
     *
     * @param \Laminas\Soap\Server $server
     * @param string $wsdl
     * @param array $options
     */
    public function __construct(SOAPServer $server, $wsdl, $options = null)
    {
        $this->server = $server;

        // Use Server specified SOAP version as default
        $this->setSoapVersion($server->getSoapVersion());

        parent::__construct($wsdl, $options);
    }

    /**
     * Actual "do request" method.
     *
     * @internal
     * @param \Laminas\Soap\Client\Common $client
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int    $version
     * @param int    $one_way
     * @return mixed
     */
    public function _doRequest(Common $client, $request, $location, $action, $version, $one_way = null)
    {
        // Perform request as is
        ob_start();
        $this->server->handle($request);
        $response = ob_get_clean();

        if ($response === null || $response === '') {
            $serverResponse = $this->server->getResponse();
            if ($serverResponse !== null) {
                $response = $serverResponse;
            }
        }

        return $response;
    }
}
