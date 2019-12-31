<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Soap;

use Laminas\Soap\Wsdl;
use Laminas\Soap\Wsdl\ComplexTypeStrategy;
use Laminas\Soap\Wsdl\ComplexTypeStrategy\ComplexTypeStrategyInterface;

/**
* Laminas_Soap_Server
*
* @group      Laminas_Soap
* @group      Laminas_Soap_Wsdl
**/
class WsdlTestHelper extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Wsdl
     */
    protected $wsdl;
    /**
     * @var \DOMDocument
     */
    protected $dom;
    /**
     * @var \DOMXPath
     */
    protected $xpath;

    /**
     * @var ComplexTypeStrategy\ComplexTypeStrategyInterface
     */
    protected $strategy;

    /**
     * @var string
     */
    protected $defaultServiceName = 'MyService';

    /**
     * @var string
     */
    protected $defaultServiceUri = 'http://localhost/MyService.php';

    public function setUp()
    {
        if (empty($this->strategy) or !($this->strategy instanceof ComplexTypeStrategyInterface)) {
            $this->strategy = new Wsdl\ComplexTypeStrategy\DefaultComplexType();
        }

        $this->wsdl = new Wsdl($this->defaultServiceName, $this->defaultServiceUri, $this->strategy);

        if ($this->strategy instanceof ComplexTypeStrategyInterface) {
            $this->strategy->setContext($this->wsdl);
        }

        $this->dom = $this->wsdl->toDomDocument();
        $this->dom = $this->registerNamespaces($this->dom);
    }

    /**
     * @param \DOMDocument $obj
     * @param string $documentNamespace
     * @return \DOMDocument
     */
    public function registerNamespaces($obj, $documentNamespace = null)
    {
        if (empty($documentNamespace)) {
            $documentNamespace = $this->defaultServiceUri;
        }

        $this->xpath = new \DOMXPath($obj);
        $this->xpath->registerNamespace('unittest', Wsdl::WSDL_NS_URI);

        $this->xpath->registerNamespace('tns', $documentNamespace);
        $this->xpath->registerNamespace('soap', Wsdl::SOAP_11_NS_URI);
        $this->xpath->registerNamespace('soap12', Wsdl::SOAP_12_NS_URI);
        $this->xpath->registerNamespace('xsd', Wsdl::XSD_NS_URI);
        $this->xpath->registerNamespace('soap-enc', Wsdl::SOAP_ENC_URI);
        $this->xpath->registerNamespace('wsdl', Wsdl::WSDL_NS_URI);

        return $obj;
    }

    /**
     * @param \DOMElement $element
     */
    public function testDocumentNodes($element = null)
    {
        if (!($this->wsdl instanceof Wsdl)) {
            return;
        }

        if (null === $element) {
            $element = $this->wsdl->toDomDocument()->documentElement;
        }

        foreach ($element->childNodes as $node) {
            if (in_array($node->nodeType, [XML_ELEMENT_NODE])) {
                $this->assertNotEmpty(
                    $node->namespaceURI,
                    'Document element: ' . $node->nodeName . ' has no valid namespace. Line: ' . $node->getLineNo()
                );
                $this->testDocumentNodes($node);
            }
        }
    }
}
