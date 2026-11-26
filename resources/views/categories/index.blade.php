@extends('layouts.crud')
@section('title', 'Kategori Obat')
@section('content')
<p><a class="btn" href="{{ route('categories.create') }}">+ Tambah kategori</a></p>
<table>
    <tr><th>Nama</th><th>Aksi</th></tr>
    @forelse($categories as $c)
    <tr>
        <td>{{ $c->name }}</td>
        <td>
            <a class="btn btn-gray" href="{{ route('categories.edit', $c) }}">Ubah</a>
            <form method="POST" action="{{ route('categories.destroy', $c) }}" style="display:inline"
                  onsubmit="return confirm('Hapus kategori ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-red">Hapus</button>
            </form>
        </td>
    </tr>
    @empty
    <tr><td colspan="2">Belum ada kategori.</td></tr>
    @endforelse
</table>
@endsection