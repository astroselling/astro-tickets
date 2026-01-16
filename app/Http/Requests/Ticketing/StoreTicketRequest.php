<?php

declare(strict_types=1);

namespace App\Http\Requests\Ticketing;

use Illuminate\Foundation\Http\FormRequest;

final class StoreTicketRequest extends FormRequest
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
            'board_id' => ['required', 'integer', 'exists:ticket_boards,id'],
            'type_id' => ['required', 'integer', 'exists:ticket_types,id'],
            'subtype_id' => ['nullable', 'integer', 'exists:ticket_subtypes,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['nullable', 'integer', 'min:0', 'max:10'],
        ];
    }
}
