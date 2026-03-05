export function initCommentUpdate() {
    document.querySelectorAll('.update-comment-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const commentId = this.dataset.commentId;
            const originalBody = this.dataset.commentBody;
            const textarea = document.getElementById(`edit-textarea-${commentId}`);
            const newBody = textarea.value.trim();

            if (!newBody) {
                alert('コメントを入力してください');
                return;
            }

            if (newBody === originalBody) {
                return; // 変更がない場合は何もしない
            }

            // ボタンの状態を保存
            // const originalText = this.textContent;

            // ボタンを無効化(連打防止)
            this.disabled = true;
            this.textContent = '更新中...';

            try {
                const response = await fetch(`/comments/${commentId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ body: newBody })
                });

                const data = await response.json();

                if (data.success) {
                    // 表示部分の本文を更新
                    const commentBody = document.getElementById(`comment-body-${commentId}`);
                    commentBody.textContent = newBody;

                    // 更新ボタンの元の本文を更新
                    this.dataset.originalBody = newBody;

                    // 編集フォームを閉じる
                    document.getElementById(`edit-form-${commentId}`).classList.add('hidden');
                    commentBody.classList.remove('hidden');

                    const editBtn = document.querySelector(`.edit-comment-btn[data-comment-id="${commentId}"]`);
                    if (editBtn) {
                        editBtn.disabled = false;
                        editBtn.classList.remove('cursor-not-allowed', 'text-gray-500');
                        editBtn.classList.add('text-blue-600', 'hover:text-blue-900');
                    }

                    // 成功メッセージを表示
                    if (window.showFlashMessage) {
                        window.showFlashMessage('コメントを更新しました', 'success');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('更新に失敗しました');
            }
            // ボタンを元に戻す
            this.disabled = false;
            this.textContent = originalBody;
        });
    });
}
