<?php

declare(strict_types=1);

use Sam\AttributeValidation\AttributeValidator;
use Sam\AttributeValidation\Attributes\Validate;
use Sam\AttributeValidation\Validator\LaravelValidator;
use Sam\AttributeValidation\MetadataExtractor;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;

beforeEach(function () {
    // Laravel Validator does not require Bootstraping in Pest if Laravel Test Environment is used
    // If you are not in Laravel, you will need to register Translator and Presensverifier
});

test('validates DTO with Laravel validator attribute', function () {

    // DTO с атрибутом Validate
    $dto = new class () {
        #[Validate(['min:3', 'email'], ['min' => 'custom min message error'])]
        public ?string $email = 'd';

        #[Validate(['required', 'min:3'])]
        public ?string $name = 'Al';
    };

    $loader = new ArrayLoader();
    $translator = new Translator($loader, 'en');
    $factory = new Factory($translator);

    // Extractor + laravel validator
    $extractor = new MetadataExtractor();
    $laravelValidator = new LaravelValidator($factory);

    $validator = new AttributeValidator(
        extractor: $extractor,
        validators: [$laravelValidator]
    );

    $errors = $validator->validate($dto);

    expect($errors)->toBeArray()
        ->and($errors)->toHaveKey('email')
        ->and($errors['email'][0])->toContain('custom min message error')
        ->and($errors['email'][1])->toContain('validation.email')
        ->and($errors)->toHaveKey('name')
        ->and($errors['name'][0])->toContain('validation.min.string');
});
