<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AboutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'badge_title' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'title_ne' => 'nullable|string|max:255',
            'description_en' => 'required|string',
            'description_ne' => 'nullable|string',
            'glass_text_en' => 'nullable|string|max:255',
            'glass_text_ne' => 'nullable|string|max:255',
            'image' => $this->isMethod('PUT') || $this->isMethod ('PATCH') ? 'nullable|image|mimes:jpg,jpeg,png|max:3072' : 'required|image|mimes:jpg,jpeg,png|max:3072',
            

        ];
    }
}
