@extends('layouts.navbar')

@section('isi')
    @if ($errors->any())
        <div class="alert alert-danger" role="alert" id="msg-box">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif

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
            <span class="text-muted">Evaluasi diri / <a href="">Atur deadline</a></span>
        </div>
    </div>

    <form action="{{ route('ed_set_time_action') }}" method="POST">
        @csrf
        <div class="col element">
            <div class="row mb-3 px-3">
                <label class="col-sm-2 col-form-label">Atur Tanggal : </label>
                <div class="col-sm-10">
                    @if ($deadline[0] != null)
                        <input type="text" name="id" value="{{ $deadline[1] }}" hidden>
                        <input type="date" name="date" value="{{ date('Y-m-d', strtotime($deadline[0])) }}"
                            class="w-100 form-control" required>
                    @else
                        <input type="text" name="id" value="" hidden>
                        <input type="date" name="date" class="w-100 form-control" required>
                    @endif
                </div>
            </div>
            <div class="row mb-3 px-3">
                <label class="col-sm-2 col-form-label">Atur Waktu : </label>
                <div class="col-sm-10">
                    @if ($deadline[0] != null)
                        <input type="time" name="time" value="{{ date('H:i', strtotime($deadline[0])) }}"
                            class="w-100 form-control" required>
                    @else
                        <input type="time" name="time" class="w-100 form-control" required>
                    @endif
                </div>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end pr-3">
                <a class="btn btn-danger mr-2" type="button" value="Batal" href="{{ URL('evaluasi') }}">Batal</a>
                <input class="btn btn-primary" type="submit" value="Simpan">
            </div>
        </div>
    </form>
@endsection
