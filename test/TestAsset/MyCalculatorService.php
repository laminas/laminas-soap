<?php

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
