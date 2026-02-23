@extends('layouts.app')

@section('header')
    <span class="text-xl font-bold px-2 py-1">
        新規記事作成
    </span>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form method="POST" 
                        action="{{ route('posts.store') }}" 
                        enctype="multipart/form-data" 
                        class="space-y-6">
                    
                    {{-- 共通フォームを読み込み --}}
                    @include('posts._form', [
                        'submitButtonText' => '投稿する',
                        'cancelUrl' => route('posts.index')
                    ])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection