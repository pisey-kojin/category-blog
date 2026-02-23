<?php

namespace App\Constants;

/**
 * 画像アップロードに関する定数
 */
class ImageConstants
{
    /**
     * 画像の最大幅（ピクセル）
     */
    public const MAX_WIDTH = 1200;

    /**
     * 画像の最大高さ（ピクセル）
     * nullの場合はアスペクト比自動
     */
    public const MAX_HEIGHT = null;

    /**
     * 画像の品質（1-100）
     */
    public const QUALITY = 90;

    /**
     * 保存先ディレクトリ（投稿記事用）
     */
    public const DIRECTORY_POSTS = 'posts';

    /**
     * 保存先ディレクトリ（プロフィール画像用）
     */
    public const DIRECTORY_AVATARS = 'avatars';

    /**
     * 最大ファイルサイズ（KB）
     * 2048KB = 2MB
     */
    public const MAX_SIZE_KB = 2048;

    /**
     * 許可するMIMEタイプ
     */
    public const ALLOWED_MIMES = ['jpeg', 'png', 'jpg', 'gif'];

    /**
     * 許可するMIMEタイプ（バリデーション用文字列）
     */
    public const ALLOWED_MIMES_STRING = 'jpeg,png,jpg,gif';
}
