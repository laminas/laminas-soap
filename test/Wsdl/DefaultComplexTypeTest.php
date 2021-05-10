<?php

namespace LaminasTest\Soap\Wsdl;

use Laminas\Soap\Wsdl\ComplexTypeStrategy\DefaultComplexType;
use Laminas\Soap\Wsdl\DocumentationStrategy\DocumentationStrategyInterface;
use LaminasTest\Soap\TestAsset\PublicPrivateProtected;
use LaminasTest\Soap\TestAsset\WsdlTestClass;
use LaminasTest\Soap\WsdlTestHelper;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use ReflectionClass;
use ReflectionProperty;

/**
 * @covers \Laminas\Soap\Wsdl\ComplexTypeStrategy\DefaultComplexType
 */
class DefaultComplexTypeTest extends WsdlTestHelper
{
    use ProphecyTrait;

    /**
     * @var DefaultComplexType
     */
    protected $strategy;

    public function setUp(): void
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

        $this->documentNodesTest();
    }

    public function testDoubleClassesAreDiscoveredByStrategy()
    {
        $this->strategy->addComplexType('LaminasTest\Soap\TestAsset\WsdlTestClass');
        $this->strategy->addComplexType('\LaminasTest\Soap\TestAsset\WsdlTestClass');

        $nodes = $this->xpath->query('//xsd:complexType[@name="WsdlTestClass"]');
        $this->assertEquals(1, $nodes->length);

        $this->documentNodesTest();
    }

    public function testDocumentationStrategyCalled()
    {
        $documentation = $this->prophesize(DocumentationStrategyInterface::class);
        $documentation->getPropertyDocumentation(Argument::type(ReflectionProperty::class))
            ->shouldBeCalledTimes(2)
            ->willReturn('Property');
        $documentation->getComplexTypeDocumentation(Argument::type(ReflectionClass::class))
            ->shouldBeCalledTimes(1)
            ->willReturn('Complex type');
        $this->strategy->setDocumentationStrategy($documentation->reveal());
        $this->strategy->addComplexType(WsdlTestClass::class);
    }
}
