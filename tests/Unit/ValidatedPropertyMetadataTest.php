<?php

use Sam\AttributeValidation\PropertyMetadata;
use Sam\AttributeValidation\Contracts\ValidationRule;

it('returns the name of the property', function () {
    // Arrange
    $target = new class () {
        public string $testProperty = 'testValue';
    };

    $reflectionProperty = new ReflectionProperty($target, 'testProperty');
    $metadata = new PropertyMetadata($reflectionProperty, []);

    // Act
    $propertyName = $metadata->name();

    // Assert
    expect($propertyName)->toBe('testProperty');
});

it('returns the value of the property from the given object', function () {
    // Arrange
    $target = new class () {
        public string $testProperty = 'testValue';
    };

    $reflectionProperty = new ReflectionProperty($target, 'testProperty');
    $metadata = new PropertyMetadata($reflectionProperty, []);

    // Act
    $value = $metadata->value($target);

    // Assert
    expect($value)->toBe('testValue');
});

it('correctly sets and retrieves validation rules', function () {
    // Arrange
    #[Attribute]
    class TestValidationRuleValidationMetadataExtractorTest implements ValidationRule
    {
    }

    $target = new class () {
        public string $testProperty;
    };

    $reflectionProperty = new ReflectionProperty($target, 'testProperty');
    $rules = [new TestValidationRuleValidationMetadataExtractorTest()];
    $metadata = new PropertyMetadata($reflectionProperty, $rules);

    // Act
    $retrievedRules = $metadata->rules;

    // Assert
    expect($retrievedRules)->toHaveCount(1);
    expect($retrievedRules[0])->toBeInstanceOf(TestValidationRuleValidationMetadataExtractorTest::class);
});
