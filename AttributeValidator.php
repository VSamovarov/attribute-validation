<?php

declare (strict_types=1);

namespace Sam\AttributeValidation;

use Sam\AttributeValidation\Contracts\Validator;

final readonly class AttributeValidator
{
    /**
     * @param MetadataExtractor $extractor
     * @param Validator[] $validators
     */
    public function __construct(
        private MetadataExtractor $extractor,
        private iterable          $validators,
    ) {
    }

    public function validate(object $obj): array
    {
        $errors = [];
        $metadata = $this->extractor->resolve($obj);
        foreach ($metadata as $property) {
            $propertyName = $property->name();
            $rules = $property->rules();
            $value = $property->value($obj);
            foreach ($rules as $rule) {
                foreach ($this->validators as $validator) {
                    if ($validator->supports($rule)) {
                        $result = $validator->validate($propertyName, $value, $rule);
                        if (!empty($result)) {
                            $errors[$propertyName] = array_merge($errors[$propertyName] ?? [], $result);
                        }
                    }
                }
            }
        }
        return $errors;
    }
}
