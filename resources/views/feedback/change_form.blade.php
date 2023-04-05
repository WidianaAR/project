@extends('layouts.navbar')

@section('isi')
    @if (Session::has('error'))
        <div class="alert alert-danger" role="alert" id="msg-box">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ Session::get('error') }}
        </div>
    @endif

    <div class="row m-0">
        <div class="col pl-1">
            <h5>Feedback auditor</h5>
        </div>
        <span class="text-muted">Feedback / <a href="">Ubah data</a></span>
    </div>

    <div class="element">
        <form action="{{ route('feedbacks.update', $feedback->id) }}" method="POST">
            @method('PUT')
            @csrf
            <div class="row align-items-center mb-3">
                <label class="col-3">Program studi : </label>
                <div class="col">
                    <select name="prodi_id" class="form-control @error('prodi_id') is-invalid @enderror" required>
                        @foreach ($prodis as $prodi)
                            <option value="{{ $prodi->id }}" @if (old('prodi_id') == $prodi->id || $feedback->prodi_id == $prodi->id) selected @endif>
                                {{ $prodi->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row align-items-center mb-3">
                <label class="col-3">Tanggal audit : </label>
                <div class="col">
                    <input type="date" name="tanggal_audit" value="{{ old('tanggal_audit', $feedback->tanggal_audit) }}"
                        class="w-100 form-control" required>
                </div>
            </div>
            <div class="row align-items-center">
                <label class="col-3">Temuan dan rekomendasi audit : </label>
                <div class="col">
                    <input id="keterangan" type="hidden" name="keterangan"
                        value="{{ old('keterangan', $feedback->keterangan) }}" required>
                    <trix-editor class="trix-content" input="keterangan"></trix-editor>
                </div>
            </div>
            <div class="row justify-content-end pr-3 mt-3">
                <a class="btn btn-danger mr-2" type="button" value="Batal" href="{{ route('feedbacks.index') }}">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
@endsection
