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
            ->where('title', 'LIKE', '%' . $request->search . '%')
            ->get());
    }

    public function total(Request $request)
    {
        return response(Post::where('title', 'LIKE', '%' . $request->search . '%')
            ->count());
    }

    public function show(string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'code' => 1,
                'message' => 'Пост не найден'
            ], 404);
        }

        return response()->json($post);
    }

    public function store(Request $request)
    {
        if (!Gate::allows('create-post')) {
            return response()->json([
                'code' => 1,
                'message' => 'У вас нет прав на добавление поста',
            ], 401);
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
        } catch (Exception $e) {
            return response()->json([
                'code' => 2,
                'message' => 'Ошибка загрузки файла в хранилище S3',
            ]);
        }

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

    public function destroy(string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'code' => 1,
                'message' => 'Пост не найден'
            ], 404);
        }

        if ($post->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return response()->json([
                'code' => 1,
                'message' => 'Вы можете удалять только свои посты'
            ], 403);
        }

        $post->delete();

        return response()->json([
            'code'=> 0,
            'message' => 'Пост успешно удален'
        ]);
    }

    public function update(Request $request, string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'code'=> 1,
                'message' => 'Пост не найден'
            ], 404);
        }

        if ($post->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return response()->json([
                'code' => 1,
                'message' => 'Вы можете редактировать только свои посты'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'required|max:255|unique:posts,title,' . $id,
            'content' => 'required|string',
            'image' => 'nullable|file|image|max:2048'
        ]);

            try {
                $post = Post::findOrFail($id);
                $post->title = $validated['title'];
                $post->content = $validated['content'];

                if ($request->hasFile('image')) {
                    try {
                        if ($post->image) {
                            $baseUrl = Storage::disk('s3')->url($post->image);
                            $oldPath = str_replace($baseUrl, '', $post->image);
                            if (Storage::disk('s3')->exists($oldPath)) {
                                Storage::disk('s3')->delete($oldPath);
                            }
                        }
                        $file = $request->file('image');
                        $fileName = rand(1, 100000) . '_' . $file->getClientOriginalName();
                        $path = Storage::disk('s3')->putFileAs('post_pictures', $file, $fileName);
                        $post->image = Storage::disk('s3')->url($path);
                    }catch (Exception $e) {
                        return response()->json(['message' => 'Error uploading image: ',
                            'error' => ['code' => $e->getCode(), 'message' => $e->getMessage()]], 500);
                    }
                }
                $post->save();

                return response()->json([
                    'code' => 0,
                    'message' => 'Пост успешно обновлен'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'code'=> 2,
                    'message' => 'Ошибка при добавлении'
                ]);
            }
        }



}
