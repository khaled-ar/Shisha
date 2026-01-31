<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
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
    public function show(User $coordinate)
    {
        return $this->generalResponse([
            'lon' => $coordinate->lon,
            'lat' => $coordinate->lat,
            'image_url' => $coordinate->image_url,
            'name' => $coordinate->name,
            'phone' => $coordinate->phone,
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
