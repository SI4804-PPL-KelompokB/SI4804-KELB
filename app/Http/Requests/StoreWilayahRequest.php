<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWilayahRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_wilayah' => ['required', 'string', 'max:255', 'unique:wilayah,nama_wilayah'],
            'deskripsi'    => ['nullable', 'string', 'max:1000'],
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
            'nama_wilayah.string'   => 'Nama wilayah harus berupa teks.',
            'nama_wilayah.max'      => 'Nama wilayah maksimal 255 karakter.',
            'nama_wilayah.unique'   => 'Nama wilayah tersebut sudah terdaftar dalam sistem.',
            'deskripsi.string'      => 'Deskripsi harus berupa teks.',
            'deskripsi.max'         => 'Deskripsi maksimal 1000 karakter.',
        ];
    }
}
