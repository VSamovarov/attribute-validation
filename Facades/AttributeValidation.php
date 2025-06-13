<?php

declare(strict_types=1);

namespace Sam\AttributeValidation\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array validate(object $dto)
 *
 * @see \Sam\AttributeValidation\AttributeValidator
 */
class AttributeValidation extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Sam\AttributeValidation\AttributeValidator::class;
    }
}
