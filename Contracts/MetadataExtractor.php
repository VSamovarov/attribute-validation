<?php

namespace Sam\AttributeValidation\Contracts;

use Sam\AttributeValidation\PropertyMetadata;

interface MetadataExtractor
{
    /**
     * Returns an array of validation rules for the object.
     *
     * @param object $obj
     * @return array<string,PropertyMetadata> // field => rules
     */
    public function resolve(object $obj): array;
}
