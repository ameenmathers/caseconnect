<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadCallRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'audio' => [
                'required',
                'file',
                'mimes:mp3,wav,m4a,ogg,webm,flac,mp4,mpeg',
                'max:51200',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'audio.required' => 'Please select an audio file to upload.',
            'audio.file' => 'The uploaded item must be a valid file.',
            'audio.mimes' => 'The file must be an audio file (mp3, wav, m4a, ogg, webm, flac).',
            'audio.max' => 'The audio file must not exceed 50MB.',
        ];
    }
}

