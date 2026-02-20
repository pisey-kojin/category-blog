<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageUploadService
{
    /**
     * 画像アップロードし、保存パスを返す
     * 
     * @param UploadedFile $image
     * @param string $directory
     * 
     * @return string
     */
    public function upload(UploadedFile $image, string $directory = 'posts'): string
    {
        // 1. ファイル名を生成（ユニックにする）
        $filename = $this->generateFileName($image);

        // 2. 画像をリサイズ
        $optimizedImage = $this->resizeImage($image);

        // 3. 保存先のパスを作成
        $path = $directory . '/' . $filename;

        // 4. 画像を保存
        Storage::disk('public')->put($path, (string)$optimizedImage->encode());

        // 5. ごぞんしたパスを返す
        return $path;
    }

    /**
     * 画像を削除する
     * 
     * @param string|null $path 
     * @return bool
     */
    public function delete(?string $path): bool
    {
        // パスが空、またはファイルが存在しななければ何もしない
        if (!$path || !Storage::disk('public')->exists($path)) {
            return false;
        }

        // 画像を削除
        return Storage::disk('public')->delete($path);
    }

    /**
     * ユニークなファイル名を生成
     * 
     * @param UploadedFile $image
     * @return string
     */
    private function generateFileName(UploadedFile $image): string
    {
        return time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
    }

    /**
     * 画像をリサイズ
     * 
     * @param UploadedFile $image
     * @return \Intervention\Image\Image
     */
    private function resizeImage(UploadedFile $image)
    {
        $manager = new ImageManager(new Driver());
        $img = $manager->read($image);

        // 幅1200px、高さは自動調整（アスペクト比維持）
        $img->resize(1200, null, function ($contstraint) {
            $contstraint->aspectRatio(); // アスペクト比を維持
            $contstraint->upsize(); // 元画像より大きくしない
        });

        return $img;
    }
}
