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
            <h5>Program studi</h5>
        </div>
        <div class="col p-0 text-right">
            <span class="text-muted">Program studi / <a href="{{ route('prodis.create') }}">Tambah prodi</a></span>
        </div>
    </div>

    <div class="element">
        <div class="add-form">
            <form action="{{ route('prodis.store') }}" method="POST">
                @csrf
                <input type="text" name="kode_prodi" placeholder="Kode Prodi"
                    class="form-control @error('kode_prodi') is-invalid mb-0 @enderror" aria-describedby="kode_prodi_error"
                    value="{{ old('kode_prodi') }}" required>
                @error('kode_prodi')
                    <div class="invalid-feedback mt-0 mb-2" id="kode_prodi_error">
                        {{ $message }}
                    </div>
                @enderror

                <select name="jurusan_id" class="form-control @error('jurusan_id') is-invalid @enderror" required>
                    <option value="">Jurusan</option>
                    @foreach ($jurusans as $jurusan)
                        <option value="{{ $jurusan->id }}" {{ old('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                            {{ $jurusan->nama_jurusan }}</option>
                    @endforeach
                </select>

                <input type="text" name="nama_prodi" placeholder="Nama Prodi"
                    class="form-control @error('nama_prodi') is-invalid mb-0 @enderror" aria-describedby="nama_prodi_error"
                    value="{{ old('nama_prodi') }}" required>
                @error('nama_prodi')
                    <div class="invalid-feedback mt-0 mb-2" id="nama_prodi_error">
                        {{ $message }}
                    </div>
                @enderror

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a class="btn btn-danger mr-2" type="button" value="Batal"
                        href="{{ route('prodis.index') }}">Batal</a>
                    <input class="btn btn-primary" type="submit" value="Tambah">
                </div>
            </form>
        </div>
    </div>
@endsection
