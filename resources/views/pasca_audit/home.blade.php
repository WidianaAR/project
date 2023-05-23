@extends('layouts.navbar')

@section('isi')
    <div class="row align-items-center">
        <div class="col">
            <span class="text-muted">Daftar tilik / <a href="">{{ $keterangan }}</a></span>
        </div>
        <div class="col-auto text-left box @if (!$years) mr-3 @endif">
            <button class="simple" type="button" data-toggle="dropdown" aria-expanded="false">
                Ketegori <i class='fa fa-angle-down fa-sm'></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="moduleDropDown">
                <a class="dropdown-item {{ Request::is(['tilik', 'tilik/semua*']) ? 'active' : '' }}"
                    href="{{ route('tilik_home') }}">Semua data</a>
                <a class="dropdown-item {{ Request::is('tilik/evaluasi*') ? 'active' : '' }}"
                    href="{{ route('tilik_home', 'evaluasi') }}">Evaluasi diri</a>
                <a class="dropdown-item {{ Request::is('tilik/standar*') ? 'active' : '' }}"
                    href="{{ route('tilik_home', 'standar') }}">Ketercapaian standar</a>
            </div>
        </div>

        @if ($years)
            <div class="col-auto text-left box">
                <button class="simple" type="button" data-toggle="dropdown" aria-expanded="false">
                    Tahun <i class='fa fa-angle-down fa-sm'></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="moduleDropDown">
                    @foreach ($years as $year)
                        <a class="dropdown-item {{ Request::is(['tilik/evaluasi/' . $year, 'tilik/standar/' . $year, 'tilik/semua/' . $year]) ? 'active' : '' }}"
                            href="@if ($keterangan == 'Evaluasi diri') {{ route('tilik_year', ['evaluasi', $year]) }} @elseif ($keterangan == 'Ketercapaian standar') {{ route('tilik_year', ['standar', $year]) }} @else {{ route('tilik_year', ['semua', $year]) }} @endif">{{ $year }}</a>
                    @endforeach
                </div>
            </div>
        @endif
        <div class="col-auto text-left pl-2">
            <form action="{{ route('ed_export_all') }}" method="POST">
                @csrf
                @foreach ($data as $file)
                    <input name="data[]" type="hidden" value="{{ $file->file_data }}">
                @endforeach
                <input type="submit" class="btn btn-sm btn-primary" value="Export Semua File">
            </form>
        </div>
    </div>

    <div class="element pb-1">
        @if ($data->count())
            @foreach ($data as $file)
                <a href="@if ($file->kategori == 'evaluasi') {{ route('tilik_ed_table', $file->id) }} @else {{ route('tilik_ks_table', $file->id) }} @endif"
                    class="decoration-none">
                    <div class="card file-list mb-2">
                        <div class="card-text p-1">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-regular fa-file-lines fa-lg pl-2" style="color: #ababab;"></i>
                                </div>
                                <div class="col p-0">
                                    @if ($file->kategori == 'evaluasi')
                                        Evaluasi diri
                                    @else
                                        Ketercapaian standar
                                    @endif
                                    {{ $file->prodi->nama_prodi }} <br>
                                    <small><span class="text-muted">{{ $file->tahun }} | Status :
                                            {{ $file->status->keterangan }}</span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
            <div class="d-flex justify-content-end">
                {{ $data->links() }}
            </div>
        @else
            <h5 class="text-center">Data kosong</h5>
        @endif
    </div>
@endsection
