<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|min:3|max:255',
            'body' => 'required|string|min:10|max:5000',
            'tags' => 'nullable|array',
            'tags.*' => 'nullable|string|distinct|min:3|max:255'
        ];
    }
}
