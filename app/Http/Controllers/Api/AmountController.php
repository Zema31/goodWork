<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PromoService\PromoService;
use Illuminate\Http\Request;

class AmountController extends Controller
{
    public function update(Request $request, string $id)
    {
        $service = new PromoService;
        $result = $service->editAmountPromo($request->all(), $id);
        return $this->constractResponce($result);
    }
}
