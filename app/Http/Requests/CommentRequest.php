<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'body' => ['required', 'string','min:3', 'max:500'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
        ];
    }
}
