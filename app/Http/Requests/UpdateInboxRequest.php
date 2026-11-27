<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateInboxRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan memperbarui pesan.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Aturan validasi pembaruan status pesan (tandai read/unread).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'is_read' => ['required', 'boolean'],
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
            'is_read.required' => 'Status baca (is_read) wajib disertakan.',
            'is_read.boolean'  => 'Status baca harus berupa nilai boolean (true/false atau 1/0).',
        ];
    }
}
