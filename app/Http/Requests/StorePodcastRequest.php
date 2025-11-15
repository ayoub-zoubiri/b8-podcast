<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePodcastRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB
        ];
    }

    public function messages()
    {
        return [
            'image.required' => 'L\'image est obligatoire',
            'image.image' => 'Le fichier doit être une image',
            'image.mimes' => 'L\'image doit être au format: jpeg, png, jpg ou gif',
            'image.max' => 'L\'image ne doit pas dépasser 5MB',
        ];
    }
}
