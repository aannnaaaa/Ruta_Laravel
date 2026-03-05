<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostControllerApi extends Controller
{
    public function index()
    {

        return response(Post::all());
    }

    public function show(string $id)
    {
        return response(Post::find($id));
    }
}

