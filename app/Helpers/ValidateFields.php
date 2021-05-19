<?php

namespace App\Helpers;

use App\Exceptions\CustomErrors\DefaultApplicationException;

class ValidateFields
{
    public static function required($fieldsRequired = [], $data=[])
    {
        foreach ($fieldsRequired as $field) {
            if (!isset($data[$field]) || !$data[$field]) {
                throw new DefaultApplicationException("$field field is required.", 422);
            }
        }
    }
}
