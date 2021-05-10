<?php

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
