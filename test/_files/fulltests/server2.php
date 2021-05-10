<?php

require_once "Laminas/Soap/AutoDiscover.php";
require_once "Laminas/Soap/Server.php";
require_once "Laminas/Soap/Wsdl/Strategy/ArrayOfTypeComplex.php";

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

class Laminas_Soap_Service_Server2
{
    /**
     * @param  string $foo
     * @param  string $bar
     * @return Laminas_Soap_Wsdl_ComplexTypeB
     */
    public function request($foo, $bar)
    {
        $b = new Laminas_Soap_Wsdl_ComplexTypeB();
        $b->bar = $bar;
        $b->foo = $foo;
        return $b;
    }
}

if (isset($_GET['wsdl'])) {
    $server = new Laminas_Soap_AutoDiscover(new Laminas_Soap_Wsdl_Strategy_ArrayOfTypeComplex());
} else {
    $uri = "http://".$_SERVER['HTTP_HOST']."/".$_SERVER['PHP_SELF']."?wsdl";
    $server = new Laminas_Soap_Server($uri);
}
$server->setClass('Laminas_Soap_Service_Server2');
$server->handle();
