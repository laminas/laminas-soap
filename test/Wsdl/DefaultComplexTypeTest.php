<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Soap\Wsdl;

use Laminas\Soap\Wsdl\ComplexTypeStrategy\DefaultComplexType;
use LaminasTest\Soap\TestAsset\PublicPrivateProtected;
use LaminasTest\Soap\WsdlTestHelper;

require_once __DIR__ . '/../TestAsset/commontypes.php';

/**
 * @covers \Laminas\Soap\Wsdl\ComplexTypeStrategy\DefaultComplexType
 */
class DefaultComplexTypeTest extends WsdlTestHelper
{
    /**
     * @var DefaultComplexType
     */
    protected $strategy;

    public function setUp()
    {
        $this->strategy = new DefaultComplexType();

        parent::setUp();
    }

    /**
     * @group Laminas-5944
     */
    public function testOnlyPublicPropertiesAreDiscoveredByStrategy()
    {
        $this->strategy->addComplexType('LaminasTest\Soap\TestAsset\PublicPrivateProtected');

        $nodes = $this->xpath->query('//xsd:element[@name="'.(PublicPrivateProtected::PROTECTED_VAR_NAME).'"]');
        $this->assertEquals(0, $nodes->length, 'Document should not contain protected fields');

        $nodes = $this->xpath->query('//xsd:element[@name="'.(PublicPrivateProtected::PRIVATE_VAR_NAME).'"]');
        $this->assertEquals(0, $nodes->length, 'Document should not contain private fields');

        $this->testDocumentNodes();
    }

    public function testDoubleClassesAreDiscoveredByStrategy()
    {
        $this->strategy->addComplexType('LaminasTest\Soap\TestAsset\WsdlTestClass');
        $this->strategy->addComplexType('\LaminasTest\Soap\TestAsset\WsdlTestClass');

        $nodes = $this->xpath->query('//xsd:complexType[@name="WsdlTestClass"]');
        $this->assertEquals(1, $nodes->length);

        $this->testDocumentNodes();
    }
}
