<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show() {
        return $this->generalResponse(request()->user());
    }

    public function update(UpdateProfileRequest $request) {
        return $request->update();
    }
}
