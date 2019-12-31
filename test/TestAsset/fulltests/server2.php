<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Soap\TestAsset\fulltests;

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

class Server2
{
    /**
     * @param  string $foo
     * @param  string $bar
     * @return \LaminasTest\Soap\TestAsset\fulltests\ComplexTypeB
     */
    public function request($foo, $bar)
    {
        $b = new ComplexTypeB();
        $b->bar = $bar;
        $b->foo = $foo;
        return $b;
    }
}

if (isset($_GET['wsdl'])) {
    $server = new \Laminas\Soap\AutoDiscover(new \Laminas\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex());
} else {
    $uri = "http://".$_SERVER['HTTP_HOST']."/".$_SERVER['PHP_SELF']."?wsdl";
    $server = new \Laminas\Soap\Server($uri);
}
$server->setClass('LaminasTest\Soap\TestAsset\fulltests\Server2');
$server->handle();
