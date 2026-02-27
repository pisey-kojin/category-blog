@extends('layouts.app')

@section('header')
    <span class="text-xl font-bold px-2 py-1">
        {{ $post->title }}
    </span>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- 記事ヘッダー情報 -->
                <div class="mb-6">
                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                        <span>{{ $post->created_at->format('Y年m月d日') }}</span>
                        <span>•</span>
                        <span>カテゴリ: {{ $post->category->name }}</span>
                        <span>•</span>
                        <span>投稿者: {{ $post->user->name }}</span>
                    </div>

                    <!-- いいねボタン -->
                    <div class="flex items-center gap-4">
                        <form action="{{ route('posts.like', $post) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="flex items-center gap-2 px-4 py-2 rounded-full {{ $post->isLikedBy(auth()->user()) ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                                <span>いいね {{ $post->likes_count }}</span>
                            </button>
                        </form>

                        @can('update', $post)
                            <div class="flex gap-2">
                                <a href="{{ route('posts.edit', $post) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">
                                    編集
                                </a>
                                <form action="{{ route('posts.destroy', $post) }}" 
                                      method="POST"
                                      onsubmit="return confirm('この記事を削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        削除
                                    </button>
                                </form>
                            </div>
                        @endcan
                    </div>
                </div>

                <!-- アイキャッチ画像 -->
                @if($post->image_path)
                    <div class="mb-8">
                        <img src="{{ Storage::url($post->image_path) }}" 
                             alt="{{ $post->title }}"
                             class="w-full max-h-96 object-cover rounded-lg">
                    </div>
                @endif

                <!-- 本文 -->
                <div class="prose max-w-none">
                    {!! nl2br(e($post->body)) !!}
                </div>


                <!-- 戻るボタン -->
                <div class="mt-8">
                    <a href="{{ route('posts.index') }}" 
                       class="text-blue-500 hover:underline">
                        ← 記事一覧に戻る
                    </a>
                </div>
            </div>
        </div>

    <!-- コメントセクション -->
    <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                コメント （{{ $post->comments->count() }}）
            </h3>

            {{-- コメント投稿フォーム（ログイン時のみ） --}}
            @auth
                <form action="{{ route('comments.store', $post)}}" 
                    method="POST"
                    class="mb-6 comment-form"
                    data-post-id="{{ $post->id }}">
                    @csrf
                    <textarea 
                        name="body"
                        rows="5" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 comment-textarea"
                        placeholder="コメントを書く..."
                        id="new-comment-textarea">{{ old('body') }}</textarea>
                    <div class="mt-2 flex-justify-end">
                        <button
                            type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded submit-comment-btn"
                            id="submit-comment-btn"
                            disabled
                            >
                            投稿する
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 comment-counter" id="comment-counter">0/1000文字</p>
                </form>
            @else
                <p class="mb-4 text-gray-600">
                    コメントするには <a href="{{ route('login')}}" class="text-blue-500 hover:underline">ログイン</a>してください。
                </p>
            @endauth

            {{-- コメント一覧 --}}
            <div class="space-y-4 comments-container">
                @forelse ($post->comments as $comment)
                    <div 
                        class="border-b border-gray-200 pb-4 last:border-0" 
                        data-comment-id = "{{ $comment->id }}"
                        >
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ substr($comment->user->name, 0, 1)}}
                                </div>
                                <div>
                                    <span class="font-medium text-gray-900">
                                        {{ $comment->user->name }}
                                    </span>
                                    <span class="text-sm text-gray-500 ml-2">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            {{-- 自分おコメントなら操作ボタン --}}
                            @can('delete', $comment)
                                <div class="flex gap-2">
                                    <button 
                                        type="button"
                                        class="text-blue-600 hover:text-blue-900 text-sm edit-comment-btn"
                                        data-comment-id="{{ $comment->id }}"
                                        data-comment-body="{{ $comment->body }}"
                                        >
                                        編集
                                    </button>
                                    <form 
                                        action="{{ route('comments.destroy', $comment)}}"
                                        method="POST" 
                                        class="delete-comment-form inline"
                                        data-comment-id={{ $comment->id }}
                                        >
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit"
                                            class="text-red-600 hover:text-red-900 text-sm"
                                            onclick="return confirm('このコメントを削除しますか？')"
                                            >
                                            削除
                                        </button>
                                    </form>
                                </div>
                            @endcan
                        </div>
                            
                        {{-- コメント本文表示（通常時） --}}
                        <div 
                            class="mt-2 text-gray-700 whitespace-pre-wrap comment-body"
                            id="comment-body-{{ $comment->id }}"
                            >{{ $comment->body }}
                        </div>

                        {{-- 編集フォーム（初期は非表示 --}}
                        <div class="mt-2 eidt-form hidden" id="edit-form-{{ $comment->id }}">
                            <textarea 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 edit-textarea" 
                                id="edit-textarea-{{ $comment->id }}" 
                                rows="5"
                                >{{ $comment->body }}</textarea>
                            <span 
                                class="text-xs text-gray-500 edit-counter"
                                id="edit-counter-{{ $comment->id }}"
                                >
                                {{ strlen($comment->body) }}/1000文字
                            </span>    
                            <div class="mt-2 flex-gap-2 justify-end">
                                <button 
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-3 rounded text-sm cancel-edit-btn"
                                    data-comment-id="{{ $comment->id }}"
                                    >
                                    キャンセル
                                </button>
                                <button type="button" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm update-comment-btn"
                                    data-comment-id="{{ $comment->id }}"
                                    data-original-body="{{ $comment->body }}"
                                    disabled>
                                    更新
                                </button>
                            </div>
                        </div>
                    </div> 
                @empty
                    <p class="text-gray-500 text-center py-4">
                        まだコメントはありません
                    </p>
                @endforelse
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
