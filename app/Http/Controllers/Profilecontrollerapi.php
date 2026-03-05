<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileControllerApi extends Controller
{
    public function index()
    {

        return response(Profile::all());
    }

    public function show(string $id)
    {
        return response(Profile::find($id));
    }
}
