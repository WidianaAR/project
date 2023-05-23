@extends('layouts.navbar')
@section('isi')
    <div class="row m-0">
        <div class="col pl-1">
            <span class="text-muted">Panduan / <a href="">Detail</a></span>
        </div>
        <a href="@if (Auth::user()->role_id == 1) {{ route('panduans.index') }} @else {{ route('panduan_home') }} @endif"
            type="button" class="btn btn-sm btn-secondary"><i class="fa fa-arrow-left" aria-hidden="true"></i>Kembali</a>
    </div>

    <div class="element">
        <div class="border-bottom text-center pb-2">
            <h5 class="mb-0">{{ $panduan->judul }}</h5>
        </div>
        <div class="mt-2">{!! $panduan->keterangan !!}</div>
        <div class="mt-4">Download file : <a
                href="{{ route('panduan_download', $panduan->id) }}">{{ basename($panduan->file_data) }}</a></div>
    </div>
@endsection
