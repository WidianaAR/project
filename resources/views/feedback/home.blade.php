@extends('layouts.navbar')
@section('isi')
    <div class="row align-items-center">
        <div class="col">
            <span class="text-muted">Feedback / <a href="">{{ $keterangan }}</a></span>
        </div>
        @if ($years)
            <div class="col-auto text-left box">
                <button class="simple" type="button" data-toggle="dropdown" aria-expanded="false">
                    Ketegori <i class='fa fa-angle-down fa-sm'></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="moduleDropDown">
                    <a class="dropdown-item" href="{{ route('feedback', 'evaluasi') }}">Evaluasi diri</a>
                    <a class="dropdown-item" href="{{ route('feedback', 'standar') }}">Ketercapaian standar</a>
                </div>
            </div>

            <div class="col-auto text-left box mr-3">
                <button class="simple" type="button" data-toggle="dropdown" aria-expanded="false">
                    Tahun <i class='fa fa-angle-down fa-sm'></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="moduleDropDown">
                    <a class="dropdown-item"
                        href="@if ($data_evaluasi) {{ route('fb_year', ['evaluasi', 'all']) }} @else {{ route('fb_year', ['standar', 'all']) }} @endif">Semua</a>
                    @foreach ($years as $year)
                        <a class="dropdown-item"
                            href="@if ($data_evaluasi) {{ route('fb_year', ['evaluasi', $year]) }} @else {{ route('fb_year', ['standar', $year]) }} @endif">{{ $year }}</a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="element">
        @if ($data_evaluasi)
            @foreach ($data_evaluasi as $feedback)
                <a href="{{ route('fb_ed_table', $feedback->id) }}" class="feedback-link">
                    <div class="card feedback-list mb-2">
                        <div class="card-text p-1">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-regular fa-file-lines fa-lg pl-2" style="color: #ababab;"></i>
                                </div>
                                <div class="col p-0">
                                    Evaluasi diri program studi
                                    {{ $feedback->prodi->nama_prodi }} <br>
                                    <small><span class="text-muted">{{ $feedback->tahun }}</span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        @elseif ($data_standar)
            @foreach ($data_standar as $feedback)
                <a href="{{ route('fb_ks_table', $feedback->id) }}" class="feedback-link">
                    <div class="card feedback-list mb-2">
                        <div class="card-text p-1">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-regular fa-file-lines fa-lg pl-2" style="color: #ababab;"></i>
                                </div>
                                <div class="col p-0">
                                    Ketercapaian standar program studi
                                    {{ $feedback->prodi->nama_prodi }} <br>
                                    {{-- <small><span class="text-muted">{{ date('d-m-Y', strtotime($feedback->tanggal_audit)) }}</span></small> --}}
                                    <small><span class="text-muted">{{ $feedback->tahun }}</span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        @else
            <h5 class="text-center">Data kosong</h5>
        @endif
    </div>
@endsection
