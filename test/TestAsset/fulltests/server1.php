<?php

namespace LaminasTest\Soap\TestAsset\fulltests;

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

        return [$a];
    }
}

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

class ComplexTypeA
{
    /**
     * @var \LaminasTest\Soap\TestAsset\fulltests\ComplexTypeB[]
     */
    public $baz = [];
}

if (isset($_GET['wsdl'])) {
    $server = new \Laminas\Soap\AutoDiscover(new \Laminas\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex());
} else {
    $uri = "http://".$_SERVER['HTTP_HOST']."/".$_SERVER['PHP_SELF']."?wsdl";
    $server = new \Laminas\Soap\Server($uri);
}
$server->setClass('\LaminasTest\Soap\TestAsset\fulltests\Server1');
$server->handle();
