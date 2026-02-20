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
    </div>
</div>
@endsection