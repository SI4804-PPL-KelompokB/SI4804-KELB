<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWilayahRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && $user->hasRole(['admin', 'dinkes']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Ambil ID wilayah dari route parameter agar unique rule mengabaikan record saat ini
        $wilayahId = $this->route('id');

        return [
            'nama_wilayah' => [
                'required',
                'string',
                'max:255',
                Rule::unique('wilayah', 'nama_wilayah')->ignore($wilayahId),
            ],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
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
            'nama_wilayah.required' => 'Nama wilayah wajib diisi.',
            'nama_wilayah.string' => 'Nama wilayah harus berupa teks.',
            'nama_wilayah.max' => 'Nama wilayah maksimal 255 karakter.',
            'nama_wilayah.unique' => 'Nama wilayah tersebut sudah terdaftar dalam sistem.',
            'deskripsi.string' => 'Deskripsi harus berupa teks.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',
        ];
    }
}
