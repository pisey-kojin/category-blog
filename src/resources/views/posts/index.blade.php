@extends('layouts.app')

@section('header')
    <span class="text-xl font-bold px-2 py-1">
        記事一覧
    </span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
   
    <div class="mb-4 flex justify-end">        
        @auth
            <a href="{{ route('posts.create') }}" 
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                ＋ 新規記事
            </a>
        @endauth
    </div>

    <!-- カテゴリフィルター -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <h3 class="text-lg font-bold mb-2">カテゴリ</h3>
        
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('posts.index') }}" 
               class="px-3 py-1 rounded-full 
               {{ !request('category') 
               ? 'bg-blue-500 text-white' 
               : 'bg-gray-200 text-gray-700' }}"
               >
                全て
            </a>
            
            @foreach($categories as $category)
                <a href="{{ route('posts.index', ['category' => $category->id]) }}" 
                   class="px-3 py-1 rounded-full 
                   {{ request('category') == $category->id 
                   ? 'bg-blue-500 text-white' 
                   : 'bg-gray-200 text-gray-700' }}"
                   >
                    {{ $category->name }}
                    <span class="text-xs ml-1 {{ request('category') == $category->id ? 'text-white' : 'text-gray-500' }}">
                        ({{ $category->posts_count }})
                    </span>
                </a>
            @endforeach
            
        </div>
        <div class="pt-2 mt-2">
            @if ($filters['keyword'])
                <div class="mb-4">
                    「{{ $filters['keyword'] }}」の検索結果
                    <a href="{{ route('posts.index', ['category' => $filters['category']])}}" class="text-blue-500">
                        クリア
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- 記事グリッド -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($posts as $post)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
                @if($post->image_path)
                    <img src="{{ Storage::url($post->image_path) }}" 
                         alt="{{ $post->title }}"
                         class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400">No Image</span>
                    </div>
                @endif
                
                <div class="p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs text-gray-500">
                            {{ $post->created_at->format('Y/m/d') }}
                        </span>
                        <span class="text-xs bg-gray-200 px-2 py-1 rounded">
                            {{ $post->category->name }}
                        </span>
                    </div>
                    
                    <h2 class="text-xl font-semibold mb-2">
                        <a href="{{ route('posts.show', $post) }}" class="hover:text-blue-600">
                            {{ $post->title }}
                        </a>
                    </h2>
                    
                    <p class="text-gray-600 mb-4">
                        {{ Str::limit($post->body, 100) }}
                    </p>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">
                            By {{ $post->user->name }}
                        </span>
                        
                        <div class="flex items-center gap-2">
                            <form action="{{ route('posts.like', $post) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="flex items-center gap-1 text-red-500 hover:text-red-600">
                                    <svg class="w-5 h-5 {{ $post->isLikedBy(auth()->user()) ? 'fill-current' : '' }}" 
                                         xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                    <span>{{ $post->likes_count }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12">
                <p class="text-gray-500">記事がありません。</p>
            </div>
        @endforelse
        
    </div>

    <!-- ページネーション -->
    <div class="mt-6">
        {{ $posts->withQueryString()->links() }}
    </div>
</div>
@endsection