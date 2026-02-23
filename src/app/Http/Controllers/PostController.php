<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\ImageUploadService;
use App\Constants\PaginationConstants;

class PostController extends Controller
{
    use AuthorizesRequests;

    protected $imageService;

    /**
     * 依存性の注入（DI)
     * コンストラクタでサービスを受け取る
     */
    public function __construct(ImageUploadService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * 記事一覧表示
     */
    public function index()
    {
        /**
         * カテゴリでフィルタする
         */
        $query = Post::with(['user', 'category'])
            ->withCount('likes');

        // カテゴリフィルタ
        $currentCategory = null;
        if (request('category')) {
            $query->where('category_id', request('category'));
            $currentCategory = Category::find(request('category'));
        }

        $categories = Category::withCount('posts')->get();

        // withQueryString()で２ページ目に移動する時にも、フィルタを維持できる
        $posts = $query
            ->latest()
            ->paginate(PaginationConstants::POSTS_PER_PAGE)
            ->withQueryString();

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
        $data = $request->validated();

        // 画像アップロード（サービスに任せる）
        if ($request->hasFile('image')) {
            $data['image_path'] =
                $this->imageService
                ->upload($request->file('image'));
        }

        $data['user_id'] = Auth::id();
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
    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $data = $request->validated();

        // 画像削除チェック
        if ($request->has('remove_image') && $request->remove_image) {
            // サービスに削除を依頼
            $this->imageService->delete($post->image_path);
            $data['image_path'] = null;
        }

        // 新規画像アップロード（削除チェックの有無に関わらず優先）
        if ($request->hasFile('image')) {
            // 古い画像を削除
            $this->imageService->delete($post->image_path);
            // 新規画像アップロード
            $data['image_path'] = $this->imageService
                ->upload($request->file('image'));
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

        // 画像も削除（サービスに任せる）
        $this->imageService->delete($post->image_path);

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
