@extends('layouts.navbar')
@section('isi')
    <div class="row m-0">
        <div class="col pl-1">
            <span class="text-muted">Feedback / <a href="">Detail</a></span>
        </div>
        <a href="@if (Auth::user()->role_id == 4) {{ route('feedbacks.index') }} @else {{ route('feedback_home') }} @endif"
            type="button" class="btn btn-danger"><i class="fa fa-arrow-left" aria-hidden="true"></i>Kembali</a>
    </div>

    <div class="element">
        <div class="border-bottom">
            <h5 class="mb-0">Temuan dan rekomendasi audit {{ $feedback->prodi->jurusan->nama_jurusan }} program studi
                {{ $feedback->prodi->nama_prodi }}</h5>
            <small class="text-muted">{{ date('d-m-Y', strtotime($feedback->tanggal_audit)) }}</small>
        </div>
        <div class="mt-2">{!! $feedback->keterangan !!}</div>
    </div>
@endsection
