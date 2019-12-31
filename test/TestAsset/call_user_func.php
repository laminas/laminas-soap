<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Soap;

use LaminasTest\Soap\TestAsset\MockCallUserFunc;

/**
 * Function interceptor for call_user_func.
 *
 * @return mixed Return value.
 */
function call_user_func()
{
    if (! MockCallUserFunc::$mock) {
        return call_user_func_array('\call_user_func', func_get_args());
    }

    MockCallUserFunc::$params = func_get_args();

    $result = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">'
        . '<s:Body>';

    $result .= '<TestMethodResponse xmlns="http://unit/test">'
        . '<TestMethodResult>'
        . '<TestMethodResult><dummy></dummy></TestMethodResult>'
        . '</TestMethodResult>'
        . '</TestMethodResponse>';

    $result .= '</s:Body>'
        . '</s:Envelope>';

    return $result;
}
