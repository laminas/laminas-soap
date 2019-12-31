<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

require_once "Laminas/Soap/AutoDiscover.php";
require_once "Laminas/Soap/Server.php";
require_once "Laminas/Soap/Wsdl/Strategy/ArrayOfTypeComplex.php";

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

        return [$a];
    }
}

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

class Laminas_Soap_Wsdl_ComplexTypeA
{
    /**
     * @var Laminas_Soap_Wsdl_ComplexTypeB[]
     */
    public $baz = [];
}

if (isset($_GET['wsdl'])) {
    $server = new Laminas\Soap\AutoDiscover(new Laminas\Soap\Wsdl\Strategy\ArrayOfTypeComplex());
} else {
    $uri = "http://".$_SERVER['HTTP_HOST']."/".$_SERVER['PHP_SELF']."?wsdl";
    $server = new Laminas\Soap\Server($uri);
}
$server->setClass('Laminas_Soap_Service_Server1');
$server->handle();
