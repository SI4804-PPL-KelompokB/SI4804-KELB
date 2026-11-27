<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePermintaanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Hanya pengguna dengan role 'faskes', status akun aktif, dan memiliki faskes_id
     * yang valid yang dapat membuat permintaan darurat baru.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user
            && $user->hasRole('faskes')
            && (bool) $user->is_active
            && $user->faskes_id !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'urgensi_id' => ['required', 'integer', 'exists:urgensi,id'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'urgensi_id.required' => 'Tingkat urgensi wajib dipilih.',
            'urgensi_id.integer' => 'Format tingkat urgensi tidak valid.',
            'urgensi_id.exists' => 'Tingkat urgensi yang dipilih tidak ditemukan dalam sistem.',
            'catatan.string' => 'Catatan harus berupa teks.',
            'catatan.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
