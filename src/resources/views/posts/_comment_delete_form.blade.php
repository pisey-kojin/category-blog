<form
    action="{{ route('comments.destroy', $comment)}}"
    method="POST"
    class="delete-comment-form inline"
    data-comment-id={{ $comment->id }}
    >
    @csrf
    @method('DELETE')
    <button type="submit"
        class="text-red-600 hover:text-red-900 text-sm delete-comment-btn">
        削除
    </button>
</form>
