<?php

declare (strict_types=1);

namespace Sam\AttributeValidation;

use ReflectionProperty;
use Sam\AttributeValidation\Contracts\ValidationRule as ValidationRuleInterface;
use Sam\AttributeValidation\Contracts\PropertyMetadata as ValidatedPropertyMetadataInterface;

readonly class PropertyMetadata implements ValidatedPropertyMetadataInterface
{
    /**
     * @param ReflectionProperty $property
     * @param ValidationRuleInterface[] $rules
     */
    final public function __construct(public ReflectionProperty $property, public array $rules)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return $this->property->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function value(object $object): mixed
    {
        return $this->property->getValue($object);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return $this->rules;
    }
}
