import './bootstrap';

import { initLikeButtons } from './components/likes/like-button';

import { initCommentForm } from './components/comments/comment-form';
import { initCommentEdit } from './components/comments/comment-edit';
import { initCommentDelete } from './components/comments/comment-delete';
import { initCommentUpdate } from './components/comments/comment-update';

import { showFlashMessage } from './utils/helpers';

document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.like-form')) {
        initLikeButtons();
    }
    if (document.querySelector('.comment-form')) {
        initCommentForm();
        initCommentEdit();
        initCommentDelete();
        initCommentUpdate();
    }

    window.showFlashMessage = showFlashMessage;
});
