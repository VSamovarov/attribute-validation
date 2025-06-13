<?php

declare (strict_types=1);

namespace Sam\AttributeValidation;

use Sam\AttributeValidation\Contracts\ValidationRule as ValidationRuleInterface;
use Sam\AttributeValidation\Contracts\MetadataExtractor as MetadataExtractorInterface;

class MetadataExtractor implements MetadataExtractorInterface
{
    private static array $cache = [];

    /**
     * {@inheritdoc}
     */
    public function resolve(object $obj): array
    {
        return self::$cache[get_class($obj)] ??= $this->getRules($obj);
    }

    /**
     * Returns an array of validation rules for the object.
     *
     * @param object $obj
     * @return array<string,PropertyMetadata> // field => rules
     */
    private function getRules(object $obj): array
    {
        $reflection = new \ReflectionClass($obj);
        $rules = [];
        foreach ($reflection->getProperties() as $property) {
            $propertyRules = [];
            foreach ($property->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();
                if ($instance instanceof ValidationRuleInterface) {
                    $propertyRules[] = $instance;
                }
            }
            if (!empty($propertyRules)) {
                $rules[$property->getName()] = new PropertyMetadata($property, $propertyRules);
            }
        }
        return $rules;
    }

    public static function clearCache(): void
    {
        self::$cache = [];
    }
}
