@extends('layouts.crud')
@section('title', $category->exists ? 'Ubah Kategori' : 'Tambah Kategori')
@section('content')
<form method="POST" action="{{ $category->exists ? route('categories.update', $category) : route('categories.store') }}">
    @csrf
    @if($category->exists) @method('PUT') @endif
    <label>Nama kategori</label>
    <input name="name" value="{{ old('name', $category->name) }}" required>
    <p><button class="btn">Simpan</button> <a class="btn btn-gray" href="{{ route('categories.index') }}">Batal</a></p>
</form>
@endsection