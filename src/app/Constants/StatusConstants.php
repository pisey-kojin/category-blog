<?php

namespace App\Constants;

/**
 * ステータスに関する定数
 */
class StatusConstants
{
    /**
     * 記事のステータス
     */
    public const POST_STATUS_DRAFT = 0;      // 下書き
    public const POST_STATUS_PUBLISHED = 1;  // 公開中
    public const POST_STATUS_ARCHIVED = 2;   // アーカイブ

    /**
     * ステータスの表示名を取得する配列
     */
    public const POST_STATUS_LABELS = [
        self::POST_STATUS_DRAFT => '下書き',
        self::POST_STATUS_PUBLISHED => '公開中',
        self::POST_STATUS_ARCHIVED => 'アーカイブ',
    ];

    /**
     * ステータスの色（CSS用）
     */
    public const POST_STATUS_COLORS = [
        self::POST_STATUS_DRAFT => 'gray',
        self::POST_STATUS_PUBLISHED => 'green',
        self::POST_STATUS_ARCHIVED => 'yellow',
    ];
}
