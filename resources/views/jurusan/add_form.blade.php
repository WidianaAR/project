@extends('layouts.navbar')
@section('isi')
    @if (session('error_store'))
        <div class="alert alert-danger" role="alert" id="msg-box">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ $message }}
        </div>
    @endif

    <div class="row m-0">
        <div class="col pl-1">
            <h5>Jurusan</h5>
        </div>
        <div class="col p-0 text-right">
            <span class="text-muted">Jurusan / <a href="{{ route('jurusans.create') }}">Tambah jurusan</a></span>
        </div>
    </div>

    <div class="element">
        <div class="add-form">
            <form action="{{ route('jurusans.store') }}" method="POST">
                @csrf
                <input type="text" name="kode_jurusan" placeholder="Kode Jurusan"
                    class="form-control @error('kode_jurusan') is-invalid mb-0 @enderror"
                    aria-describedby="kode_jurusan_error" value="{{ old('kode_jurusan') }}" required>
                @error('kode_jurusan')
                    <div class="invalid-feedback mt-0 mb-2" id="kode_jurusan_error">
                        {{ $message }}
                    </div>
                @enderror

                <input type="text" name="nama_jurusan" placeholder="Singkatan"
                    class="form-control @error('nama_jurusan') is-invalid mb-0 @enderror"
                    aria-describedby="nama_jurusan_error" value="{{ old('nama_jurusan') }}" required>
                @error('nama_jurusan')
                    <div class="invalid-feedback mt-0 mb-2" id="nama_jurusan_error">
                        {{ $message }}
                    </div>
                @enderror

                <input type="text" name="keterangan" placeholder="Nama Jurusan"
                    class="form-control @error('keterangan') is-invalid mb-0 @enderror" aria-describedby="keterangan_error"
                    value="{{ old('keterangan') }}" required>
                @error('keterangan')
                    <div class="invalid-feedback mt-0 mb-2" id="keterangan_error">
                        {{ $message }}
                    </div>
                @enderror

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a class="btn btn-danger mr-2" type="button" value="Batal"
                        href="{{ route('jurusans.index') }}">Batal</a>
                    <input class="btn btn-primary" type="submit" value="Tambah">
                </div>
            </form>
        </div>
    </div>
@endsection
