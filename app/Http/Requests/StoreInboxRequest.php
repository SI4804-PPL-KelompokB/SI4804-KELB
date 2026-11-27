<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreInboxRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan membuat permintaan ini.
     * Semua aktor terautentikasi berhak mengirim pesan (F22/PBI-21).
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Aturan validasi pengiriman pesan baru.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'penerima_id' => [
                'required',
                'integer',
                'exists:users,id',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ((int) $value === (int) Auth::id()) {
                        $fail('Anda tidak dapat mengirim pesan kepada akun Anda sendiri.');
                    }
                },
            ],
            'subjek' => ['required', 'string', 'max:200'],
            'isi' => ['required', 'string', 'max:5000'],
        ];
    }

    /**
     * Pesan kustom kesalahan validasi.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'penerima_id.required' => 'Penerima pesan wajib dipilih.',
            'penerima_id.integer' => 'ID penerima tidak valid.',
            'penerima_id.exists' => 'Penerima yang dipilih tidak ditemukan dalam sistem.',
            'subjek.required' => 'Subjek pesan wajib diisi.',
            'subjek.string' => 'Subjek pesan harus berupa teks.',
            'subjek.max' => 'Subjek pesan maksimal 200 karakter.',
            'isi.required' => 'Isi pesan wajib diisi.',
            'isi.string' => 'Isi pesan harus berupa teks.',
            'isi.max' => 'Isi pesan maksimal 5000 karakter.',
        ];
    }
}
