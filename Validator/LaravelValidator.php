<?php

declare (strict_types=1);

namespace Sam\AttributeValidation\Validator;

use Sam\AttributeValidation\Attributes\Validate;
use Sam\AttributeValidation\Contracts\Validator;
use Sam\AttributeValidation\Contracts\ValidationRule;
use Illuminate\Validation\Factory;

class LaravelValidator implements Validator
{
    public function __construct(
        private Factory $validatorFactory
    ) {
    }

    public function supports(ValidationRule $rule): bool
    {
        return $rule instanceof Validate;
    }

    public function validate(string $propertyName, mixed $value, ValidationRule $rule): array
    {
        $errors = [];
        if ($rule instanceof Validate) {
            $validator = $this->validatorFactory->make(
                data: [$propertyName => $value],
                rules: [$propertyName => $rule->rules],
                messages: [$propertyName => $rule->messages]
            );
            if ($validator->fails()) {
                $err = $validator->errors();
                $errors = $err->all();
            }
        }
        return $errors;
    }
}
