<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ketercapaian Standar</title>
    <link rel="stylesheet" href="/css/app.css">
</head>

<body>
    @extends('layouts.navbar')

    @section('isi')
        @if (Session::has('success'))
            <div class="alert alert-success" role="alert" id="msg-box">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                {{ Session::get('success') }}
            </div>
        @endif

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
                    @include('ketercapaian_standar/countdown')
                </div>
            @else
                <div class="col">
                    <h5>Ketercapaian standar</h5>
                </div>
            @endif

            @canany(['koorprodi', 'auditor'])
                @if ($years)
                    <div class="col-auto text-right box mx-0">
                        <button class="simple" type="button" data-toggle="dropdown" aria-expanded="false">
                            Tahun <i class='fa fa-angle-down fa-sm'></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="moduleDropDown">
                            @foreach ($years as $year)
                                <a class="dropdown-item" href="{{ route('ks_filter_year', $year) }}">{{ $year }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endcanany

            @can('pjm')
                <div class="col text-right p-0">
                    <form action="{{ route('ks_export_file') }}" method="POST">
                        @csrf
                        <input name="filename" type="hidden" value="{{ $data->file_data }}">
                        <input type="submit" class="btn btn-primary" value="Export File">
                    </form>
                </div>
            @endcan

            {{-- Data --}}
            @can('koorprodi')
                <div class="col-auto text-left @if (($data && $data->tahun != date('Y')) || !$deadline[0]) pl-0 @endif">
                    @if ($deadline[0])
                        @if ($id_standar && $data && $data->tahun == date('Y'))
                            <a type="button" class="btn btn-danger" href="{{ route('ks_delete', $id_standar) }}"
                                onclick="return confirm('Apakah Anda Yakin Menghapus File?');"><i class="fas fa-trash"></i>
                                Hapus File Excel</a>
                            <a type="button" class="btn btn-primary" href="" data-toggle="modal"
                                data-target="#importModal"><i class="fas fa-file-upload"></i> Ganti File Excel</a>
                        @elseif(!$id_standar)
                            <a type="button" class="btn btn-primary" href="" data-toggle="modal"
                                data-target="#importModal"><i class="fas fa-file-upload"></i> Import File Excel</a>
                        @endif
                    @endif
                </div>
            @endcan

            @cannot('koorprodi')
                <div class="@if ($data) col-auto text-left @else ml-3 @endif">
                    @can('auditor')
                        @if ($data)
                            @if ($data->status == 'ditinjau' && $deadline[0] && $data->tahun == date('Y'))
                                <a type="button" class="btn btn-secondary" href="" data-toggle="modal"
                                    data-target="#feedbackModal">
                                    Perlu Perbaikan
                                </a>
                                <a type="button" class="btn btn-success" href="{{ route('ks_confirm', $id_standar) }}"
                                    onclick="return confirm('Apakah Anda yakin menyetujui data ini? Data yang sudah disetujui akan disimpan ke dalam statistik');">
                                    Konfirmasi
                                </a>
                            @elseif ($data->status == 'disetujui' && $deadline[0] && $data->tahun == date('Y'))
                                <a type="button" class="btn btn-secondary" href="{{ route('ks_cancel_confirm', $id_standar) }}"
                                    onclick="return confirm('Apakah Anda yakin membatalkan data ini? Data yang sudah dibatalkan akan dihapus dari statistik');">
                                    Batal Setujui
                                </a>
                            @endif
                            <a type="button" class="btn btn-primary" href="" data-toggle="modal" data-target="#importModal">
                                <i class="fas fa-file-upload"></i> Ganti File Excel
                            </a>
                        @endif
                    @else
                        <a type="button" class="btn btn-danger" href="{{ route('ks_home') }}">
                            <i class="fa fa-arrow-left" aria-hidden="true"></i> Kembali
                        </a>
                    @endcan
                </div>
            @endcannot
        </div>

        @if ($data and $data->keterangan)
            <div class="row align-items-center my-3 px-3">
                <span class="border border-danger p-2" style="width: 100%">
                    <b>Yang perlu diperbaiki:</b>
                    <br>
                    {{ $data->keterangan }}
                </span>
            </div>
        @endif

        {{-- Table --}}
        @if ($headers)
            @for ($i = 0; $i < count($sheetName) - 2; $i++)
                <div class="element text-right">
                    <span class="text-muted">{{ $data->prodi->nama_prodi }} / <a
                            href="">{{ $data->tahun }}</a></span>
                    <table class="table table-bordered mt-2">
                        <thead class="thead">
                            <th colspan="8" class="py-1">
                                <h5>{{ $sheetName[$i] }}</h5>
                            </th>
                            <tr>
                                @foreach ($headers[$i] as $header)
                                    @if ($header && $header != 'Temuan')
                                        <th class="py-1 align-middle">{{ $header }}</th>
                                    @endif
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sheetData[$i] as $sheet)
                                @if ($sheet['D'])
                                    <tr>
                                        @foreach (range('A', 'C') as $v)
                                            <td> {{ $sheet[$v] }} </td>
                                        @endforeach
                                        <td>
                                            {{ $sheet['D'] }}
                                            {{ $sheet['E'] }}
                                            {{ $sheet['F'] }}
                                        </td>
                                        @foreach (range('G', 'I') as $v)
                                            <td> {{ $sheet[$v] }} </td>
                                        @endforeach
                                        <td>
                                            <a href="{{ $sheet['J'] }}">
                                                {{ strip_tags(\Illuminate\Support\Str::limit($sheet['J'], 5, '...')) }}
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endfor
        @else
            <div class="my-3 text-center element">
                <h5>Koorprodi belum memasukkan data ketercapaian standar tahun {{ date('Y') }}</h5>
            </div>
        @endif
        </div>

        @can('pjm')
            <div class="floating-action-button">
                <a type="button" href="{{ route('ks_set_time') }}" class="btn"><i class='fa fa-clock fa-2x'
                        style='color: #0D64AC'></i></a>
            </div>
        @endcan
    @endsection

    <div class="modal fade" id="importModal" role="dialog" arialabelledby="modalLabel" area-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('ks_import_action') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <h2>Pilih File</h2>
                        <input type="file" name="file">
                        <input type="text" name="prodi" value="{{ Auth::user()->prodi_id }}" hidden>
                        <input type="text" name="tahun" value="{{ $data->tahun ?? date('Y') }}" hidden>
                        <input type="text" name="id" value="{{ $data->id ?? '' }}" hidden>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <input class="btn btn-primary" type="submit" value="Simpan">
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="feedbackModal" role="dialog" arialabelledby="modalLabel" area-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('ks_feedback') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="text" name="id_standar" value="{{ $id_standar }}" hidden>
                        <h5>Apa yang perlu diperbaiki?</h5>
                        <textarea name="feedback" style="height: 200px; width: 100%"> </textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <input class="btn btn-primary" type="submit" value="Simpan">
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</body>

</html>
