<?php

namespace LaminasTest\Soap\Client;

use Laminas\Http\Client\Adapter\Curl;
use Laminas\Soap\Client\Common;
use Laminas\Soap\Client\DotNet as DotNetClient;
use Laminas\Uri\Http;
use LaminasTest\Soap\DeprecatedAssertionsTrait;
use LaminasTest\Soap\TestAsset\MockCallUserFunc;
use PHPUnit\Framework\TestCase;

/**
 * .NET SOAP client tester.
 */
class DotNetTest extends TestCase
{
    use DeprecatedAssertionsTrait;

    /**
     * .NET SOAP client.
     *
     * @var DotNetClient
     */
    private $client;

    /**
     * cURL client.
     *
     * @var Curl
     */
    private $curlClient;

    /**
     * Sets up the fixture.
     */
    protected function setUp(): void
    {
        MockCallUserFunc::$mock = false;
        $this->client           = new DotNetClient(
            null,
            [
                'location' => 'http://unithost/test',
                'uri'      => 'http://unithost/test',
            ]
        );
    }

    /**
     * Disables mocking of call_user_func
     */
    protected function tearDown(): void
    {
        MockCallUserFunc::$mock = false;
    }

    /**
     * Tests that a default cURL client is used if none is injected.
     *
     * @return void
     * @covers Laminas\Soap\Client\DotNet::getCurlClient
     */
    public function testADefaultCurlClientIsUsedIfNoneIsInjected()
    {
        $this->assertInstanceOf(Curl::class, $this->client->getCurlClient());
    }

    /**
     * Tests that the cURL client can be injected.
     *
     * @return void
     * @covers Laminas\Soap\Client\DotNet::getCurlClient
     * @covers Laminas\Soap\Client\DotNet::setCurlClient
     */
    public function testCurlClientCanBeInjected()
    {
        $this->mockCurlClient();
        $this->assertSame($this->curlClient, $this->client->getCurlClient());
    }

    /**
     * Tests that a cURL client request is done when using NTLM
     * authentication.
     *
     * @return void
     * @covers Laminas\Soap\Client\DotNet::_doRequest
     */
    public function testCurlClientRequestIsDoneWhenUsingNtlmAuthentication()
    {
        $this->mockNtlmRequest();
        $this->assertInstanceOf('stdClass', $this->client->TestMethod());
    }

    /**
     * Tests that the default SOAP client request is done when not using NTLM authentication.
     *
     * @return void
     * @covers Laminas\Soap\Client\DotNet::_doRequest
     */
    public function testDefaultSoapClientRequestIsDoneWhenNotUsingNtlmAuthentication()
    {
        $soapClient = new Common(
            function (Common $client, $request, $location, $action, $version, $oneWay = null) {
                $this->assertEquals('http://unit/test#TestMethod', $action);
                $result = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">'
                    . '<s:Body>';

                $result .= '<TestMethodResponse xmlns="http://unit/test">'
                    . '<TestMethodResult>'
                    . '<TestMethodResult><dummy></dummy></TestMethodResult>'
                    . '</TestMethodResult>'
                    . '</TestMethodResponse>';

                $result .= '</s:Body>'
                    . '</s:Envelope>';

                return $result;
            },
            null,
            [
                'location' => 'http://unit/test',
                'uri'      => 'http://unit/test',
            ]
        );
        $this->assertAttributeEquals(false, 'useNtlm', $this->client);
        $this->client->setOptions([
            'authentication' => 'ntlm',
            'login'          => 'username',
            'password'       => 'testpass',
        ]);
        $this->client->setSoapClient($soapClient);
        $this->assertInstanceOf('stdClass', $this->client->TestMethod());
    }

    /**
     * Tests that the last request headers can be fetched correctly.
     *
     * @return void
     * @covers Laminas\Soap\Client\DotNet::getLastRequestHeaders
     */
    public function testLastRequestHeadersCanBeFetchedCorrectly()
    {
        $expectedHeaders = "Content-Type: text/xml; charset=utf-8\r\n"
                         . "Method: POST\r\n"
                         . "SOAPAction: \"http://unithost/test#TestMethod\"\r\n"
                         . "User-Agent: PHP-SOAP-CURL\r\n";

        $this->mockNtlmRequest();
        $this->client->TestMethod();

        $this->assertSame($expectedHeaders, $this->client->getLastRequestHeaders());
    }

    /**
     * Tests that the last response headers can be fetched correctly.
     *
     * @return void
     * @covers Laminas\Soap\Client\DotNet::getLastResponseHeaders
     */
    public function testLastResponseHeadersCanBeFetchedCorrectly()
    {
        $expectedHeaders = "Cache-Control: private\r\n"
                         . "Content-Type: text/xml; charset=utf-8\r\n";

        $this->mockNtlmRequest();
        $this->client->TestMethod();

        $this->assertSame($expectedHeaders, $this->client->getLastResponseHeaders());
    }

    /**
     * Mocks the cURL client.
     *
     * @return void
     */
    private function mockCurlClient()
    {
        $this->curlClient = $this->getMockBuilder(Curl::class)
            ->setMethods(['close', 'connect', 'read', 'write'])
            ->getMock();

        $this->client->setCurlClient($this->curlClient);
    }

    /**
     * Mocks an NTLM SOAP request.
     *
     * @return void
     */
    private function mockNtlmRequest()
    {
        $headers  = [
            'Content-Type' => 'text/xml; charset=utf-8',
            'Method'       => 'POST',
            'SOAPAction'   => '"http://unithost/test#TestMethod"',
            'User-Agent'   => 'PHP-SOAP-CURL',
        ];
        $response = "HTTP/1.1 200 OK\n"
            . "Cache-Control: private\n"
            . "Content-Type: text/xml; charset=utf-8\n"
            . "\n\n"
            . '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">'
            . '<s:Body>'
            . '<TestMethodResponse xmlns="http://unit/test">'
            . '<TestMethodResult>'
            . '<TestMethodResult><dummy></dummy></TestMethodResult>'
            . '</TestMethodResult>'
            . '</TestMethodResponse>'
            . '</s:Body>'
            . '</s:Envelope>';

        $this->mockCurlClient();

        $this->curlClient
            ->expects($this->once())
            ->method('connect')
            ->with('unithost', 80);
        $this->curlClient
            ->expects($this->once())
            ->method('read')
            ->will($this->returnValue($response));
        $this->curlClient
            ->expects($this->any())
            ->method('write')
            ->with(
                'POST',
                $this->isInstanceOf(Http::class),
                1.1,
                $headers,
                $this->stringContains('<SOAP-ENV')
            );

        $this->client->setOptions([
            'authentication' => 'ntlm',
            'login'          => 'username',
            'password'       => 'testpass',
        ]);
    }
}
