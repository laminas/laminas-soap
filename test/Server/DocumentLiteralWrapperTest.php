<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Soap\Server;

use Laminas\Soap\Client\Local as SoapClient;
use Laminas\Soap\Server;
use Laminas\Soap\Server\DocumentLiteralWrapper;
use LaminasTest\Soap\TestAsset\MyCalculatorService;

class DocumentLiteralWrapperTest extends \PHPUnit_Framework_TestCase
{
    const WSDL = '/_files/calculator.wsdl';

    private $client;

    public function setUp()
    {
        ini_set("soap.wsdl_cache_enabled", 0);
    }

    /**
     * @runInSeparateProcess
     */
    public function testDelegate()
    {
        $server = new Server(__DIR__ . self::WSDL);
        $server->setObject(new DocumentLiteralWrapper(new MyCalculatorService));

        // The local client needs an abstraction for this pattern as well.
        // This is just a test so we use the messy way.
        $client = new SoapClient($server, __DIR__ . self::WSDL);
        $ret = $client->add(['x' => 10, 'y' => 20]);

        $this->assertInstanceOf('stdClass', $ret);
        $this->assertEquals(30, $ret->addResult);
    }
}
