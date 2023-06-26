@extends('layouts.navbar')

@section('title')
    <title>Simulasi Akreditasi</title>
@endsection

@section('isi')
    @if (Session::has('error'))
        <div class="alert alert-danger" role="alert" id="msg-box">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ Session::get('error') }}
        </div>
    @endif

    <div class="row align-items-center">
        @if ($deadline[0])
            <div class="col-auto pr-0">
                Batas akhir upload file :
            </div>
            <div class="col text-left">
                @include('layouts.countdown')
            </div>
        @else
            <div class="col">
                <h5>Simulasi Akreditasi</h5>
            </div>
        @endif
        <div class="col text-right">
            <span class="text-muted">Simulasi akreditasi / <a href="">Upload file excel</a></span>
        </div>
    </div>

    <div class="element row justify-content-center">
        <div class="add-form col-6">
            <label class="mb-1">Instrumen simulasi akreditasi</label>
            <form action="{{ route('ed_import_action') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file"
                    class="form-control form-control-sm @error('file') m-0 is-invalid @enderror"
                    aria-describedby="file-error" required>
                @error('file')
                    <div class="invalid-feedback mb-2" id="file-error">
                        {{ $message }}
                    </div>
                @enderror

                <label class="mb-1">Program studi</label>
                <select name='prodi' class="custom-select" required>
                    <option value='' selected></option>
                    @foreach ($prodis as $prodi)
                        <option value="{{ $prodi->id }}" {{ old('prodi') == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->nama_prodi }}</option>
                    @endforeach
                </select>
                <input type="text" name="tahun" value="{{ date('Y') }}" hidden>
                <div class="mt-3 d-md-flex justify-content-md-end">
                    <a class="btn btn-sm btn-secondary mr-2" type="button" value="Batal"
                        href="{{ route('ed_home') }}">Batal</a>
                    <input class="btn btn-sm btn-primary" type="submit" value="Tambah data">
                </div>
            </form>
        </div>
    </div>
@endsection
