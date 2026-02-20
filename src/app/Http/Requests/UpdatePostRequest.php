<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|max:100',
            'body' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です',
            'title.max' => 'タイトルは100文字以内です',
            'body.required' => '本文は必須です',
            'category_id.required' => 'カテゴリを選択してください',
            'category_id.exists' => '選択されたカテゴリは存在しません',
            'image.image' => '画像ファイルをアップロードしてください',
            'image.mimes' => '画像形式はjpeg,png,jpg,gifのみです',
            'image.max' => '画像サイズは2MB以内です'
        ];
    }
}
