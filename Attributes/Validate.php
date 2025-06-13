<?php

declare (strict_types=1);

/**
 * Attribute class for defining validation rules and messages for a property.
 *
 * This attribute is used to provide validation rules and corresponding
 * error messages for a property in classes. It can be applied multiple times
 * to a property.
 *
 * Implements the ValidationRule contract.
 *
 * @param array<string|\Illuminate\Contracts\Validation\Rule|array> $rules An array of validation rules.
 * @param array<string, string> $messages An array of custom error messages where the key is the rule name, and the value is the message.
 */

namespace Sam\AttributeValidation\Attributes;

use Sam\AttributeValidation\Contracts\ValidationRule;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class Validate implements ValidationRule
{
    /**
     * @param array<string|\Illuminate\Contracts\Validation\Rule|array> $rules
     * @param array<string, string> $messages
     */
    public function __construct(
        public array $rules,
        public array $messages = [],
    ) {
    }
}
