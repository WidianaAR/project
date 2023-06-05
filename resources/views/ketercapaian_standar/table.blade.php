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
                    @include('layouts.countdown')
                </div>
            @else
                <div class="col">
                    <h5>Ketercapaian standar</h5>
                </div>
            @endif

            @can('koorprodi')
                @if ($years)
                    <div class="col-auto text-right box mx-2">
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
            @endcan

            @if ($file)
                <div class="col-auto text-right p-0">
                    <form action="{{ route('ks_export_file') }}" method="POST">
                        @csrf
                        <input name="filename" type="hidden" value="{{ $file->file_data }}">
                        <input type="submit" class="btn btn-sm btn-primary" value="Export File">
                    </form>
                </div>
            @endif

            @can('koorprodi')
                <div class="col-auto text-left @if (($file && $file->tahun != date('Y')) || !$deadline[0]) pl-0 @else pl-1 @endif">
                    @if ($deadline[0])
                        @if ($id_standar && $file && $file->tahun == date('Y'))
                            <a type="button" class="btn btn-sm btn-success" href="" data-toggle="modal"
                                data-target="#importModal"><i class="fas fa-file-upload"></i> Ganti File Excel</a>
                            <a type="button" class="btn btn-sm btn-danger" href="{{ route('ks_delete', $id_standar) }}"
                                onclick="return confirm('Apakah Anda Yakin Menghapus File?');"><i class="fas fa-trash"></i>
                                Hapus File Excel</a>
                        @elseif(!$id_standar)
                            <a type="button" class="btn btn-sm btn-primary" href="" data-toggle="modal"
                                data-target="#importModal"><i class="fas fa-file-upload"></i> Import File Excel</a>
                        @endif
                    @endif
                </div>
            @endcan

            @cannot('koorprodi')
                <div class="@if ($file) col-auto text-left @else ml-3 @endif">
                    <a type="button" class="btn btn-sm btn-secondary" href="{{ route('ks_home') }}">
                        <i class="fa fa-sm fa-arrow-left" aria-hidden="true"></i> Kembali
                    </a>
                </div>
            @endcannot
        </div>

        @if ($headers)
            @for ($i = 0; $i < count($sheetName) - 2; $i++)
                <div class="element">
                    <span class="text-muted">{{ $file->prodi->nama_prodi }} / {{ $file->tahun }} @can('koorprodi')
                            /
                            <a href="" data-toggle="modal" data-target="#tahapModal">{{ $file->status->keterangan }}</a>
                        @endcan </span>
                    <table class="table mt-2 text-left">
                        <thead>
                            <th colspan="8" class="py-1">
                                <h5>{{ $sheetName[$i] }}</h5>
                            </th>
                            <tr>
                                @foreach ($headers[$i] as $header)
                                    @if ($header && $header != 'Satuan')
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
                                        @foreach (range('H', 'I') as $v)
                                            <td> {{ $sheet[$v] }} </td>
                                        @endforeach
                                        <td>
                                            <a href="{{ $sheet['J'] }}">
                                                {{ strip_tags(\Illuminate\Support\Str::limit($sheet['J'], 15, '...')) }}
                                            </a>
                                        </td>
                                        @if (array_key_exists('K', $sheet))
                                            <td>
                                                {{ $sheet['K'] }}
                                            </td>
                                        @endif
                                        @if (array_key_exists('L', $sheet))
                                            <td>
                                                {{ $sheet['L'] }}
                                            </td>
                                        @endif
                                        @if (array_key_exists('M', $sheet))
                                            <td>
                                                {{ $sheet['M'] }}
                                            </td>
                                        @endif
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endfor
        @else
            <div class="my-3 text-center element">
                <h5>Koorprodi belum mengunggah file ketercapaian standar tahun {{ date('Y') }}</h5>
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

    @can('koorprodi')
        <div class="modal fade" id="importModal" role="dialog" arialabelledby="modalLabel" area-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('ks_import_action') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <h5 class="pb-3">Silahkan unggah file ketercapaian standar tahun {{ date('Y') }}</h5>
                            <input class="form-control form-control-sm" type="file" name="file">
                            <input type="text" name="prodi" value="{{ Auth::user()->user_access_file[0]->prodi_id }}"
                                hidden>
                            <input type="text" name="tahun" value="{{ $file->tahun ?? date('Y') }}" hidden>
                            <input type="text" name="id" value="{{ $file->id ?? '' }}" hidden>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                            <input class="btn btn-sm btn-primary" type="submit" value="Unggah file">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if ($file)
            <div class="modal fade" id="tahapModal" role="dialog" arialabelledby="modalLabel" area-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="row pl-5">
                            @include('layouts.tahap_breadcrumb')
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan
</body>

</html>
