{{-- フォームの共通部分 --}}
@csrf
@if(isset($method) && $method === 'PUT')
    @method('PUT')
@endisset

<!-- タイトル -->
<div>
    <label for="title" class="block text-sm font-medium text-gray-700">
        タイトル <span class="text-red-500">*</span>
    </label>
    <input type="text" 
            name="title" 
            id="title" 
            value="{{ old('title', $post->title ?? '') }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    <x-error field="title" />
    {{-- <x-input-with-error name="title" label="タイトル" :value="$post->title ?? ''" required /> --}}
</div>

<!-- カテゴリ -->
<div>
    <label for="category_id" class="block text-sm font-medium text-gray-700">
        カテゴリ <span class="text-red-500">*</span>
    </label>
    <select name="category_id" 
            id="category_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">選択してください</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" 
                {{ old('category_id', $post->category_id ?? '') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    <x-error field="category_id" />
</div>

<!-- 画像アップロード -->
<div>
    <label for="image" class="block text-sm font-medium text-gray-700">
        アイキャッチ画像
    </label>
    
    <!-- 現在の画像表示（あれば） -->
    @isset($post->image_path)
        <div class="mt-2 p-3 bg-gray-50 rounded-md border border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linecap="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm text-gray-600">
                        現在の画像: {{ basename($post->image_path) }}
                    </span>
                </div>
                
                <!-- 画像削除用チェックボックス -->
                <label class="flex items-center space-x-1 text-sm text-red-600 hover:text-red-800 cursor-pointer">
                    <input type="checkbox" 
                            name="remove_image" 
                            value="1"
                            class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span>削除</span>
                </label>
            </div>
        </div>
    @endisset

    <!-- 新規画像アップロード -->
    <div class="mt-3">
        <input type="file" 
                name="image" 
                id="image"
                accept="image/jpeg,image/png,image/jpg,image/gif"
                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        <p class="mt-1 text-xs text-gray-500">画像形式: JPEG, PNG, JPG, GIF / 最大2MB</p>
        @isset($post->image_path)
            （新しい画像を選択すると自動的に置き換わります）
        @endisset
    </div>
    <x-error field="image" />
</div>

<!-- 本文 -->
<div>
    <label for="body" class="block text-sm font-medium text-gray-700">
        本文 <span class="text-red-500">*</span>
    </label>
    <textarea name="body" 
                id="body" 
                rows="15"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('body', $post->body ?? '') }}
    </textarea>
    <x-error field="body" />
</div>

<!-- ボタン -->
<div class="flex items-center gap-4">
    <button type="submit" 
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        {{ $submitButtonText ?? '投稿する' }}
    </button>
    <a href="{{ $cancelUrl ?? route('route.index') }}" 
        class="text-gray-600 hover:text-gray-900">
        キャンセル
    </a>
</div>
