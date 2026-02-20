<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    use AuthorizesRequests;

    /**
     * 記事一覧表示
     */
    public function index()
    {
        /**フィルタなし */
        // $posts = Post::with(['user', 'category'])
        //     ->withCount('likes')
        //     ->latest()
        //     ->paginate(9);

        /**
         * フィルタあり
         */
        $query = Post::with(['user', 'category'])
            ->withCount('likes');

        // カテゴリフィルタ
        $currentCategory = null;
        if (request('category')) {
            $query->where('category_id', request('category'));
            $currentCategory = Category::find(request('category'));
        }

        // withQueryString()で２ページ目に移動する時にも、フィルタを維持できる
        $posts = $query->latest()->paginate(9)->withQueryString();

        $categories = Category::withCount('posts')->get();

        return view('posts.index', compact('posts', 'categories', 'currentCategory'));
    }

    /**
     * 記事作成フォーム
     */
    public function create()
    {
        $categories = Category::all();

        return view('posts.create', compact('categories'));
    }

    /**
     * 記事保存
     */
    public function store(StorePostRequest $request)
    {
        // $post = $request
        //     ->user()
        //     ->categories()
        //     ->create($request->validated());

        $data = $request->validated();

        // dd($request->all()); // <- Debug to see what inside $request.
        // \Log::info('Validated data:', $data);


        //画像アップロード処理
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            // Intervention Image V3
            $manager = new ImageManager(new Driver());
            $img = $manager->read($image);
            $img->resize(1200, 1200, function ($contstraint) {
                $contstraint->aspectRatio();
                $contstraint->upsize();
            });

            $path = 'posts/' . $filename;
            Storage::disk('public')->put($path, (string)$img->encode());

            $data['image_path'] = $path;
        }

        $data['user_id'] = Auth::id();

        // \Log::info('Data to save:', $data);
        Post::create($data);

        return redirect()
            ->route('posts.index')
            ->with('success', '記事を作成しました');
    }

    /**
     * 記事詳細表示
     */
    public function show(Post $post)
    {
        $post->load(['user', 'category', 'likes']);
        $post->loadCount('likes');

        return view('posts.show', compact('post'));
    }

    /**
     * 記事編集フォーム
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        $categories = Category::all();
        return view('posts.edit', compact('post', 'categories'));
    }

    /**
     * 記事更新
     */
    /**
     * 記事更新
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $data = $request->validated();

        // 画像削除チェックボックスがオン、または新規画像アップロードの場合
        $shouldRemoveImage = $request->has('remove_image') && $request->remove_image == '1';

        // 1. 画像削除のみ（新規アップロードなし）
        if ($shouldRemoveImage && !$request->hasFile('image')) {
            if ($post->image_path) {
                Storage::disk('public')->delete($post->image_path);
                $data['image_path'] = null;
            }
        }

        // 2. 新規画像アップロード（削除チェックの有無に関わらず優先）
        if ($request->hasFile('image')) {
            // 古い画像を削除
            if ($post->image_path) {
                Storage::disk('public')->delete($post->image_path);
            }

            // 新規画像アップロード
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            // Intervention Image v3
            $manager = new ImageManager(new Driver());
            $img = $manager->read($image);
            $img->resize(1200, 1200, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $path = 'posts/' . $filename;
            Storage::disk('public')->put($path, (string) $img->encode());

            $data['image_path'] = $path;
        }

        $post->update($data);

        return redirect()
            ->route('posts.show', $post)
            ->with('success', '記事を更新しました');
    }

    /**
     * 記事削除
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        // 画像も削除
        if ($post->image_path) {
            // dd($post->image_path);
            Storage::disk('public')->delete($post->image_path);
        }

        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('success', '記事を削除しました');
    }

    /**
     * いいね機能
     */
    public function like(Post $post)
    {
        /**
         * @var App\Models\User
         */
        $user = Auth::user();

        // いいねしていれば削除、していなければ追加
        $like = $post->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $message = 'いいねを取り消しました';
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            $message = 'いいねしました';
        }

        // Ajaxリクエスト対応
        if (request()->ajax()) {
            return response()->json([
                'likes_count' => $post->likes()->count(),
                'message' => $message,
                'is_liked' => !$like // true＝良いねした、false＝いいね解除
            ]);
        }

        return back()->with('success', $message);
    }
}
