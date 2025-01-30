<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function constractResponce(array $responce): object
    {
        return response(
            $responce['data'],
            $responce['code'],
        )->header('Content-Type', 'applicatin/json;charset=utf-8', JSON_UNESCAPED_UNICODE);
    }
}
