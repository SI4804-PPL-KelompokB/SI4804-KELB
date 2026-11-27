<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StorePenawaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();

        return $user !== null
            && $user->is_active
            && $user->hasRole('faskes')
            && $user->faskes_id !== null;
    }

    public function rules(): array
    {
        return [
            'judul' => ['nullable', 'string', 'max:150'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'judul.max' => 'Judul penawaran maksimal 150 karakter.',
            'catatan.max' => 'Catatan penawaran maksimal 1.000 karakter.',
        ];
    }
}
