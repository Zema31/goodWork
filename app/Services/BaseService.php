<?php

namespace App\Services;
use Illuminate\Support\Facades\Log;

class BaseService
{
    public function errorLog(string $filename, string $error){
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/' . $filename . '-error.log'),
          ])->info($error);
        return ['code' => 422, 'content' => ['message' => 'Произошла ошибка. Попробуйте позднее.']];
    }
}
