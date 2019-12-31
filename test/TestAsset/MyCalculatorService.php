<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Soap\TestAsset;

/**
 * MyCalculatorService
 *
 * Class used in DocumentLiteralWrapperTest
 */
class MyCalculatorService
{
    /**
     * @param int $x
     * @param int $y
     * @return int
     */
    public function add($x, $y)
    {
        return $x + $y;
    }
}
