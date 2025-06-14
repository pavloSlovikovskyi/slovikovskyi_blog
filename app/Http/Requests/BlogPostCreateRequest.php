<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogPostCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Дозволяємо всім користувачам робити цей запит (для простоти)
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'       => 'required|min:5|max:200|unique:blog_posts', // 'unique:blog_posts' - заголовок має бути унікальним
            'slug'        => 'max:200|unique:blog_posts', // 'unique:blog_posts' - slug має бути унікальним
            'content_raw' => 'required|string|min:5|max:10000',
            'category_id' => 'required|integer|exists:blog_categories,id', // Категорія має бути обов'язковою, цілим числом і існувати в таблиці blog_categories
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required'    => 'Введіть заголовок статті',
            'title.min'         => 'Заголовок статті повинен містити мінімум :min символів',
            'title.max'         => 'Заголовок статті повинен містити максимум :max символів',
            'title.unique'      => 'Стаття з таким заголовком вже існує',
            'slug.max'          => 'Псевдонім повинен містити максимум :max символів',
            'slug.unique'       => 'Такий псевдонім вже використовується',
            'content_raw.required' => 'Введіть текст статті',
            'content_raw.min'   => 'Мінімальна довжина статті :min символів',
            'content_raw.max'   => 'Максимальна довжина статті :max символів',
            'category_id.required' => 'Виберіть категорію',
            'category_id.integer' => 'Некоректний формат категорії',
            'category_id.exists' => 'Вибрана категорія не існує',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title'       => 'заголовок статті',
            'slug'        => 'псевдонім',
            'content_raw' => 'текст статті',
            'category_id' => 'категорія',
        ];
    }
}
