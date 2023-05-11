<?php

use Illuminate\Support\Str;

//下划线转驼峰命名
if (!function_exists('convert2Camel')) {
    function convert2Camel($data)
    {
        if (is_array($data)) {
            return array_combine(
                array_map(function ($key) {
                    if (preg_match('/^[A-Z]/', $key)) { //判断第一个字符是否为大写
                        return ucfirst(Str::camel($key)); //保留第一个字符的大小写
                    } else {
                        return lcfirst(Str::camel($key));
                    }
                }, array_keys($data)),
                array_map(function ($value) {
                    return convert2Camel($value);
                }, $data)
            );
        }

        if ($data instanceof Illuminate\Support\Collection) {
            return $data->mapWithKeys(function ($value, $key) {
                if (preg_match('/^[A-Z]/', $key)) { //判断第一个字符是否为大写
                    return [ucfirst(Str::camel($key)) => convert2Camel($value)]; //保留第一个字符的大小写
                } else {
                    return [lcfirst(Str::camel($key)) => convert2Camel($value)];
                }
            });
        }

        if (is_object($data)) {
            return collect($data)->mapWithKeys(function ($value, $key) {
                if (preg_match('/^[A-Z]/', $key)) { //判断第一个字符是否为大写
                    return [ucfirst(Str::camel($key)) => convert2Camel($value)]; //保留第一个字符的大小写
                } else {
                    return [lcfirst(Str::camel($key)) => convert2Camel($value)];
                }
            });
        }

        return $data;
    }

}


