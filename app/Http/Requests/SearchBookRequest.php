<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true; // or add logic for user permissions
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'title' => 'nullable|string|max:255',
            'genre' => 'nullable|string|max:255',
        ];
    }
}
