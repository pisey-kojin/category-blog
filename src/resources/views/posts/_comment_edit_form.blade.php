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
