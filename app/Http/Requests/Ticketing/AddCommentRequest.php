<?php

declare(strict_types=1);

namespace App\Http\Requests\Ticketing;

use Illuminate\Foundation\Http\FormRequest;

final class AddCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string'],
            'is_internal' => ['nullable', 'boolean'],
        ];
    }
}
