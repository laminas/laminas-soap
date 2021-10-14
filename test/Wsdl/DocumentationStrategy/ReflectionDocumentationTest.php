<?php

namespace LaminasTest\Soap\Wsdl\DocumentationStrategy;

use Laminas\Soap\Wsdl\DocumentationStrategy\ReflectionDocumentation;
use LaminasTest\Soap\TestAsset\PropertyDocumentationTestClass;
use LaminasTest\Soap\TestAsset\WsdlTestClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ReflectionDocumentationTest extends TestCase
{
    private ReflectionDocumentation $documentation;

    protected function setUp(): void
    {
        $this->documentation = new ReflectionDocumentation();
    }

    public function testGetPropertyDocumentationParsesDocComment()
    {
        $class      = new PropertyDocumentationTestClass();
        $reflection = new ReflectionClass($class);
        $actual     = $this->documentation->getPropertyDocumentation($reflection->getProperty('withoutType'));
        $this->assertEquals('Property documentation', $actual);
    }

    public function testGetPropertyDocumentationSkipsAnnotations()
    {
        $class      = new PropertyDocumentationTestClass();
        $reflection = new ReflectionClass($class);
        $actual     = $this->documentation->getPropertyDocumentation($reflection->getProperty('withType'));
        $this->assertEquals('Property documentation', $actual);
    }

    public function testGetPropertyDocumentationReturnsEmptyString()
    {
        $class      = new PropertyDocumentationTestClass();
        $reflection = new ReflectionClass($class);
        $actual     = $this->documentation->getPropertyDocumentation($reflection->getProperty('noDoc'));
        $this->assertEquals('', $actual);
    }

    public function getGetComplexTypeDocumentationParsesDocComment()
    {
        $reflection = new ReflectionClass(new WsdlTestClass());
        $actual     = $this->documentation->getComplexTypeDocumentation($reflection);
        $this->assertEquals('Test class', $actual);
    }
}
