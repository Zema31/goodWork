<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PromoService\PromoService;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $service = new PromoService;
        $result = $service->findPromosByUserId();
        return $this->constractResponce($result);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $service = new PromoService;
        $result = $service->createPromo($request->all());
        return $this->constractResponce($result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $service = new PromoService;
        $result = $service->editTextPromo($request->all(), $id);
        return $this->constractResponce($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = new PromoService;
        $result = $service->deletePromo($id);
        return $this->constractResponce($result);
    }
}
