<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagControllerApi extends Controller
{
    public function index()
    {

        return response(Tag::all());
    }

    public function show(string $id)
    {
        return response(Tag::find($id));
    }
}
