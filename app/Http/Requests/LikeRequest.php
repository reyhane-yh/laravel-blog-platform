<?php

namespace App\Http\Requests;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class LikeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'type' => 'required|string|in:post,comment',
        ];
    }

    public function validationData(): array
    {
        return array_merge($this->all(), [
            'id' => $this->route('id'),
            'type' => $this->route('type'),
        ]);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check if the type is 'post'
            if ($this->type === 'post' && !Post::find($this->id)) {
                $validator->errors()->add('id', 'The selected post id is invalid.');
            }

            // Check if the type is 'comment'
            if ($this->type === 'comment' && !Comment::find($this->id)) {
                $validator->errors()->add('id', 'The selected comment id is invalid.');
            }
        });
    }
}
