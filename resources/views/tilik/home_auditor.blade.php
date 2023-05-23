<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>

<body>
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
                    <a class="dropdown-item {{ Request::is(['tilik_auditor', 'tilik_auditor/semua*']) ? 'active' : '' }}"
                        href="{{ route('tilik_home_auditor') }}">Semua data</a>
                    <a class="dropdown-item {{ Request::is('tilik_auditor/evaluasi*') ? 'active' : '' }}"
                        href="{{ route('tilik_home_auditor', 'evaluasi') }}">Evaluasi diri</a>
                    <a class="dropdown-item {{ Request::is('tilik_auditor/standar*') ? 'active' : '' }}"
                        href="{{ route('tilik_home_auditor', 'standar') }}">Ketercapaian standar</a>
                </div>
            </div>

            @if ($years)
                <div class="col-auto text-left box mr-3">
                    <button class="simple" type="button" data-toggle="dropdown" aria-expanded="false">
                        Tahun <i class='fa fa-angle-down fa-sm'></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="moduleDropDown">
                        @foreach ($years as $year)
                            <a class="dropdown-item {{ Request::is(['tilik_auditor/evaluasi/' . $year, 'tilik_auditor/standar/' . $year, 'tilik_auditor/semua/' . $year]) ? 'active' : '' }}"
                                href="@if ($keterangan == 'Evaluasi diri') {{ route('tilik_year_auditor', ['evaluasi', $year]) }} @elseif ($keterangan == 'Ketercapaian standar') {{ route('tilik_year_auditor', ['standar', $year]) }} @else {{ route('tilik_year_auditor', ['semua', $year]) }} @endif">{{ $year }}</a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="element pb-1 table-responsive">
            @if ($data->count())
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>File</th>
                            <th>Kategori</th>
                            <th>Program Studi</th>
                            <th>Tahun</th>
                            <th>Status</th>
                            <th>Action</th>
                    </thead>
                    <tbody>
                        @foreach ($data as $file)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ basename($file->file_data) }}</td>
                                <td>{{ $file->kategori }}</td>
                                <td>{{ $file->prodi->nama_prodi }}</td>
                                <td>{{ $file->tahun }}</td>
                                <td>
                                    <a href="" data-toggle="modal"
                                        data-target="#tahapModal{{ $loop->iteration }}">{{ $file->status->keterangan }}</a>
                                </td>
                                <td>
                                    @if ($file->status_id == 2)
                                        <a href="@if ($file->kategori == 'evaluasi') {{ route('tilik_ed_table', $file->id) }} @else {{ route('tilik_ks_table', $file->id) }} @endif"
                                            class="btn btn-sm btn-outline-primary">Tambah tilik</a>
                                    @elseif ($file->status_id == 3)
                                        <a href="@if ($file->kategori == 'evaluasi') {{ route('tilik_ed_table', $file->id) }} @else {{ route('tilik_ks_table', $file->id) }} @endif"
                                            class="btn btn-sm btn-outline-success">Lihat</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $data->links() }}
            @else
                <h5>Data Kosong</h5>
            @endif
        </div>
    @endsection

    @foreach ($data as $file)
        <div class="modal fade" id="tahapModal{{ $loop->iteration }}" role="dialog" arialabelledby="modalLabel"
            area-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="row pl-5">
                        @include('layouts.tahap_breadcrumb')
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</body>

</html>
