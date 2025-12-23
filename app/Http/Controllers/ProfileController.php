<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show() {
        $user = request()->user();
        if($user->role == 'employee-driver') {
            unset($user->phone_verified_at, $user->lon, $user->lat, $user->role);
        }
        return $this->generalResponse($user);
    }

    public function update(UpdateProfileRequest $request) {
        return $request->update();
    }
}
