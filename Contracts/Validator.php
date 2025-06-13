<?php

/**
 * Interface Validator
 *
 * This interface defines a contract for attribute validation implementations.
 * It allows registration of different validators based on attribute types,
 * enabling flexible and extensible validation strategies.
 *
 * Implementations of this interface can handle specific validation rules
 * by determining if they support a given property type and performing
 * the actual validation logic.
 *
 * @package Sam\AttributeValidation\Contracts
 */

namespace Sam\AttributeValidation\Contracts;

interface Validator
{
    /**
     * Determines if this validator implementation supports validating the given attribute.
     * This method helps to match specific validator implementations with appropriate properties.
     *
     * @param ValidationRule $rule We check for a coincidence of the type
     * @return bool True if this validator can validate the attribute, false otherwise
     */
    public function supports(ValidationRule $rule): bool;

    /**
     * Returns an array of errors or null
     *
     * @param string $propertyName
     * @param mixed $value
     * @param ValidationRule $rule
     * @return array<string,array<string>> - errors
     */
    public function validate(string $propertyName, mixed $value, ValidationRule $rule): array;
}
