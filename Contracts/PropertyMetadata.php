<?php


/**
 * Interface PropertyMetadata
 *
 * This interface defines the contract for handling property metadata.
 * It includes methods for retrieving the property's name, validation rules,
 * and the value of the property from a given object.
 */

namespace Sam\AttributeValidation\Contracts;

use Sam\AttributeValidation\Contracts\ValidationRule as ValidationRuleInterface;

interface PropertyMetadata
{
    /**
     * Get property name
     * @return string
     */
    public function name(): string;


    /**
     * Returns validation rules defined for the DTO property
     * Each rule must implement ValidationRule interface
     * These rules are specified as attributes before the validated property
     *
     * @return ValidationRuleInterface[] Array of validation rules
     */
    public function rules(): array;


    /**
     * Returns the value of the property for a specific object instance
     *
     * @param object $object Object instance to get property value from
     * @return mixed The value of the property
     */
    public function value(object $object): mixed;

}
