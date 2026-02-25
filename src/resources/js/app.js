import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// いいね昨日の非同期処理
document.addEventListener('DOMContentLoaded', function(){
    const likeForms = document.querySelectorAll('.like-form');

    likeForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const url = form.action;
            const token = form.querySelector('input[name="_token"]').value;
            const button = form.querySelector('.like-button');
            const countSpan = form.querySelector('.like-count');

            fetch(url, {
                method: 'POST',
                header: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                // いいね数を更新
                if (countSpan) {
                    countSpan.textContent = data.likes_count;
                }

                 // ボタンの見た目を更新
                if (data.is_liked) {
                    button.classList.remove('text-gray-500', 'hover:text-red-500', 'bg-gray-200', 'text-gray-700');
                    button.classList.add('text-red-500', 'bg-red-500', 'text-white');
                    button.querySelector('svg')?.classList.add('fill-current');
                } else {
                    button.classList.remove('text-red-500', 'bg-red-500', 'text-white');
                    button.classList.add('text-gray-500', 'hover:text-red-500', 'bg-gray-200', 'text-gray-700');
                    button.querySelector('svg')?.classList.remove('fill-current');
                }
            });
        });
    });
});

// コメント編集機能
document.addEventListener('DOMContentLoaded', function() {
    
    // 編集ボタンクリック
    document.querySelectorAll('.edit-comment-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const commentBody = this.dataset.commentBody;
            
            // 表示を隠す
            document.getElementById(`comment-body-${commentId}`).classList.add('hidden');
            // 編集フォームを表示
            document.getElementById(`edit-form-${commentId}`).classList.remove('hidden');
            //　編集フォームのテキストエリアに現在の本文をセット
            document.querySelector(`#edit-form-${commentId} .edit-textarea`).value = commentBody;
        });
    });
    
    // キャンセルボタン
    document.querySelectorAll('.cancel-edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            
            // 編集フォームを隠す
            document.getElementById(`edit-form-${commentId}`).classList.add('hidden');
            // 表示を戻す
            document.getElementById(`comment-body-${commentId}`).classList.remove('hidden');
        });
    });
    
    // 更新ボタン（Ajax）
    document.querySelectorAll('.update-comment-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const commentId = this.dataset.commentId;
            const textarea = document.querySelector(`#edit-form-${commentId} .edit-textarea`);
            const newBody = textarea.value.trim();
            
            if (!newBody) {
                alert('コメントを入力してください');
                return;
            }
            
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
                    // 本文を更新
                    document.getElementById(`comment-body-${commentId}`).textContent = newBody;
                    
                    // フォームを閉じる
                    document.getElementById(`edit-form-${commentId}`).classList.add('hidden');
                    document.getElementById(`comment-body-${commentId}`).classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('更新に失敗しました');
            }
        });
    });
});
