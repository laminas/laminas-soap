<?php

namespace Laminas\Soap\Wsdl\ComplexTypeStrategy;

use Laminas\Soap\Exception;
use Laminas\Soap\Wsdl;

class ArrayOfTypeComplex extends DefaultComplexType
{
    /**
     * Add an ArrayOfType based on the xsd:complexType syntax if type[] is
     * detected in return value doc comment.
     *
     * @param  string $type
     * @return string tns:xsd-type
     * @throws Exception\InvalidArgumentException
     */
    public function addComplexType($type)
    {
        if (($soapType = $this->scanRegisteredTypes($type)) !== null) {
            return $soapType;
        }

        $singularType = $this->getSingularPhpType($type);
        $nestingLevel = $this->getNestedCount($type);

        if ($nestingLevel == 0) {
            return parent::addComplexType($singularType);
        }

        if ($nestingLevel != 1) {
            throw new Exception\InvalidArgumentException(
                'ArrayOfTypeComplex cannot return nested ArrayOfObject deeper than one level. '
                . 'Use array object properties to return deep nested data.'
            );
        }

        // The following blocks define the Array of Object structure
        return $this->addArrayOfComplexType($singularType, $type);
    }

    /**
     * Add an ArrayOfType based on the xsd:complexType syntax if type[] is
     * detected in return value doc comment.
     *
     * @param  string $singularType   e.g. '\MyNamespace\MyClassname'
     * @param  string $type           e.g. '\MyNamespace\MyClassname[]'
     * @return string tns:xsd-type   e.g. 'tns:ArrayOfMyNamespace.MyClassname'
     */
    protected function addArrayOfComplexType($singularType, $type)
    {
        if (($soapType = $this->scanRegisteredTypes($type)) !== null) {
            return $soapType;
        }

        $xsdComplexTypeName = 'ArrayOf' . $this->getContext()->translateType($singularType);
        $xsdComplexType     = Wsdl::TYPES_NS . ':' . $xsdComplexTypeName;

        // Register type here to avoid recursion
        $this->getContext()->addType($type, $xsdComplexType);

        // Process singular type using DefaultComplexType strategy
        parent::addComplexType($singularType);

        // Add array type structure to WSDL document
        $dom = $this->getContext()->toDomDocument();

        $complexType = $dom->createElementNS(Wsdl::XSD_NS_URI, 'complexType');
        $this->getContext()->getSchema()->appendChild($complexType);

        $complexType->setAttribute('name', $xsdComplexTypeName);

        $complexContent = $dom->createElementNS(Wsdl::XSD_NS_URI, 'complexContent');
        $complexType->appendChild($complexContent);

        $xsdRestriction = $dom->createElementNS(Wsdl::XSD_NS_URI, 'restriction');
        $complexContent->appendChild($xsdRestriction);
        $xsdRestriction->setAttribute('base', Wsdl::SOAP_ENC_NS . ':Array');

        $xsdAttribute = $dom->createElementNS(Wsdl::XSD_NS_URI, 'attribute');
        $xsdRestriction->appendChild($xsdAttribute);

        $xsdAttribute->setAttribute('ref', Wsdl::SOAP_ENC_NS . ':arrayType');
        $xsdAttribute->setAttributeNS(
            Wsdl::WSDL_NS_URI,
            'arrayType',
            Wsdl::TYPES_NS . ':' . $this->getContext()->translateType($singularType) . '[]'
        );

        return $xsdComplexType;
    }

    /**
     * From a nested definition with type[], get the singular PHP Type
     *
     * @param  string $type
     * @return string
     */
    protected function getSingularPhpType($type)
    {
        return str_replace('[]', '', $type);
    }

    /**
     * Return the array nesting level based on the type name
     *
     * @param  string $type
     * @return int
     */
    protected function getNestedCount($type)
    {
        return substr_count($type, '[]');
    }
}
