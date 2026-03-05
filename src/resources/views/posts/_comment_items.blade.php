{{-- コメント一覧 --}}
<div class="border-b border-gray-200 pb-4 last:border-0"
    data-comment-id = "{{ $comment->id }}"
    id="comment-{{ $comment->id }}">

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
                @include('posts._comment_edit_button', ['comment' => $comment])
                @include('posts._comment_delete_form', ['comment' => $comment])
            </div>
        @endcan
    </div>

    {{-- コメント本文表示（通常時） --}}
    <div
        class="mt-2 text-gray-700 whitespace-pre-wrap comment-body"
        id="comment-body-{{ $comment->id }}"
        >{{ $comment->body }}
    </div>

    {{-- 編集フォーム --}}
    @include('posts._comment_edit_form', ['comment' => $comment])
</div>
