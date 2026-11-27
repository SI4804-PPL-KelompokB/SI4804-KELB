<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePenawaranRequest extends FormRequest
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
            'catatan' => ['present', 'nullable', 'string', 'max:1000'],
            'judul' => ['sometimes', 'nullable', 'string', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'catatan.present' => 'Field catatan wajib disertakan.',
            'catatan.max' => 'Catatan penawaran maksimal 1.000 karakter.',
            'judul.max' => 'Judul penawaran maksimal 150 karakter.',
        ];
    }
}
