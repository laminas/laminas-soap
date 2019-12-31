<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Soap\TestAsset;

/**
 * Allows mocking of call_user_func.
 */
class MockCallUserFunc
{
    /**
     * Whether to mock the call_user_func function.
     *
     * @var boolean
     */
    public static $mock = false;

    /**
     * Passed parameters.
     *
     * @var array
     */
    public static $params = array();
}
