<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePodcastRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|string|max:255',
            'category' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB
        ];
    }

    public function messages()
    {
        return [
            'image.image' => 'Le fichier doit être une image',
            'image.mimes' => 'L\'image doit être au format: jpeg, png, jpg ou gif',
            'image.max' => 'L\'image ne doit pas dépasser 5MB',
        ];
    }
}
