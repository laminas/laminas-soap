<?php

namespace LaminasTest\Soap\TestAsset\fulltests;

require_once __DIR__ . '/server1.php';

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
    $uri = "http://".($_SERVER['HTTP_HOST'] ?? 'localhost')."/".($_SERVER['PHP_SELF'] ?? '')."?wsdl";
    $server = new \Laminas\Soap\Server($uri);
}
$server->setClass('LaminasTest\Soap\TestAsset\fulltests\Server2');
$server->handle();
