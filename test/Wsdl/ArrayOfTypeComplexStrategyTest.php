<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Soap\Wsdl;

require_once __DIR__."/../TestAsset/commontypes.php";

use Laminas\Soap\Wsdl;
use Laminas\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex;

/**
 * @category   Laminas
 * @package    Laminas_Soap
 * @subpackage UnitTests
 * @group      Laminas_Soap
 * @group      Laminas_Soap_Wsdl
 */
class ArrayOfTypeComplexStrategyTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Laminas\Soap\Wsdl */
    private $wsdl;

    /** @var \Laminas\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex */
    private $strategy;

    public function setUp()
    {
        $this->strategy = new ArrayOfTypeComplex();
        $this->wsdl = new Wsdl('MyService', 'http://localhost/MyService.php', $this->strategy);
    }

    public function testNestingObjectsDeepMakesNoSenseThrowingException()
    {
        $this->setExpectedException('Laminas\Soap\Exception\InvalidArgumentException', 'ArrayOfTypeComplex cannot return nested ArrayOfObject deeper than one level');
        $this->wsdl->addComplexType('\LaminasTest\Soap\TestAsset\ComplexTest[][]');
    }

    public function testAddComplexTypeOfNonExistingClassThrowsException()
    {
        $this->setExpectedException('Laminas\Soap\Exception\InvalidArgumentException', 'Cannot add a complex type \LaminasTest\Soap\TestAsset\UnknownClass that is not an object or where class');
        $this->wsdl->addComplexType('\LaminasTest\Soap\TestAsset\UnknownClass[]');
    }

    /**
     * @group Laminas-5046
     */
    public function testArrayOfSimpleObject()
    {
        $return = $this->wsdl->addComplexType('\LaminasTest\Soap\TestAsset\ComplexTest[]');
        $this->assertEquals("tns:ArrayOfComplexTest", $return);

        $wsdl = $this->wsdl->toXML();

        $this->assertContains(
            '<xsd:complexType name="ArrayOfComplexTest"><xsd:complexContent><xsd:restriction base="soap-enc:Array"><xsd:attribute ref="soap-enc:arrayType" wsdl:arrayType="tns:ComplexTest[]"/></xsd:restriction></xsd:complexContent></xsd:complexType>',
            $wsdl,
            $wsdl
        );

        $this->assertContains(
            '<xsd:complexType name="ComplexTest"><xsd:all><xsd:element name="var" type="xsd:int"/></xsd:all></xsd:complexType>',
            $wsdl
        );
    }

    public function testThatOverridingStrategyIsReset()
    {
        $return = $this->wsdl->addComplexType('\LaminasTest\Soap\TestAsset\ComplexTest[]');
        $this->assertEquals("tns:ArrayOfComplexTest", $return);
        // $this->assertTrue($this->wsdl->getComplexTypeStrategy() instanceof \Laminas\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplexStrategy);

        $wsdl = $this->wsdl->toXML();
    }

    /**
     * @group Laminas-5046
     */
    public function testArrayOfComplexObjects()
    {
        $return = $this->wsdl->addComplexType('\LaminasTest\Soap\TestAsset\ComplexObjectStructure[]');
        $this->assertEquals("tns:ArrayOfComplexObjectStructure", $return);

        $wsdl = $this->wsdl->toXML();

        $this->assertContains(
            '<xsd:complexType name="ArrayOfComplexObjectStructure"><xsd:complexContent><xsd:restriction base="soap-enc:Array"><xsd:attribute ref="soap-enc:arrayType" wsdl:arrayType="tns:ComplexObjectStructure[]"/></xsd:restriction></xsd:complexContent></xsd:complexType>',
            $wsdl,
            $wsdl
        );

        $this->assertContains(
            '<xsd:complexType name="ComplexObjectStructure"><xsd:all><xsd:element name="boolean" type="xsd:boolean"/><xsd:element name="string" type="xsd:string"/><xsd:element name="int" type="xsd:int"/><xsd:element name="array" type="soap-enc:Array"/></xsd:all></xsd:complexType>',
            $wsdl
        );
    }

    public function testArrayOfObjectWithObject()
    {
        $return = $this->wsdl->addComplexType('\LaminasTest\Soap\TestAsset\ComplexObjectWithObjectStructure[]');
        $this->assertEquals("tns:ArrayOfComplexObjectWithObjectStructure", $return);

        $wsdl = $this->wsdl->toXML();

        $this->assertContains(
            '<xsd:complexType name="ArrayOfComplexObjectWithObjectStructure"><xsd:complexContent><xsd:restriction base="soap-enc:Array"><xsd:attribute ref="soap-enc:arrayType" wsdl:arrayType="tns:ComplexObjectWithObjectStructure[]"/></xsd:restriction></xsd:complexContent></xsd:complexType>',
            $wsdl
        );

        $this->assertContains(
            '<xsd:complexType name="ComplexObjectWithObjectStructure"><xsd:all><xsd:element name="object" type="tns:ComplexTest" nillable="true"/></xsd:all></xsd:complexType>',
            $wsdl,
            $wsdl
        );

        $this->assertContains(
            '<xsd:complexType name="ComplexTest"><xsd:all><xsd:element name="var" type="xsd:int"/></xsd:all></xsd:complexType>',
            $wsdl
        );
    }

    /**
     * @group Laminas-4937
     */
    public function testAddingTypesMultipleTimesIsSavedOnlyOnce()
    {
        $return = $this->wsdl->addComplexType('\LaminasTest\Soap\TestAsset\ComplexObjectWithObjectStructure[]');
        $return = $this->wsdl->addComplexType('\LaminasTest\Soap\TestAsset\ComplexObjectWithObjectStructure[]');

        $wsdl = $this->wsdl->toXML();

        $this->assertEquals(1,
            substr_count($wsdl, 'wsdl:arrayType="tns:ComplexObjectWithObjectStructure[]"')
        );
        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="ArrayOfComplexObjectWithObjectStructure">')
        );
        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="ComplexTest">')
        );
    }

    /**
     * @group Laminas-4937
     */
    public function testAddingSingularThenArrayTypeIsRecognizedCorretly()
    {
        $return = $this->wsdl->addComplexType('\LaminasTest\Soap\TestAsset\ComplexObjectWithObjectStructure');
        $return = $this->wsdl->addComplexType('\LaminasTest\Soap\TestAsset\ComplexObjectWithObjectStructure[]');

        $wsdl = $this->wsdl->toXML();

        $this->assertEquals(1,
            substr_count($wsdl, 'wsdl:arrayType="tns:ComplexObjectWithObjectStructure[]"')
        );
        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="ArrayOfComplexObjectWithObjectStructure">')
        );
        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="ComplexTest">')
        );
    }

    /**
     * @group Laminas-5149
     */
    public function testArrayOfComplexNestedObjectsIsCoveredByStrategy()
    {
        $return = $this->wsdl->addComplexType('\LaminasTest\Soap\TestAsset\ComplexTypeA');
        $wsdl = $this->wsdl->toXml();
        $this->assertTrue(is_string($wsdl)); // no exception was thrown
    }

    /**
     * @group Laminas-5149
     */
    public function testArrayOfComplexNestedObjectsIsCoveredByStrategyAndAddsAllTypesRecursivly()
    {
        $return = $this->wsdl->addComplexType('\LaminasTest\Soap\TestAsset\ComplexTypeA');
        $wsdl = $this->wsdl->toXml();

        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="ComplexTypeA">'),
            'No definition of complex type A found.'
        );
        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="ArrayOfComplexTypeB">'),
            'No definition of complex type B array found.'
        );
        $this->assertEquals(1,
            substr_count($wsdl, 'wsdl:arrayType="tns:ComplexTypeB[]"'),
            'No usage of Complex Type B array found.'
        );
    }
}
