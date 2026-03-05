export function initCommentForm() {
    const newCommentTextarea = document.getElementById('new-comment-textarea');
    const submitBtn = document.getElementById('submit-comment-btn');
    const commentCounter = document.getElementById('comment-counter');

    if (!newCommentTextarea) return;

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

    function updateCharacterCount(){
        const length = newCommentTextarea.value.length;
        commentCounter.textContent = `${length}/1000文字`;
    }

    // 初期状態
    updateSubmitButtonState();
    updateCharacterCount();

    // イベントリスナー
    newCommentTextarea.addEventListener('input', function(){
        updateSubmitButtonState();
        updateCharacterCount();
    });
}
