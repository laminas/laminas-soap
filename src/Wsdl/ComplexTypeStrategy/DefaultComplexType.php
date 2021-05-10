<?php

namespace Laminas\Soap\Wsdl\ComplexTypeStrategy;

use DOMElement;
use Laminas\Soap\Exception;
use Laminas\Soap\Wsdl;
use Laminas\Soap\Wsdl\DocumentationStrategy\DocumentationStrategyInterface;
use ReflectionClass;
use ReflectionProperty;

class DefaultComplexType extends AbstractComplexTypeStrategy
{
    /**
     * Add a complex type by recursively using all the class properties fetched via Reflection.
     *
     * @param string $type Name of the class to be specified
     * @return string XSD Type for the given PHP type
     * @throws Exception\InvalidArgumentException if class does not exist
     */
    public function addComplexType($type)
    {
        if (! class_exists($type)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Cannot add a complex type %s that is not an object or where '
                . 'class could not be found in "DefaultComplexType" strategy.',
                $type
            ));
        }

        $class   = new ReflectionClass($type);
        $phpType = $class->getName();

        if (($soapType = $this->scanRegisteredTypes($phpType)) !== null) {
            return $soapType;
        }

        $dom = $this->getContext()->toDomDocument();
        $soapTypeName = $this->getContext()->translateType($phpType);
        $soapType     = Wsdl::TYPES_NS . ':' . $soapTypeName;

        // Register type here to avoid recursion
        $this->getContext()->addType($phpType, $soapType);

        $defaultProperties = $class->getDefaultProperties();

        $complexType = $dom->createElementNS(Wsdl::XSD_NS_URI, 'complexType');
        $complexType->setAttribute('name', $soapTypeName);

        $all = $dom->createElementNS(Wsdl::XSD_NS_URI, 'all');

        foreach ($class->getProperties() as $property) {
            if ($property->isPublic() && preg_match_all('/@var\s+([^\s]+)/m', $property->getDocComment(), $matches)) {
                /**
                 * @todo check if 'xsd:element' must be used here (it may not be
                 * compatible with using 'complexType' node for describing other
                 * classes used as attribute types for current class
                 */
                $element = $dom->createElementNS(Wsdl::XSD_NS_URI, 'element');
                $element->setAttribute('name', $propertyName = $property->getName());
                $element->setAttribute('type', $this->getContext()->getType(trim($matches[1][0])));

                // If the default value is null, then this property is nillable.
                if ($defaultProperties[$propertyName] === null) {
                    $element->setAttribute('nillable', 'true');
                }

                $this->addPropertyDocumentation($property, $element);
                $all->appendChild($element);
            }
        }

        $complexType->appendChild($all);
        $this->addComplexTypeDocumentation($class, $complexType);
        $this->getContext()->getSchema()->appendChild($complexType);

        return $soapType;
    }

    /**
     * @return void
     */
    private function addPropertyDocumentation(ReflectionProperty $property, DOMElement $element)
    {
        if ($this->documentationStrategy instanceof DocumentationStrategyInterface) {
            $documentation = $this->documentationStrategy->getPropertyDocumentation($property);
            if ($documentation) {
                $this->getContext()->addDocumentation($element, $documentation);
            }
        }
    }

    /**
     * @return void
     */
    private function addComplexTypeDocumentation(ReflectionClass $class, DOMElement $element)
    {
        if ($this->documentationStrategy instanceof DocumentationStrategyInterface) {
            $documentation = $this->documentationStrategy->getComplexTypeDocumentation($class);
            if ($documentation) {
                $this->getContext()->addDocumentation($element, $documentation);
            }
        }
    }
}
