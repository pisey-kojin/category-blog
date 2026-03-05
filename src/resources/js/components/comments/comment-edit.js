export function initCommentEdit() {

    let currentlyEditingId = null; // 現在編集中のコメントID

    // 編集ボタンクリック
    document.querySelectorAll('.edit-comment-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const originalBody = this.dataset.commentBody;

            // 現在のコメントを編集モードに
            startEdit(commentId, originalBody);
        });
    });

    // 編集開始
    function startEdit(commentId, originalBody) {

        if (currentlyEditingId && currentlyEditingId !== commentId) {
            cancelEdit(currentlyEditingId);
        }

        // 表示を隠す
        document.getElementById(`comment-body-${commentId}`).classList.add('hidden');
        // 編集フォームを表示
        document.getElementById(`edit-form-${commentId}`).classList.remove('hidden');

        const editBtn = document.querySelector(`.edit-comment-btn[data-comment-id="${commentId}"]`);
        editBtn.disabled = true;
        editBtn.classList.add('cursor-not-allowed', 'text-grey-500');
        editBtn.classList.remove('text-blue-600', 'hover:text-blue-900');

        // 編集中のIDを記録
        currentlyEditingId = commentId;

        // テキストエリアの状態監視開始
        const textarea = document.getElementById(`edit-textarea-${commentId}`);
        const updateBtn = document.querySelector(`.update-comment-btn[data-comment-id="${commentId}"]`);
        const counter = document.getElementById(`edit-counter-${commentId}`);
        // const originalBody = updateBtn.dataset.originalBody;


        // 初期状態では変更がないので更新ボタンは無効
        updateBtn.disabled = true;
        updateBtn.classList.add('bg-gray-300', 'cursor-not-allowed');
        updateBtn.classList.remove('bg-blue-500', 'hover:bg-blue-700');

        // テキスト変更監視
        textarea.addEventListener('input', function(){
            // 変更があったかチェック
            const isChanged = textarea.value  !== originalBody;
            const isValid = textarea.value.trim().length > 0 && textarea.value.length<=1000;

            // 更新ボタンの状態更新
            updateBtn.disabled = !(isChanged && isValid);

            if (updateBtn.disabled) {
                updateBtn.classList.add('bg-gray-300', 'cursor-not-allowed');
                updateBtn.classList.remove('bg-blue-500', 'hover:bg-blue-700');
            } else {
                updateBtn.classList.remove('bg-gray-300', 'cursor-not-allowed');
                updateBtn.classList.add('bg-blue-500', 'hover:bg-blue-700');
            }

            counter.textContent = `${this.value.length}/1000文字`;
        });
    }

    // 編集キャンセル
    function cancelEdit(commentId) {
        // 編集フォームを隠す
        document.getElementById(`edit-form-${commentId}`).classList.add('hidden');
        // 表示を戻す
        document.getElementById(`comment-body-${commentId}`).classList.remove('hidden');
        // 編集ボタンを再有効
        const editBtn = document.querySelector(`.edit-comment-btn[data-comment-id="${commentId}"]`);
        editBtn.disabled = false;
        editBtn.classList.remove('cursor-not-allowed', 'text-gray-500');
        editBtn.classList.add('text-blue-600', 'hover:text-blue-900');

        // 編集中IDをクリア
        if (currentlyEditingId === commentId) {
            currentlyEditingId = null;
        }
    }

    // キャンセルボタン
    document.querySelectorAll('.cancel-edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            cancelEdit(commentId);
        });
    });
}
