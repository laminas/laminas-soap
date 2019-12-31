<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Soap\TestAsset;

class PropertyDocumentationTestClass
{
    /**
     * Property documentation
     */
    public $withoutType;

    /**
     * Property documentation
     * @type int
     */
    public $withType;

    public $noDoc;
}
