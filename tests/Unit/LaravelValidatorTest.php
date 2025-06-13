<?php

use Sam\AttributeValidation\Attributes\Validate;
use Sam\AttributeValidation\Contracts\ValidationRule;
use Sam\AttributeValidation\Validator\LaravelValidator;
use Illuminate\Validation\Factory;
use Mockery\MockInterface;

it('should return true for supports when rule is instance of Validate', function () {
    // Arrange
    $validatorFactory = mock(Factory::class);
    $laravelValidator = new LaravelValidator($validatorFactory);

    $rule = mock(Validate::class);

    // Act
    $result = $laravelValidator->supports($rule);

    // Assert
    expect($result)->toBeTrue();
});

it('should return false for supports when rule is not instance of Validate', function () {
    // Arrange
    $validatorFactory = mock(Factory::class);
    $laravelValidator = new LaravelValidator($validatorFactory);

    $rule = mock(ValidationRule::class);

    // Act
    $result = $laravelValidator->supports($rule);

    // Assert
    expect($result)->toBeFalse();
});

it('should validate successfully when no validation errors occur', function () {
    // Arrange
    $validatorFactory = mock(Factory::class, function (MockInterface $mock) {
        $mock->shouldReceive('make')
            ->once()
            ->with([
                'property' => 'test value',
            ], [
                'property' => ['required', 'string'],
            ], [
                'property' => ['This field is required.'],
            ])
            ->andReturn(mock(\Illuminate\Contracts\Validation\Validator::class, function (MockInterface $mock) {
                $mock->shouldReceive('fails')
                    ->once()
                    ->andReturn(false);
            }));
    });

    $laravelValidator = new LaravelValidator($validatorFactory);

    $rule = mock(Validate::class, function (MockInterface $mock) {
        $mock->rules = ['required', 'string'];
        $mock->messages = ['This field is required.'];
    });

    // Act
    $errors = $laravelValidator->validate('property', 'test value', $rule);

    // Assert
    expect($errors)->toBeEmpty();
});

it('should return validation errors when validation fails', function () {
    // Arrange
    $propertyName = 'property';
    $value = '';
    $rules = ['required', 'max:10'];
    $messages = [];

    $validatorFactory = mock(Factory::class, function (MockInterface $mock) use ($propertyName, $value, $rules, $messages) {
        $mock->shouldReceive('make')
            ->once()
            ->with(
                [$propertyName => $value],  // Data
                [$propertyName => $rules], // Rules
                [$propertyName => $messages] // Messages
            )
            ->andReturn(mock(\Illuminate\Contracts\Validation\Validator::class, function (MockInterface $mock) {
                $mock->shouldReceive('fails')  // Mock the fails method
                ->once()
                    ->andReturn(true);

                $mock->shouldReceive('errors->all')  // Mock the errors method returning errors
                ->once()
                    ->andReturn(['The property field is required.']);
            }));
    });

    $laravelValidator = new LaravelValidator($validatorFactory);

    $rule = mock(Validate::class, function (MockInterface $mock) use ($rules, $messages) {
        $mock->rules = $rules;
        $mock->messages = $messages;
    });

    // Act
    $errors = $laravelValidator->validate($propertyName, $value, $rule);

    // Assert
    expect($errors)->toHaveCount(1)
        ->and($errors)->toContain('The property field is required.');
});
