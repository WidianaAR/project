@extends('layouts.navbar')

@section('isi')
    <div class="row align-items-center">
        @if ($deadline[0])
            <div class="col-auto pr-0">
                Batas akhir upload file :
            </div>
            <div class="col text-left">
                @include('ketercapaian_standar/countdown')
            </div>
        @else
            <div class="col">
                <h5>Ketercapaian standar</h5>
            </div>
        @endif
        <div class="col text-right">
            <span class="text-muted">Ketercapaian standar / <a href="">Ubah data</a></span>
        </div>
    </div>

    <div class="element">
        <div class="add-form">
            <form action="{{ route('ks_change_action') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="text" name="id_standar" value="{{ $data->id }}" hidden>
                <input type="file" name="file" class="form-control @error('file') m-0 is-invalid @enderror"
                    aria-describedby="file-error" required>
                @error('file')
                    <div class="invalid-feedback mb-2" id="file-error">
                        {{ $message }}
                    </div>
                @enderror
                <select name='prodi' class="custom-select" required>
                    @foreach ($prodis as $prodi)
                        <option value="{{ $prodi->id }}" @if (old('prodi') == $prodi->id || $data->prodi_id == $prodi->id) selected @endif>
                            {{ $prodi->nama_prodi }}</option>
                    @endforeach
                </select>
                <input type="text" name="tahun" value="{{ $data->tahun }}" hidden>
                <div class="d-grid mt-3 gap-2 d-md-flex justify-content-md-end">
                    <a class="btn btn-danger mr-2" type="button" value="Batal" href="{{ route('ks_home') }}">Batal</a>
                    <input class="btn btn-primary" type="submit" value="Simpan">
                </div>
            </form>
        </div>
    </div>
@endsection
