<?php

namespace LaminasTest\Soap\Wsdl;

use Laminas\Soap\Exception\InvalidArgumentException;
use Laminas\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeSequence;
use LaminasTest\Soap\TestAsset\SequenceTest;
use LaminasTest\Soap\WsdlTestHelper;

class ArrayOfTypeSequenceStrategyTest extends WsdlTestHelper
{
    public function setUp(): void
    {
        $this->strategy = new ArrayOfTypeSequence();

        parent::setUp();
    }

    /**
     * @dataProvider dataProviderForFunctionReturningSimpleArrayOfBasicTypes
     */
    public function testFunctionReturningSimpleArrayOfBasicTypes(string $type, string $arrayTypeName)
    {
        $this->wsdl->addComplexType($type . '[]');
        // test duplicates also
        $this->wsdl->addComplexType($type . '[]');

        $nodes = $this->xpath->query('//wsdl:types/xsd:schema/xsd:complexType[@name="' . $arrayTypeName . '"]');
        $this->assertEquals(1, $nodes->length, 'Missing complex type declaration');

        $nodes = $this->xpath->query('xsd:sequence/xsd:element', $nodes->item(0));
        $this->assertEquals(1, $nodes->length, 'Missing complex type element declaration');

        $this->assertEquals(
            'item',
            $nodes->item(0)->getAttribute('name'),
            'Wrong complex type element name attribute'
        );
        $this->assertEquals(
            'xsd:' . $type,
            $nodes->item(0)->getAttribute('type'),
            'Wrong complex type type attribute value'
        );
        $this->assertEquals(
            '0',
            $nodes->item(0)->getAttribute('minOccurs'),
            'Wrong complex type minOccurs attribute value'
        );
        $this->assertEquals(
            'unbounded',
            $nodes->item(0)->getAttribute('maxOccurs'),
            'Wrong complex type maxOccurs attribute value'
        );

        $this->documentNodesTest();
    }

    /** @psalm-return array<array-key, array{0: string, 1: string}> */
    public function dataProviderForFunctionReturningSimpleArrayOfBasicTypes(): array
    {
        return [
            ['int', 'ArrayOfInt'],
            ['string', 'ArrayOfString'],
            ['boolean', 'ArrayOfBoolean'],
            ['float', 'ArrayOfFloat'],
            ['double', 'ArrayOfDouble'],
        ];
    }

    /**
     * @dataProvider dataProviderForNestedTypesDefinitions
     */
    public function testNestedTypesDefinitions(
        string $stringDefinition,
        string $definedTypeName,
        array $nestedTypeNames
    ) {
        $return = $this->wsdl->addComplexType($stringDefinition);
        $this->assertEquals('tns:' . $definedTypeName, $return);

        foreach ($nestedTypeNames as $nestedTypeName => $typeName) {
            $nodes = $this->xpath->query('//wsdl:types/xsd:schema/xsd:complexType[@name="' . $nestedTypeName . '"]');
            $this->assertEquals(1, $nodes->length, 'Invalid first level of nested element definition');

            $nodes = $this->xpath->query('xsd:sequence/xsd:element', $nodes->item(0));
            $this->assertEquals(1, $nodes->length, 'Invalid element in first level of nested element definition');

            $this->assertEquals(
                'item',
                $nodes->item(0)->getAttribute('name'),
                'Wrong complex type element name attribute'
            );
            $this->assertEquals(
                '0',
                $nodes->item(0)->getAttribute('minOccurs'),
                'Wrong complex type minOccurs attribute value'
            );
            $this->assertEquals(
                'unbounded',
                $nodes->item(0)->getAttribute('maxOccurs'),
                'Wrong complex type maxOccurs attribute value'
            );
            $this->assertEquals(
                $typeName,
                $nodes->item(0)->getAttribute('type'),
                'Wrong complex type type attribute value'
            );
        }

        $this->documentNodesTest();
    }

    /**
     * @psalm-return array<array-key, array{
     *     0: string,
     *     1: string,
     *     3: array<string, string>
     * }>
     */
    public function dataProviderForNestedTypesDefinitions(): array
    {
        return [
            [
                'string[][]',
                'ArrayOfArrayOfString',
                [
                    'ArrayOfString'        => 'xsd:string',
                    'ArrayOfArrayOfString' => 'tns:ArrayOfString',
                ],
            ],
            [
                'string[][][]',
                'ArrayOfArrayOfArrayOfString',
                [
                    'ArrayOfString'               => 'xsd:string',
                    'ArrayOfArrayOfString'        => 'tns:ArrayOfString',
                    'ArrayOfArrayOfArrayOfString' => 'tns:ArrayOfArrayOfString',
                ],
            ],
            [
                'string[][][][]',
                'ArrayOfArrayOfArrayOfArrayOfString',
                [
                    'ArrayOfString'                      => 'xsd:string',
                    'ArrayOfArrayOfString'               => 'tns:ArrayOfString',
                    'ArrayOfArrayOfArrayOfString'        => 'tns:ArrayOfArrayOfString',
                    'ArrayOfArrayOfArrayOfArrayOfString' => 'tns:ArrayOfArrayOfArrayOfString',
                ],
            ],
            [
                'int[][]',
                'ArrayOfArrayOfInt',
                [
                    'ArrayOfInt'        => 'xsd:int',
                    'ArrayOfArrayOfInt' => 'tns:ArrayOfInt',
                ],
            ],
        ];
    }

    public function testAddComplexTypeObject()
    {
        $return = $this->wsdl->addComplexType(SequenceTest::class);

        $this->assertEquals('tns:SequenceTest', $return);

        $nodes = $this->xpath->query('//xsd:complexType[@name="SequenceTest"]');
        $this->assertEquals(1, $nodes->length, 'Missing complex type: SequenceTest');

        $nodes = $this->xpath->query('xsd:all/xsd:element', $nodes->item(0));
        $this->assertEquals(1, $nodes->length, 'Missing element definition in complex type: SequenceTest');

        $this->assertEquals('var', $nodes->item(0)->getAttribute('name'), 'Invalid name attribute value');
        $this->assertEquals('xsd:int', $nodes->item(0)->getAttribute('type'), 'Invalid type attribute value');

        $this->documentNodesTest();
    }

    public function testAddComplexTypeArrayOfObject()
    {
        $return = $this->wsdl->addComplexType('\LaminasTest\Soap\TestAsset\ComplexTypeA[]');
        $this->assertEquals('tns:ArrayOfComplexTypeA', $return);

        // class a
        $nodes = $this->xpath->query('//wsdl:types/xsd:schema/xsd:complexType[@name="ComplexTypeA"]');
        $this->assertEquals(1, $nodes->length, 'Missing complex type definition.');

        $nodes = $this->xpath->query('xsd:all/xsd:element', $nodes->item(0));

        $this->assertEquals(1, $nodes->length, 'Missing complex type element declaration');

        $this->assertEquals(
            'baz',
            $nodes->item(0)->getAttribute('name'),
            'Wrong complex type element name attribute'
        );
        $this->assertEquals(
            'tns:ArrayOfComplexTypeB',
            $nodes->item(0)->getAttribute('type'),
            'Wrong complex type type attribute value'
        );

        // class b
        $nodes = $this->xpath->query('//wsdl:types/xsd:schema/xsd:complexType[@name="ComplexTypeB"]');
        $this->assertEquals(1, $nodes->length, 'Missing complex type definition.');

        foreach (
            [
                'bar' => 'xsd:string',
                'foo' => 'xsd:string',
            ] as $name => $type
        ) {
            $node = $this->xpath->query('xsd:all/xsd:element[@name="' . $name . '"]', $nodes->item(0));

            $this->assertEquals(
                $name,
                $node->item(0)->getAttribute('name'),
                'Invalid name attribute value in complex object definition'
            );
            $this->assertEquals(
                $type,
                $node->item(0)->getAttribute('type'),
                'Invalid type name in complex object definition'
            );
            $this->assertEquals(
                'true',
                $node->item(0)->getAttribute('nillable'),
                'Invalid nillable attribute value'
            );
        }

        // array of class a and class b
        foreach (
            [
                'ArrayOfComplexTypeB' => 'ComplexTypeB',
                'ArrayOfComplexTypeA' => 'ComplexTypeA',
            ] as $arrayTypeName => $typeName
        ) {
            $nodes = $this->xpath->query(
                '//wsdl:types/xsd:schema/xsd:complexType[@name="' . $arrayTypeName . '"]'
            );
            $this->assertEquals(1, $nodes->length, 'Missing complex type definition.');

            $nodes = $this->xpath->query('xsd:sequence/xsd:element', $nodes->item(0));
            $this->assertEquals(1, $nodes->length, 'Missing complex type element declaration');

            $this->assertEquals(
                'item',
                $nodes->item(0)->getAttribute('name'),
                'Wrong complex type element name attribute'
            );
            $this->assertEquals(
                'tns:' . $typeName,
                $nodes->item(0)->getAttribute('type'),
                'Wrong complex type type attribute value'
            );
            $this->assertEquals(
                '0',
                $nodes->item(0)->getAttribute('minOccurs'),
                'Wrong complex type minOccurs attribute value'
            );
            $this->assertEquals(
                'unbounded',
                $nodes->item(0)->getAttribute('maxOccurs'),
                'Wrong complex type maxOccurs attribute value'
            );
        }

        $this->documentNodesTest();
    }

    public function testAddComplexTypeOfNonExistingClassThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot add a complex type');
        $this->wsdl->addComplexType('LaminasTest\Soap\Wsdl\UnknownClass[]');
    }
}
