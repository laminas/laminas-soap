<?php

namespace LaminasTest\Soap\TestAsset;

/**
 * Allows mocking of call_user_func.
 */
class MockCallUserFunc
{
    /**
     * Whether to mock the call_user_func function.
     *
     * @var bool
     */
    public static $mock = false;

    /**
     * Passed parameters.
     *
     * @var array
     */
    public static $params = [];
}
