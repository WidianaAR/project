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
            <span class="text-muted">Simulasi akreditasi / <a href="">Ubah data</a></span>
        </div>
    </div>

    <div class="element row justify-content-center">
        <div class="add-form col-6">
            <label class="mb-1">Instrumen simulasi akreditasi</label>
            <form action="{{ route('ed_change_action') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="text" name="id_evaluasi" value="{{ $data->id }}" hidden>
                <input type="file" name="file"
                    class="form-control form-control-sm m-0 @error('file') is-invalid @enderror"
                    aria-describedby="file-error">
                @error('file')
                    <div class="invalid-feedback mb-2" id="file-error">
                        {{ $message }}
                    </div>
                @else
                    <p><small class="text-danger"> <i class="fa fa-circle-info"></i> Isi jika ingin merubah file excel instrumen
                            simulasi akreditasi prodi</small></p>
                @enderror

                <label class="mb-1">Program studi</label>
                <select name='prodi' class="custom-select" required>
                    @foreach ($prodis as $prodi)
                        <option value="{{ $prodi->id }}" @if (old('prodi') == $prodi->id || $data->prodi_id == $prodi->id) selected @endif>
                            {{ $prodi->nama_prodi }}</option>
                    @endforeach
                </select>
                <input type="text" name="tahun" value="{{ $data->tahun }}" hidden>
                <div class="mt-3 d-md-flex justify-content-md-end">
                    <a class="btn btn-sm btn-secondary mr-2" type="button" value="Batal"
                        href="{{ route('ed_home') }}">Batal</a>
                    <input class="btn btn-sm btn-primary" type="submit" value="Simpan perubahan">
                </div>
            </form>
        </div>
    </div>
@endsection
