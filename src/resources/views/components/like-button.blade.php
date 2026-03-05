@props(['post'])

@auth
    <form action="{{ route('posts.like', $post) }}" 
          method="POST" 
          class="like-form inline"
          data-post-id="{{ $post->id }}">
        @csrf
        <button type="submit" 
                class="flex items-center gap-2 px-4 py-2 rounded-full transition like-button disabled:opacity-50 disabled:cursor-not-allowed
                       {{ $post->isLikedBy(auth()->user()) ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                {{-- 連打防止用 --}}
                data-liked="{{ $post->isLikedBy(auth()->user()) ? 'true' : 'false' }}">
            <svg class="w-5 h-5 {{ $post->isLikedBy(auth()->user()) ? 'fill-current' : '' }}" 
                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
            <span class="like-count">{{ $post->likes_count }}</span>
        </button>
    </form>
@else
    <a href="{{ route('login') }}" 
       class="flex items-center gap-2 px-4 py-2 rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
        </svg>
        <span class="like-count">{{ $post->likes_count }}</span>
    </a>
@endauth