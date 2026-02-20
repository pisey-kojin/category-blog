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