<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    use AuthorizesRequests;

    /**
     * コメント保存
     */
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment = $post->comments()->create([
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);
        $comment->load('user');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'comment' => $comment,
                'message' => 'コメントを投稿しました',
            ]);
        }

        return back()->with('success', 'コメントを投稿しました');
    }

    /**
     * コメントを削除
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'コメントを削除しました'
            ]);
        }

        return back()->with('success', 'コメントを削除しました');
    }

    /**
     * コメント編集フォーム（Ajax用）
     */
    public function edit(Comment $comment)
    {
        $this->authorize('update', $comment);

        if (request()->ajax()) {
            return response()->json([
                'message' => true,
                'comment' => $comment
            ]);
        }

        return back();
    }

    /**
     * コメント更新
     */
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment->update([
            'body' => $request->body
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'comment' => $comment,
                'message' => 'コメントを更新しました'
            ]);
        }

        return back()->with('success', 'コメントを更新しました');
    }
}
