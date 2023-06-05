@extends('layouts.navbar')

@section('title')
    <title>Prodi</title>
@endsection

@section('isi')
    @if (session('error_store'))
        <div class="alert alert-danger" role="alert" id="msg-box">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ $message }}
        </div>
    @endif

    <div class="row m-0">
        <div class="col pl-1">
            <h5>Program studi</h5>
        </div>
        <div class="col p-0 text-right">
            <span class="text-muted">Program studi / <a href="{{ route('prodis.create') }}">Tambah prodi</a></span>
        </div>
    </div>

    <div class="element row justify-content-center">
        <div class="add-form col-6">
            <form action="{{ route('prodis.store') }}" method="POST">
                @csrf
                <label class="mb-1">Kode program studi</label>
                <input type="text" name="kode_prodi"
                    class="form-control form-control-sm @error('kode_prodi') is-invalid mb-0 @enderror"
                    aria-describedby="kode_prodi_error" value="{{ old('kode_prodi') }}" required>
                @error('kode_prodi')
                    <div class="invalid-feedback mt-0 mb-2" id="kode_prodi_error">
                        {{ $message }}
                    </div>
                @enderror

                <label class="mb-1">Jurusan</label>
                <select name="jurusan_id" class="form-control form-control-sm @error('jurusan_id') is-invalid @enderror"
                    required>
                    <option value=""></option>
                    @foreach ($jurusans as $jurusan)
                        <option value="{{ $jurusan->id }}" {{ old('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                            {{ $jurusan->nama_jurusan }}</option>
                    @endforeach
                </select>

                <option value="">Nama program studi</option>
                <input type="text" name="nama_prodi"
                    class="form-control form-control-sm @error('nama_prodi') is-invalid mb-0 @enderror"
                    aria-describedby="nama_prodi_error" value="{{ old('nama_prodi') }}" required>
                @error('nama_prodi')
                    <div class="invalid-feedback mt-0 mb-2" id="nama_prodi_error">
                        {{ $message }}
                    </div>
                @enderror

                <div class="pt-2 d-md-flex justify-content-md-end">
                    <a class="btn btn-sm btn-secondary mr-2" type="button" value="Batal"
                        href="{{ route('prodis.index') }}">Batal</a>
                    <input class="btn btn-sm btn-primary" type="submit" value="Tambah prodi">
                </div>
            </form>
        </div>
    </div>
@endsection
