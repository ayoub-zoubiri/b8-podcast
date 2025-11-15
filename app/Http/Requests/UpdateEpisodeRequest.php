<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEpisodeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'audio' => 'sometimes|file|mimes:mp3,wav,ogg,m4a|max:51200', // Max 50MB
        ];
    }

    public function messages()
    {
        return [
            'audio.file' => 'Le fichier audio est invalide',
            'audio.mimes' => 'L\'audio doit être au format: mp3, wav, ogg ou m4a',
            'audio.max' => 'L\'audio ne doit pas dépasser 50MB',
        ];
    }
}
