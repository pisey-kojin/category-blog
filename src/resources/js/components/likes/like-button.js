/**
 * いいねボタンの機能
 * - 非同期でいいね/いいね解除
 * - ボタンの見た目を動的に変更
 * - いいね数の表示を更新
 */
export function initLikeButtons() {
    document.querySelectorAll('.like-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const button = form.querySelector('.like-button');
            const countSpan = form.querySelector('.like-count');

            // 連打防止
            if (button.disabled) return;
            button.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                });

                const data = await response.json();

                // いいね数を更新
                if (countSpan) {
                    countSpan.textContent = data.likes_count;
                }

                // ボタンの見た目を更新
                if (data.is_liked) {
                    button.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                    button.classList.add('bg-red-500', 'text-white');
                    button.querySelector('svg')?.classList.add('fill-current');
                } else {
                    button.classList.remove('bg-red-500', 'text-white');
                    button.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                    button.querySelector('svg')?.classList.remove('fill-current');
                }

                // 成功メッセージ
                if (window.showFlashMessage) {
                    window.showFlashMessage(data.message, 'success');
                }
            } catch (error) {
                console.error('like error:', error);
                if (window.showFlashMessage) {
                    window.showFlashMessage('いいね処理に失敗しました', 'error');
                }
            } finally {
                // ボタンを再度有効化
                setTimeout(() => {
                    button.disabled = false;
                }, 500);
            }
        });
    });
}
