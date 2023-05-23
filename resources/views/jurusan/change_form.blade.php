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
            <span class="text-muted">Jurusan / <a href="{{ route('jurusans.create') }}">Ubah jurusan</a></span>
        </div>
    </div>

    <div class="element row justify-content-center">
        <div class="add-form col-6">
            <form action="{{ route('jurusans.update', $data->id) }}" method="POST">
                @method('put')
                @csrf
                <label class="mb-1">Kode jurusan</label>
                <input type="text" name="kode_jurusan" id="kode_jurusan"
                    class="form-control form-control-sm @error('kode_jurusan') is-invalid mb-0 @enderror"
                    aria-describedby="kode_jurusan_error" value="{{ old('kode_jurusan', $data->kode_jurusan) }}" required>
                @error('kode_jurusan')
                    <div class="invalid-feedback mt-0 mb-2" id="kode_jurusan_error">
                        {{ $message }}
                    </div>
                @enderror

                <label class="mb-1" for="basic-url" class="form-label">Singkatan</label>
                <input type="text" name="nama_jurusan"
                    class="form-control form-control-sm @error('nama_jurusan') is-invalid mb-0 @enderror"
                    aria-describedby="nama_jurusan_error" value="{{ old('nama_jurusan', $data->nama_jurusan) }}" required>
                @error('nama_jurusan')
                    <div class="invalid-feedback mt-0 mb-2" id="nama_jurusan_error">
                        {{ $message }}
                    </div>
                @enderror

                <label class="mb-1" for="basic-url" class="form-label">Nama jurusan</label>
                <input type="text" name="keterangan"
                    class="form-control form-control-sm @error('keterangan') is-invalid mb-0 @enderror"
                    aria-describedby="keterangan_error" value="{{ old('keterangan', $data->keterangan) }}" required>
                @error('keterangan')
                    <div class="invalid-feedback mt-0 mb-2" id="keterangan_error">
                        {{ $message }}
                    </div>
                @enderror

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a class="btn btn-sm btn-secondary mr-2" type="button" href="{{ route('jurusans.index') }}">Batal</a>
                    <input class="btn btn-sm btn-primary" type="submit" value="Simpan perubahan">
                </div>
            </form>
        </div>
    </div>
@endsection
