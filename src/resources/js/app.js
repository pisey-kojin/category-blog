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


document.addEventListener('DOMContentLoaded', function() {
    const newCommentTextarea = document.getElementById('new-comment-textarea');
    const submitBtn = document.getElementById('submit-comment-btn');
    const commentCounter = document.getElementById('comment-counter');

    if (newCommentTextarea) {
        // 初期状態
        updateSubmitButtonState();

        // テキスト入力監視
        newCommentTextarea.addEventListener('input', function(){
            updateSubmitButtonState();
            updateCharacterCount(this, commentCounter);
        });
    }

    function updateSubmitButtonState() {
        const text = newCommentTextarea.value.trim();
        submitBtn.disabled = text.length === 0;

        // スタイルも変更（disabled時はグレーに）
        if (submitBtn.disabled) {
            submitBtn.classList.remove('bg-blue-500', 'hover:bg-blue-700');
            submitBtn.classList.add('bg-gray-500', 'cursor-not-allowed');
        } else {
            submitBtn.classList.remove('bg-gray-500', 'cursor-not-allowed');
            submitBtn.classList.add('bg-blue-500', 'hover:bg-blue-700');
        }        
    }

    // ===========================================
    // 2. 文字数カウンター共通関数
    // ===========================================
    function updateCharacterCount(textarea, counterElement){
        const length = textarea.value.length;
        counterElement.textContent = `${length}/1000文字`;

        // 文字数が上限に近づいたら色を変える
        if (length > 900) {
            counterElement.classList.add('text-orange-600');
            counterElement.classList.remove('text-gray-500');
        } else {
            counterElement.classList.remove('text-orange-600');
            counterElement.classList.add('text-gray-500');
        }

        // 上限超えは赤色（バリデーション用）
        if (length > 1000) {
            counterElement.classList.add('text-red-600');
            counterElement.classList.remove('text-orange-600', 'text-gray-500');
        } else {
            counterElement.classList.remove('text-red-600');
        }
    }

    // ===========================================
    // 3. 編集ボタンの状態管理
    // ===========================================
    let currentlyEditingId = null; // 現在編集中のコメントID

    // 編集ボタンクリック
    document.querySelectorAll('.edit-comment-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.dataset.commentId;

            // 現在のコメントを編集モードに
            startEdit(commentId);
        });
    });

    // 編集開始
    function startEdit(commentId) {
        // 表示を隠す
        document.getElementById(`comment-body-${commentId}`).classList.add('hidden');
        // 編集フォームを表示
        document.getElementById(`edit-form-${commentId}`).classList.remove('hidden');
        
        // 編集ボタンを無効化
        document.querySelector(`.edit-comment-btn[data-comment-id="${commentId}"]`).disabled = true;
        document.querySelector(`.edit-comment-btn[data-comment-id="${commentId}"]`).classList.add('cursor-not-allowed', 'text-grey-500');
        document.querySelector(`.edit-comment-btn[data-comment-id="${commentId}"]`).classList.remove('text-blue-600', 'hover:text-blue-900');

        // 編集中のIDを記録
        currentlyEditingId = commentId;

        // テキストエリアの状態監視開始
        const textarea = document.getElementById(`edit-textarea-${commentId}`);
        const updateBtn = document.querySelector(`.update-comment-btn[data-comment-id="${commentId}"]`);
        const counter = document.getElementById(`edit-counter-${commentId}`);
        const originalBody = updateBtn.dataset.originalBody;

        //　編集フォームのテキストエリアに現在の本文をセット
        document.querySelector(`#edit-form-${commentId} .edit-textarea`).value = originalBody;

        // 初期状態では変更がないので更新ボタンは無効
        updateBtn.disabled = true;
        updateBtn.classList.add('bg-gray-300', 'cursor-not-allowed');
        updateBtn.classList.remove('bg-blue-500', 'hover:bg-blue-700');

        // テキスト変更監視
        textarea.addEventListener('input', function(){
            // 文字数カウント更新
            updateCharacterCount(textarea, counter);

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
        });
    }
    
    // 編集キャンセル
    function cancelEdit(commentId) {
        // 編集フォームを隠す
        document.getElementById(`edit-form-${commentId}`).classList.add('hidden');
        // 表示を戻す
        document.getElementById(`comment-body-${commentId}`).classList.remove('hidden');
        // 編集ボタンを再有効
        document.querySelector(`.edit-comment-btn[data-comment-id="${commentId}"]`).disabled = false;
        document.querySelector(`.edit-comment-btn[data-comment-id="${commentId}"]`).classList.remove('cursor-not-allowed', 'text-gray-500');
        document.querySelector(`.edit-comment-btn[data-comment-id="${commentId}"]`).classList.add('text-blue-600', 'hover:text-blue-900');

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
    
    // ===========================================
    // 4. 更新ボタン（Ajax）
    // ===========================================
    document.querySelectorAll('.update-comment-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const commentId = this.dataset.commentId;
            const textarea = document.getElementById(`edit-textarea-${commentId}`);
            const newBody = textarea.value.trim();
            
            if (!newBody) {
                alert('コメントを入力してください');
                return;
            }

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
                    // 本文を更新
                    document.getElementById(`comment-body-${commentId}`).textContent = newBody;

                    // 更新ボタンの元の本文を更新
                    this.dataset.originalBody = newBody;

                    //編集モードを終了
                    cancelEdit(commentId);

                    // 成功メッセージを表示
                    showFlashMessage('コメントを更新しました', 'success');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('更新に失敗しました');
            }
            // ボタンを元に戻す
            this.disabled = false;
            this.textContent = '更新';
        });
    });

    // ===========================================
    // 5. フラッシュメッセージ表示機能（オプション）
    // ===========================================
    function showFlashMessage(message, type = 'success') {
        const flashDiv = document.createElement('div');
        flashDiv.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white z-50 transition-opacity duration-500`;
        flashDiv.textContent = message;

        document.body.appendChild(flashDiv);

        setTimeout(() => {
            flashDiv.style.opacity = '0';
            setTimeout(() => flashDiv.remove(), 500);
        }, 3000);
    }
});
