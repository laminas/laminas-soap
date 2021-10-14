<?php

namespace Laminas\Soap\Wsdl\DocumentationStrategy;

use ReflectionClass;
use ReflectionProperty;

use function explode;
use function implode;
use function preg_match;
use function preg_replace;
use function trim;

final class ReflectionDocumentation implements DocumentationStrategyInterface
{
    /**
     * @return string
     */
    public function getPropertyDocumentation(ReflectionProperty $property)
    {
        return $this->parseDocComment($property->getDocComment());
    }

    /**
     * @return string
     */
    public function getComplexTypeDocumentation(ReflectionClass $class)
    {
        return $this->parseDocComment($class->getDocComment());
    }

    /**
     * @param string $docComment
     * @return string
     */
    private function parseDocComment($docComment)
    {
        $documentation = [];
        foreach (explode("\n", $docComment) as $i => $line) {
            if ($i === 0) {
                continue;
            }

            $line = trim(preg_replace('/\s*\*+/', '', $line));
            if (preg_match('/^(@[a-z]|\/)/i', $line)) {
                break;
            }

            // only include newlines if we've already got documentation
            if (! empty($documentation) || $line !== '') {
                $documentation[] = $line;
            }
        }

        return implode("\n", $documentation);
    }
}
