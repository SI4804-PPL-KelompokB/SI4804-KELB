<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePermintaanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Hanya pengguna dengan role 'faskes', status akun aktif, dan memiliki faskes_id
     * yang valid yang dapat mengubah tingkat urgensi permintaan.
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
     * PUT /api/permintaan/{id} hanya mengizinkan pembaruan urgensi_id.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'urgensi_id' => ['required', 'integer', 'exists:urgensi,id'],
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
        ];
    }
}
