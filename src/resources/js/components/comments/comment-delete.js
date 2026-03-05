export function initCommentDelete() {

    // 全ての削除フォームを処理
    document.querySelectorAll('.delete-comment-form').forEach(form => {

        form.addEventListener('submit', async (e) => {
            e.preventDefault();  // 通常のフォーム送信をキャンセル

            // 確認ダイアログ
            if (!confirm('このコメントを削除しますか？')) {
                return;
            }

            const commentId = form.dataset.commentId;
            const submitBtn = form.querySelector('.delete-comment-btn');
            const originalText = submitBtn.textContent;

            // ボタンを無効化（連打防止）
            submitBtn.disabled = true;
            submitBtn.textContent = '削除中...';

            try {
                const response = await fetch(`/comments/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // コメント要素を削除（アニメーション付き）
                    const commentElement = document.getElementById(`comment-${commentId}`);

                    // フェードアウトアニメーション
                    commentElement.style.transition = 'opacity 0.3s';
                    commentElement.style.opacity = '0';

                    setTimeout(() => {
                        commentElement.remove();

                        // コメント数を更新
                        updateCommentCount();

                        // 成功メッセージ表示
                        if (window.showFlashMessage) {
                            window.showFlashMessage('コメントを削除しました', 'success');
                        }
                    }, 300);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('削除に失敗しました');

                // ボタンを元に戻す
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    });

    // コメント数を更新する関数
    function updateCommentCount() {
        const commentCount = document.querySelectorAll('.comment-item').length;
        const commentHeader = document.querySelector('h3:contains("コメント")');

        if (commentHeader) {
            commentHeader.textContent = `コメント (${commentCount})`;
        }
    }
}
