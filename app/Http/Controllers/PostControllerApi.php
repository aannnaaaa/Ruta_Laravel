<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Exception;

class PostControllerApi extends Controller
{
    public function index(Request $request)
    {
        return response(Post::limit($request->perpage ?? 5)
            ->offset(($request->perpage ?? 5) * ($request->page ?? 0))
            ->get());
    }

    public function total()
    {
        return response(Post::all()->count());
    }

    public function store(Request $request)
    {
        if (! Gate::allows('create-post')) {
            return response()->json([
                'code' => 1,
                'message' => 'У вас нет прав на добавление поста',
            ]);
        }

        $validated = $request->validate([
            'title' => 'required|unique:posts|max:255',
            'content' => 'required|string',
            'image' => 'required|file'
        ]);

        $file = $request->file('image');
        $fileName = rand(1, 100000) . '_' . $file->getClientOriginalName();

        try {
            $path = Storage::disk('s3')->putFileAs('post_pictures', $file, $fileName);
            $fileUrl = Storage::disk('s3')->url($path);
        }
        catch (Exception $e){
            return response()->json([
                'code' => 2,
                'message' => 'Ошибка загрузки файла в хранилище S3',
            ]);
        };
        $post = new Post();
        $post->title = $validated['title'];
        $post->content = $validated['content'];
        $post->user_id = auth()->id();
        $post->image = $fileUrl;
        $post->save();

        return response()->json([
            'code' => 0,
            'message' => 'Пост успешно добавлен',
        ]);
    }
}
