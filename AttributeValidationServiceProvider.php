<?php

declare(strict_types=1);

namespace Sam\AttributeValidation;

use Sam\AttributeValidation\Validator\LaravelValidator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;

class AttributeValidationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AttributeValidator::class, function ($app) {
            return new AttributeValidator(
                extractor: new MetadataExtractor(),
                validators: [
                    new LaravelValidator($app->make(Factory::class)),
                ]
            );
        });
    }
}
