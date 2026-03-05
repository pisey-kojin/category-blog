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
                        <x-like-button :post="$post" />

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
                @include('posts._comments')
            </div>
        </div>
    </div>
</div>
@endsection
