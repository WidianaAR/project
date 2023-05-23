@extends('layouts.navbar')

@section('isi')
    <div class="row m-0">
        <div class="col pl-1">
            <h5>Panduan</h5>
        </div>
        <span class="text-muted">Panduan / <a href="">Ubah data</a></span>
    </div>

    <div class="element">
        <form action="{{ route('panduans.update', $panduan->id) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="row align-items-center mb-4">
                <label class="col-2">Judul : </label>
                <div class="col">
                    <input type="text" class="form-control form-control-sm @error('judul') is-invalid mb-0 @enderror"
                        name="judul" aria-describedby="judul_error" value="{{ old('judul', $panduan->judul) }}" required>
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
                    <input id="keterangan" type="hidden" name="keterangan"
                        value="{{ old('keterangan', $panduan->keterangan) }}" required>
                    <trix-editor class="form-control form-control-sm trix-content" input="keterangan"></trix-editor>
                </div>
            </div>

            <div class="row align-items-center mt-4">
                <label class="col-2">File : </label>
                <div class="col">
                    <input type="file" class="form-control form-control-sm @error('file_data') is-invalid mb-0 @enderror"
                        name="file_data" aria-describedby="file_data_error" required>
                    @error('file_data')
                        <div class="invalid-feedback mt-0 mb-2" id="file_data_error">
                            {{ message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row justify-content-end pr-3 mt-3">
                <a class="btn btn-sm btn-secondary mr-2" type="button" value="Batal"
                    href="{{ route('panduans.index') }}">Batal</a>
                <button type="submit" class="btn btn-sm btn-primary">Simpan perubahan</button>
            </div>
        </form>
    </div>
@endsection
