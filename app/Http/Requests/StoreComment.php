<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComment extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => ['bail','required', 'min:5'],
        ];
    }

    public function messages()
    {
        return [
            'content.required' => "You can't add empty comment!",
            'content.min' => "Comment should contain at least :min chars"
        ];
    }
}
