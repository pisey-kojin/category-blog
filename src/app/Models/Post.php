<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'body',
        'image_path'
    ];

    protected $with = ['category', 'user']; // デフォルトでロード

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    // いいね数をカウント
    public function likesCount(): int
    {
        return $this->likes()->count();
    }

    // 特定のユーザーがいいねしているか
    public function isLikedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * カテゴリIDで絞り込む
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|null $categoryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByCategory($query, $categoryId)
    {
        // カテゴリIDが指定されていない場合は何もしない
        if (!$categoryId) {
            return $query;
        }

        return $query->where('category_id', $categoryId);
    }

    /**
     * キーワードで検索する（タイトルと本文）
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $keyword
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $keyword)
    {
        // キーワードが指定されていない場合は何もしない
        if (!$keyword) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
                ->orWhere('body', 'like', "%{$keyword}%");
        });
    }

    /**
     * 公開済みの記事のみ（将来の拡張用）
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
