@extends('layouts.navbar')

@section('isi')
    <div class="row align-items-center">
        @if (!!$deadline[0])
            <div class="col-auto pr-0">
                Batas akhir upload file :
            </div>
            <div class="col text-left">
                @include('evaluasi_diri/countdown')
            </div>
        @else
            <div class="col">
                <h5>Evaluasi Diri</h5>
            </div>
        @endif
        <div class="col text-right">
            <span class="text-muted">Evaluasi diri / <a href="">Upload file</a></span>
        </div>
    </div>

    <div class="element">
        <div class="add-form">
            <form action="{{ route('ed_import_action') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" class="form-control @error('file') m-0 is-invalid @enderror"
                    aria-describedby="file-error" required>
                @error('file')
                    <div class="invalid-feedback mb-2" id="file-error">
                        {{ $message }}
                    </div>
                @enderror
                <select name='prodi' class="custom-select" required>
                    <option value='' selected>Pilih program studi</option>
                    @foreach ($prodis as $prodi)
                        <option value="{{ $prodi->id }}" {{ old('prodi') == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->nama_prodi }}</option>
                    @endforeach
                </select>
                <input type="text" name="jurusan" value="{{ Auth::user()->jurusan_id }}" hidden>
                <input type="text" name="tahun" value="{{ date('Y') }}" hidden>
                <div class="d-grid mt-3 gap-2 d-md-flex justify-content-md-end">
                    <a class="btn btn-danger mr-2" type="button" value="Batal" href="{{ route('ed_home') }}">Batal</a>
                    <input class="btn btn-primary" type="submit" value="Simpan">
                </div>
            </form>
        </div>
    </div>
@endsection
