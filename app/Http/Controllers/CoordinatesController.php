<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class CoordinatesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user_driver = $request->user();
        $user_driver->update([
            'lon' => $request->lon,
            'lat' => $request->lat,
        ]);
        return $this->generalResponse(null);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $coordinate)
    {
        return $this->generalResponse([
            'lon' => $coordinate->user->lon,
            'lat' => $coordinate->user->lat,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
