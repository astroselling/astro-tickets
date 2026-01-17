<?php

declare(strict_types=1);

namespace App\Http\Requests\Ticketing;

use Illuminate\Foundation\Http\FormRequest;

final class TransitionTicketRequest extends FormRequest
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
            'state_id' => ['required', 'integer', 'exists:workflow_states,id'],
        ];
    }
}
