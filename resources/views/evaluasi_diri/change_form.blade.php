@extends('layouts.navbar')

@section('isi')
    <div class="row align-items-center">
        @if ($deadline[0])
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
            <span class="text-muted">Evaluasi diri / <a href="">Ubah data</a></span>
        </div>
    </div>

    <div class="element row justify-content-center">
        <div class="add-form col-6">
            <label class="mb-1">File evaluasi diri</label>
            <form action="{{ route('ed_change_action') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="text" name="id_evaluasi" value="{{ $data->id }}" hidden>
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
                    @foreach ($prodis as $prodi)
                        <option value="{{ $prodi->id }}" @if (old('prodi') == $prodi->id || $data->prodi_id == $prodi->id) selected @endif>
                            {{ $prodi->nama_prodi }}</option>
                    @endforeach
                </select>
                <input type="text" name="tahun" value="{{ $data->tahun }}" hidden>
                <div class="d-grid mt-3 gap-2 d-md-flex justify-content-md-end">
                    <a class="btn btn-sm btn-secondary mr-2" type="button" value="Batal"
                        href="{{ route('ed_home') }}">Batal</a>
                    <input class="btn btn-sm btn-primary" type="submit" value="Simpan perubahan">
                </div>
            </form>
        </div>
    </div>
@endsection
