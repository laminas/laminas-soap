<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Soap\TestAsset\fulltests;

/**
 * @category   Laminas
 * @package    Laminas_Soap
 * @subpackage UnitTests
 */
class Server1
{
    /**
     * @param  \LaminasTest\Soap\TestAsset\fulltests\ComplexTypeB
     * @return \LaminasTest\Soap\TestAsset\fulltests\ComplexTypeA[]
     */
    public function request($request)
    {
        $a = new ComplexTypeA();

        $b1 = new ComplexTypeB();
        $b1->bar = "bar";
        $b1->foo = "bar";
        $a->baz[] = $b1;

        $b2 = new ComplexTypeB();
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
class ComplexTypeB
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
class ComplexTypeA
{
    /**
     * @var \LaminasTest\Soap\TestAsset\fulltests\ComplexTypeB[]
     */
    public $baz = array();
}

if (isset($_GET['wsdl'])) {
    $server = new \Laminas\Soap\AutoDiscover(new \Laminas\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex());
} else {
    $uri = "http://".$_SERVER['HTTP_HOST']."/".$_SERVER['PHP_SELF']."?wsdl";
    $server = new \Laminas\Soap\Server($uri);
}
$server->setClass('\LaminasTest\Soap\TestAsset\fulltests\Server1');
$server->handle();
