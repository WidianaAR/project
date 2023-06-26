<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulasi Akreditasi</title>
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
                    <h5>Simulasi Akreditasi</h5>
                </div>
            @endif

            @can('koorprodi')
                @if ($years)
                    <div class="col-auto text-right box mx-2">
                        <button class="simple" type="button" data-toggle="dropdown" aria-expanded="false">
                            Tahun <i class='fa fa-angle-down fa-sm'></i>
                        </button>
                        <div class="dropdown-menu">
                            @foreach ($years as $year)
                                <a class="dropdown-item" href="{{ route('ed_filter_year', $year) }}">{{ $year }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endcanany

            @if ($file)
                <div class="col-auto text-right p-0">
                    <form action="{{ route('ed_export_file') }}" method="POST">
                        @csrf
                        <input name="filename" type="hidden" value="{{ $file->file_data }}">
                        <input type="submit" class="btn btn-sm btn-primary" value="Export File">
                    </form>
                </div>
            @endif

            @can('koorprodi')
                <div class="col-auto text-left @if (($file && $file->tahun != date('Y')) || !$deadline[0]) pl-0 @else pl-1 @endif">
                    @if ($deadline[0])
                        @if ($id_evaluasi && $file && $file->tahun == date('Y'))
                            <a type="button" class="btn btn-sm btn-success" href="" data-toggle="modal"
                                data-target="#importModal"><i class="fa fa-sm fa-file-upload"></i> Ganti File Excel</a>
                            <a type="button" class="btn btn-sm btn-danger" href="{{ route('ed_delete', $id_evaluasi) }}"
                                onclick="return confirm('Apakah Anda yakin menghapus file?');"><i class="fa fa-sm fa-trash"></i>
                                Hapus File Excel</a>
                        @elseif(!$id_evaluasi)
                            <a type="button" class="btn btn-sm btn-primary" href="" data-toggle="modal"
                                data-target="#importModal"><i class="fa fa-sm fa-file-upload"></i> Import File Excel</a>
                        @endif
                    @endif
                </div>
            @endcan

            @cannot('koorprodi')
                <div class="col-auto text-left pl-1">
                    <a type="button" class="btn btn-sm btn-secondary" href="{{ route('ed_home') }}">
                        <i class="fa fa-sm fa-arrow-left" aria-hidden="true"></i> Kembali
                    </a>
                </div>
            @endcannot
        </div>

        @if ($sheetData)
            <div class="element">
                <span class="text-muted">{{ $file->prodi->nama_prodi }} / {{ $file->tahun }}@can('koorprodi')
                    /
                    <a href="" data-toggle="modal" data-target="#tahapModal">{{ $file->status->keterangan }}</a>
                @endcannot
            </span>
            <table class="table mt-3 text-left">
                @foreach ($sheetData as $sheet)
                    <tr>
                        @if (!$sheet[3])
                            <th id="title" colspan="9">
                                {{ $sheet[0] }} </th>
                        @else
                            @foreach (range(0, 4) as $v)
                                @if ($sheet[$v] != 'Total')
                                    <td> {{ $sheet[$v] }} </td>
                                @endif
                            @endforeach
                            <td>
                                @if (\Illuminate\Support\Facades\URL::isValidUrl($sheet[8]))
                                    <a href="{{ $sheet[8] }}" target="_blank">
                                        {{ strip_tags(\Illuminate\Support\Str::limit($sheet[8], 20, '...')) }}
                                    </a>
                                @else
                                    {{ $sheet[8] }}
                                @endif
                            </td>
                            @if ($sheet[9])
                                <td>{{ $sheet[9] }}</td>
                            @endif
                            @if ($sheet[10])
                                <td>{{ $sheet[10] }}</td>
                            @endif
                            @if ($sheet[11])
                                <td>{{ $sheet[11] }}</td>
                            @endif
                        @endif
                    </tr>
                @endforeach
            </table>
        </div>
    @else
        <div class="my-3 text-center element">
            <h5>Koorprodi belum mengunggah instrumen Simulasi Akreditasi tahun {{ date('Y') }}</h5>
        </div>
    @endif
    </div>
@endsection

@can('koorprodi')
    <div class="modal fade" id="importModal" role="dialog" arialabelledby="modalLabel" area-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('ed_import_action') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <h5 class="pb-3">Silahkan unggah instrumen Simulasi Akreditasi tahun {{ date('Y') }}</h5>
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
