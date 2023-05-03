<?php

use Illuminate\Support\Str;

if (!function_exists('convert2Camel')) {
    function convert2Camel($data)
    {
        if (is_array($data)) {
            return array_combine(
                array_map(function ($key) {
                    return Str::camel($key);
                }, array_keys($data)),
                array_map(function ($value) {
                    return convert2Camel($value);
                }, $data)
            );
        }

        if ($data instanceof Illuminate\Support\Collection) {
            return $data->mapWithKeys(function ($value, $key) {
                return [Str::camel($key) => convert2Camel($value)];
            });
        }

        if (is_object($data)) {
            return collect($data)->mapWithKeys(function ($value, $key) {
                return [Str::camel($key) => convert2Camel($value)];
            });
        }

        return $data;
    }
}


