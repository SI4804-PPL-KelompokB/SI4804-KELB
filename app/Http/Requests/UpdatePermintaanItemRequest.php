<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePermintaanItemRequest extends FormRequest
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
            'qty' => ['required', 'integer', 'min:1', 'max:1000000'],
            'nama_obat' => ['sometimes', 'required', 'string', 'max:150'],
            'satuan' => ['sometimes', 'nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'qty.required' => 'Jumlah (qty) obat wajib diisi.',
            'qty.integer' => 'Jumlah (qty) obat harus berupa angka bulat.',
            'qty.min' => 'Jumlah (qty) obat minimal 1.',
            'qty.max' => 'Jumlah (qty) obat maksimal 1.000.000.',
            'nama_obat.required' => 'Nama obat wajib diisi.',
            'nama_obat.max' => 'Nama obat maksimal 150 karakter.',
            'satuan.max' => 'Satuan obat maksimal 50 karakter.',
        ];
    }
}
