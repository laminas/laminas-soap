<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Soap\Wsdl;

use Laminas\Soap\Wsdl;
use Laminas\Soap\Wsdl\ComplexTypeStrategy\DefaultComplexType;

/**
 * @category   Laminas
 * @package    Laminas_Soap
 * @subpackage UnitTests
 * @group      Laminas_Soap
 * @group      Laminas_Soap_Wsdl
 */
class DefaultComplexTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Laminas_Soap_Wsdl
     */
    private $wsdl;

    /**
     * @var Laminas_Soap_Wsdl_Strategy_DefaultComplexType
     */
    private $strategy;

    public function setUp()
    {
        $this->strategy = new DefaultComplexType();
        $this->wsdl = new Wsdl("TestService", "https://getlaminas.org/soap/unittests");
        $this->wsdl->setComplexTypeStrategy($this->strategy);
        $this->strategy->setContext($this->wsdl);
    }

    /**
     * @group Laminas-5944
     */
    public function testOnlyPublicPropertiesAreDiscoveredByStrategy()
    {
        $this->strategy->addComplexType('\LaminasTest\Soap\Wsdl\PublicPrivateProtected');

        $xml = $this->wsdl->toXML();
        $this->assertNotContains( PublicPrivateProtected::PROTECTED_VAR_NAME, $xml);
        $this->assertNotContains( PublicPrivateProtected::PRIVATE_VAR_NAME, $xml);
    }
}

class PublicPrivateProtected
{
    const PROTECTED_VAR_NAME = 'bar';
    const PRIVATE_VAR_NAME = 'baz';

    /**
     * @var string
     */
    public $foo;

    /**
     * @var string
     */
    protected $bar;

    /**
     * @var string
     */
    private $baz;
}
