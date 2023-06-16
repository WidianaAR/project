@extends('layouts.navbar')

@section('title')
    <title>Panduan</title>
@endsection

@section('isi')
    <div class="row m-0">
        <div class="col pl-1">
            <h5>Panduan</h5>
        </div>
        <span class="text-muted">Panduan / <a href="">Tambah data</a></span>
    </div>

    <div class="element">
        <form action="{{ route('panduans.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row align-items-center mb-4">
                <label class="col-2">Judul : </label>
                <div class="col">
                    <input type="text" class="form-control form-control-sm @error('judul') is-invalid mb-0 @enderror"
                        name="judul" aria-describedby="judul_error" value="{{ old('judul') }}" required>
                    @error('judul')
                        <div class="invalid-feedback mt-0 mb-2" id="judul_error">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row align-items-center">
                <label class="col-2">Keterangan : </label>
                <div class="col">
                    <input id="keterangan" type="hidden" name="keterangan" value="{{ old('keterangan') }}" required>
                    <trix-editor class="trix-content" input="keterangan"></trix-editor>
                </div>
            </div>

            <div class="row align-items-center mt-4">
                <label class="col-2">File : </label>
                <div class="col">
                    <input type="file" class="form-control form-control-sm @error('file_data') is-invalid mb-0 @enderror"
                        name="file_data" aria-describedby="file_data_error">
                    @error('file_data')
                        <div class="invalid-feedback mt-0 mb-2" id="file_data_error">
                            {{ $message }}
                        </div>
                    @else
                        <small class="text-danger"><i class="fa fa-circle-info"></i> Unggah file baru jika ingin merubah file
                            panduan</small>
                    @enderror
                </div>
            </div>

            <div class="row justify-content-end pr-3 mt-3">
                <a class="btn btn-sm btn-secondary mr-2" type="button" value="Batal"
                    href="{{ route('panduans.index') }}">Batal</a>
                <button type="submit" class="btn btn-sm btn-primary">Tambah panduan</button>
            </div>
        </form>
    </div>
@endsection
