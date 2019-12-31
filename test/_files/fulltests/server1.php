<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

require_once "Laminas/Soap/AutoDiscover.php";
require_once "Laminas/Soap/Server.php";
require_once "Laminas/Soap/Wsdl/Strategy/ArrayOfTypeComplex.php";

/**
 * @category   Laminas
 * @package    Laminas_Soap
 * @subpackage UnitTests
 */
class Laminas_Soap_Service_Server1
{
    /**
     * @param  Laminas_Soap_Wsdl_ComplexTypeB
     * @return Laminas_Soap_Wsdl_ComplexTypeA[]
     */
    public function request($request)
    {
        $a = new Laminas_Soap_Wsdl_ComplexTypeA();

        $b1 = new Laminas_Soap_Wsdl_ComplexTypeB();
        $b1->bar = "bar";
        $b1->foo = "bar";
        $a->baz[] = $b1;

        $b2 = new Laminas_Soap_Wsdl_ComplexTypeB();
        $b2->bar = "foo";
        $b2->foo = "foo";
        $a->baz[] = $b2;

        $a->baz[] = $request;

        return array($a);
    }
}

/**
 * @category   Laminas
 * @package    Laminas_Soap
 * @subpackage UnitTests
 */
class Laminas_Soap_Wsdl_ComplexTypeB
{
    /**
     * @var string
     */
    public $bar;
    /**
     * @var string
     */
    public $foo;
}

/**
 * @category   Laminas
 * @package    Laminas_Soap
 * @subpackage UnitTests
 */
class Laminas_Soap_Wsdl_ComplexTypeA
{
    /**
     * @var Laminas_Soap_Wsdl_ComplexTypeB[]
     */
    public $baz = array();
}

if (isset($_GET['wsdl'])) {
    $server = new Laminas_Soap_AutoDiscover(new Laminas_Soap_Wsdl_Strategy_ArrayOfTypeComplex());
} else {
    $uri = "http://".$_SERVER['HTTP_HOST']."/".$_SERVER['PHP_SELF']."?wsdl";
    $server = new Laminas_Soap_Server($uri);
}
$server->setClass('Laminas_Soap_Service_Server1');
$server->handle();
