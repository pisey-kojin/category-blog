{{-- コメント投稿フォーム（ログイン時のみ） --}}
@auth
    <form action="{{ route('comments.store', $post)}}" 
        method="POST"
        class="mb-6 comment-form">
        @csrf
        <textarea 
            name="body"
            rows="5" 
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 comment-textarea"
            placeholder="コメントを書く..."
            id="new-comment-textarea">{{ old('body') }}</textarea>
        <div class="mt-2 flex justify-between items-center">
            <span class="right text-xs text-gray-500 mt-1 comment-counter" id="comment-counter">0/1000文字</span>
            <div>
                <button
                type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded submit-comment-btn"
                id="submit-comment-btn"
                disabled>投稿する</button>
            </div>
        </div>
    </form>
@else
    <p class="mb-4 text-gray-600">
        コメントするには <a href="{{ route('login')}}" class="text-blue-500 hover:underline">ログイン</a>してください。
    </p>
@endauth

{{-- コメント一覧 --}}
<div class="space-y-4 comments-container">
    @forelse ($post->comments as $comment)
        @include('posts._comment_items', ['comment' => $comment])
    @empty
        <p class="text-gray-500 text-center py-4">まだコメントはありません</p>
    @endforelse
</div>
