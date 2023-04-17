<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluasi Diri</title>
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
                    @include('evaluasi_diri/countdown')
                </div>
            @else
                <div class="col">
                    <h5>Evaluasi Diri</h5>
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
                                <a class="dropdown-item" href="{{ route('ed_filter_year', $year) }}">{{ $year }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endcanany

            @can('pjm')
                <div class="col text-right p-0">
                    <form action="{{ route('ed_export_file') }}" method="POST">
                        @csrf
                        <input name="filename" type="hidden" value="{{ $data->file_data }}">
                        <input type="submit" class="btn btn-primary" value="Export File">
                    </form>
                </div>
            @endcan

            {{-- Data --}}
            @can('koorprodi')
                @if ($deadline[0])
                    <div class="col-auto text-left pl-1">
                        @if ($id_evaluasi)
                            <a type="button" class="btn btn-danger" href="{{ route('ed_delete', $id_evaluasi) }}"
                                onclick="return confirm('Apakah Anda yakin menghapus file?');"><i class="fas fa-trash"></i>
                                Hapus File Excel</a>
                            <a type="button" class="btn btn-primary" href="" data-toggle="modal"
                                data-target="#importModal"><i class="fas fa-file-upload"></i> Ganti File Excel</a>
                        @else
                            <a type="button" class="btn btn-primary" href="" data-toggle="modal"
                                data-target="#importModal"><i class="fas fa-file-upload"></i> Import File Excel</a>
                        @endif
                    </div>
                @endif
            @endcan

            @cannot('koorprodi')
                <div class="@if ($data) col-auto text-left @else ml-3 @endif">
                    @can('auditor')
                        @if ($data)
                            @if ($data->status == 'ditinjau' && $deadline[0])
                                <a type="button" class="btn btn-secondary" href="" data-toggle="modal"
                                    data-target="#feedbackModal">
                                    Perlu Perbaikan
                                </a>
                                <a type="button" class="btn btn-success" href="{{ route('ed_confirm', $id_evaluasi) }}"
                                    onclick="return confirm('Apakah Anda yakin menyetujui data ini? Data yang sudah disetujui akan disimpan ke dalam statistik');">
                                    Konfirmasi
                                </a>
                            @elseif ($data->status == 'disetujui')
                                <a type="button" class="btn btn-secondary" href="{{ route('ed_cancel_confirm', $id_evaluasi) }}"
                                    onclick="return confirm('Apakah Anda yakin membatalkan data ini? Data yang sudah dibatalkan akan dihapus dari statistik');">
                                    Batal Setujui
                                </a>
                            @endif
                            <a type="button" class="btn btn-primary" href="" data-toggle="modal" data-target="#importModal">
                                <i class="fas fa-file-upload"></i> Ganti File Excel
                            </a>
                        @endif
                    @else
                        <a type="button" class="btn btn-danger" href="{{ route('ed_home') }}">
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
        @if ($sheetData)
            <div class="element text-right">
                <span class="text-muted">{{ $data->prodi->nama_prodi }} / <a href="">{{ $data->tahun }}</a></span>
                <table class="table table-bordered mt-3">
                    @foreach ($sheetData as $sheet)
                        <tr>
                            @if (!$sheet[3])
                                <td id="title" colspan="9">
                                    {{ $sheet[0] }} </td>
                            @else
                                @foreach (range(0, 7) as $v)
                                    <td> {{ $sheet[$v] }} </td>
                                @endforeach
                                <td>
                                    @if (\Illuminate\Support\Facades\URL::isValidUrl($sheet[8]))
                                        <a href="{{ $sheet[8] }}">
                                            {{ strip_tags(\Illuminate\Support\Str::limit($sheet[8], 7, '...')) }}
                                        </a>
                                    @else
                                        {{ $sheet[8] }}
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </table>
            </div>
        @else
            <div class="my-3 text-center element">
                <h5>Koorprodi belum memasukkan data evaluasi diri tahun {{ date('Y') }}</h5>
            </div>
        @endif
        </div>

        @can('pjm')
            <div class="floating-action-button">
                <a type="button" href="{{ route('ed_set_time') }}" class="btn"><i class='fa fa-clock fa-2x'
                        style='color: #0D64AC'></i></a>
            </div>
        @endcan
    @endsection

    <div class="modal fade" id="importModal" role="dialog" arialabelledby="modalLabel" area-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('ed_import_action') }}" method="POST" enctype="multipart/form-data">
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
                <form action="{{ route('ed_feedback') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="text" name="id_evaluasi" value="{{ $id_evaluasi }}" hidden>
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
