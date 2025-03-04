<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CompanyService\CompanyService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $service = new CompanyService;
        $result = $service->findCompaniesByUserId();
        return $this->constractResponce($result);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $service = new CompanyService;
        $result = $service->createCompany($request->all());
        return $this->constractResponce($result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $service = new CompanyService;
        $result = $service->editStatusCompany($request->all(), $id);
        return $this->constractResponce($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = new CompanyService;
        $result = $service->deleteCompany($id);
        return $this->constractResponce($result);
    }
}
