<?php

namespace LaminasTest\Soap\Wsdl;

use Laminas\Soap\Exception\InvalidArgumentException;
use Laminas\Soap\Wsdl\ComplexTypeStrategy;
use Laminas\Soap\Wsdl\ComplexTypeStrategy\AnyType;
use Laminas\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex;
use Laminas\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeSequence;
use Laminas\Soap\Wsdl\ComplexTypeStrategy\Composite;
use Laminas\Soap\Wsdl\ComplexTypeStrategy\DefaultComplexType;
use LaminasTest\Soap\TestAsset\Anything;
use LaminasTest\Soap\TestAsset\Book;
use LaminasTest\Soap\TestAsset\Cookie;
use LaminasTest\Soap\WsdlTestHelper;

use function get_class;

/**
 * @group      Laminas_Soap
 * @group      Laminas_Soap_Wsdl
 */
class CompositeStrategyTest extends WsdlTestHelper
{
    public function setUp(): void
    {
        // override parent setup because it is needed only in one method
    }

    public function testCompositeApiAddingStragiesToTypes()
    {
        $strategy = new Composite([], new ArrayOfTypeSequence());
        $strategy->connectTypeToStrategy('Book', new ArrayOfTypeComplex());

        $bookStrategy   = $strategy->getStrategyOfType('Book');
        $cookieStrategy = $strategy->getStrategyOfType('Cookie');

        $this->assertInstanceOf(ArrayOfTypeComplex::class, $bookStrategy);
        $this->assertInstanceOf(ArrayOfTypeSequence::class, $cookieStrategy);
    }

    public function testConstructorTypeMapSyntax()
    {
        $typeMap = ['Book' => ArrayOfTypeComplex::class];

        $strategy = new ComplexTypeStrategy\Composite(
            $typeMap,
            new ArrayOfTypeSequence()
        );

        $bookStrategy   = $strategy->getStrategyOfType('Book');
        $cookieStrategy = $strategy->getStrategyOfType('Cookie');

        $this->assertInstanceOf(ArrayOfTypeComplex::class, $bookStrategy);
        $this->assertInstanceOf(ArrayOfTypeSequence::class, $cookieStrategy);
    }

    public function testCompositeThrowsExceptionOnInvalidType()
    {
        $strategy = new ComplexTypeStrategy\Composite();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid type given to Composite Type Map');
        $strategy->connectTypeToStrategy([], 'strategy');
    }

    public function testCompositeThrowsExceptionOnInvalidStrategy()
    {
        $strategy = new ComplexTypeStrategy\Composite([], 'invalid');
        $strategy->connectTypeToStrategy('Book', 'strategy');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Strategy for Complex Type "Book" is not a valid strategy');
        $strategy->getStrategyOfType('Book');
    }

    public function testCompositeThrowsExceptionOnInvalidStrategyPart2()
    {
        $strategy = new ComplexTypeStrategy\Composite([], 'invalid');
        $strategy->connectTypeToStrategy('Book', 'strategy');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Default Strategy for Complex Types is not a valid strategy object');
        $strategy->getStrategyOfType('Anything');
    }

    public function testCompositeDelegatesAddingComplexTypesToSubStrategies()
    {
        $this->strategy = new ComplexTypeStrategy\Composite([], new AnyType());
        $this->strategy->connectTypeToStrategy(
            Book::class,
            new ArrayOfTypeComplex()
        );
        $this->strategy->connectTypeToStrategy(
            Cookie::class,
            new DefaultComplexType()
        );

        parent::setUp();

        $this->assertEquals('tns:Book', $this->strategy->addComplexType(Book::class));
        $this->assertEquals('tns:Cookie', $this->strategy->addComplexType(Cookie::class));
        $this->assertEquals('xsd:anyType', $this->strategy->addComplexType(Anything::class));

        $this->documentNodesTest();
    }

    public function testCompositeRequiresContextForAddingComplexTypesOtherwiseThrowsException()
    {
        $strategy = new ComplexTypeStrategy\Composite();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot add complex type "Test"');
        $strategy->addComplexType('Test');
    }

    public function testGetDefaultStrategy()
    {
        $strategyClass = AnyType::class;

        $strategy = new Composite([], $strategyClass);

        $this->assertEquals($strategyClass, get_class($strategy->getDefaultStrategy()));
    }
}
