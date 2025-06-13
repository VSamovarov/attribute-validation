<?php

use Sam\AttributeValidation\PropertyMetadata;
use Sam\AttributeValidation\MetadataExtractor;
use Sam\AttributeValidation\Contracts\ValidationRule;

// Тестовый атрибут, реализующий ValidationRule
#[Attribute(Attribute::TARGET_PROPERTY)]
class TestValidationRule implements ValidationRule
{
    public function __construct(public string $rule)
    {
    }
}

class ValidationMetadataExtractorCacheTest
{
    #[TestValidationRule('rule1')]
    public string $name;

    #[TestValidationRule('rule2')]
    public int $age;

    public function __construct($name, int $age)
    {
        $this->name = $name;
        $this->age = $age;
    }
}

beforeEach(function () {
    MetadataExtractor::clearCache(); // Чистим кеш перед каждым тестом
});

it('resolves validation rules correctly for a class with attributes', function () {
    // Arrange
    $target = new class () {
        #[TestValidationRule('rule1')]
        public string $name;

        #[TestValidationRule('rule2')]
        public int $age;

        public string $noValidationAttribute;
    };

    $resolver = new MetadataExtractor();

    // Act
    $result = $resolver->resolve($target);
    // Assert

    expect($result['name'])->toBeInstanceOf(PropertyMetadata::class);
    expect($result['name']->property)->toBeInstanceOf(ReflectionProperty::class);

    expect($result)
        ->toHaveKey('name')
        ->toHaveKey('age')
        ->not->toHaveKey('noValidationAttribute');

    expect($result['name']->rules[0])->toBeInstanceOf(TestValidationRule::class);
    expect($result['age']->rules[0])->toBeInstanceOf(TestValidationRule::class);
    expect($result['name']->rules[0]->rule)->toBe('rule1');
    expect($result['age']->rules[0]->rule)->toBe('rule2');
});

it('returns an empty array for a class without validation attributes', function () {
    // Arrange
    $target = new class () {
        public string $field1;
        public int $field2;
    };

    $resolver = new MetadataExtractor();

    // Act
    $result = $resolver->resolve($target);

    // Assert
    expect($result)->toBe([]);
});

it('handles a class with no properties correctly', function () {
    // Arrange
    $target = new class () {
    };

    $resolver = new MetadataExtractor();

    // Act
    $result = $resolver->resolve($target);

    // Assert
    expect($result)->toBe([]);
});

it('ignores attributes that do not implement the ValidationRule interface', function () {
    // Arrange
    #[Attribute(Attribute::TARGET_PROPERTY)]
    class NonValidationAttribute
    {
    }

    $target = new class () {
        #[NonValidationAttribute]
        public string $name;

        #[TestValidationRule('validRule')]
        public int $id;
    };

    $resolver = new MetadataExtractor();

    // Act
    $result = $resolver->resolve($target);

    // Assert
    expect($result)
        ->not->toHaveKey('name')
        ->toHaveKey('id');

    expect($result['id']->rules[0])->toBeInstanceOf(TestValidationRule::class);
    expect($result['id']->rules[0]->rule)->toBe('validRule');
});

it('throws an error for invalid target type (non-object)', function () {
    // Arrange
    $resolver = new MetadataExtractor();

    // Act & Assert
    expect(fn () => $resolver->resolve('not_an_object'))
        ->toThrow(TypeError::class);
});

it('resolves validation rules correctly when multiple attributes are used for one property', function () {
    // Arrange
    #[Attribute(Attribute::TARGET_PROPERTY)]
    class AnotherValidationRule implements ValidationRule
    {
        public function __construct(public string $description)
        {
        }
    }

    $target = new class () {
        #[TestValidationRule('rule1'), AnotherValidationRule('description1')]
        public string $name;
    };

    $resolver = new MetadataExtractor();

    // Act
    $result = $resolver->resolve($target);

    // Assert
    expect($result)->toHaveKey('name');
    expect($result['name']->rules[0])->toBeInstanceOf(TestValidationRule::class);
    expect($result['name']->rules[1])->toBeInstanceOf(AnotherValidationRule::class);
    expect($result['name']->rules[0]->rule)->toBe('rule1');
    expect($result['name']->rules[1]->description)->toBe('description1');
});


it('uses cache for repeated calls', function () {
    // Arrange
    $target = new class () {
        #[TestValidationRule('rule1')]
        public string $name;
    };

    $extractor = new MetadataExtractor();

    // Act
    $result1 = $extractor->resolve($target); // The first challenge, cache is filled
    $result2 = $extractor->resolve($target); // Second challenge, data are taken from cache
    // Assert
    expect($result1)->toBe($result2); // We will make sure that the results of both calls are the same
});

it('recreates cache after clearing', function () {
    // Arrange
    $target = new class () {
        #[TestValidationRule('rule1')]
        public string $field;
    };

    $extractor = new MetadataExtractor();

    // Act
    $result1 = $extractor->resolve($target);
    MetadataExtractor::clearCache();
    $result2 = $extractor->resolve($target);
    // Assert
    expect($result1)->not->toBe($result2); // cache was dropped, the second challenge created a new object
});
