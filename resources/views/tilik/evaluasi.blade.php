@extends('layouts.navbar')

@section('isi')
    @if (Session::has('success'))
        <div class="alert alert-success" role="alert" id="msg-box">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ Session::get('success') }}
        </div>
    @endif

    <div class="row m-0 align-items-center">
        <div class="col pl-1">
            <span class="text-muted">Daftar tilik / Evaluasi / <a href="">{{ $data->prodi->nama_prodi }}
                    {{ $data->tahun }}</a></span>
        </div>
        @can('auditor')
            <button id="tambah" type="button" class="btn btn-sm btn-primary mr-2" onclick="showTilik()">
                @if ($data->status_id == 3)
                    Ubah
                @else
                    Tambah
                @endif tilik
            </button>
        @endcan
        @cannot('auditor')
            <div class="col text-right px-2">
                <form action="{{ route('ed_export_file') }}" method="POST">
                    @csrf
                    <input name="filename" type="hidden" value="{{ $data->file_data }}">
                    <input type="submit" class="btn btn-sm btn-primary" value="Export file">
                </form>
            </div>
        @endcan
        <a href="@if (Auth::user()->role_id == 4) {{ route('tilik_home_auditor') }} @else {{ route('tilik_home') }} @endif"
            type="button" class="btn btn-sm btn-secondary"><i class="fa fa-arrow-left" aria-hidden="true"></i>Kembali</a>
    </div>
    <form action="{{ route('tilik_ed_table_save') }}" method="POST">
        @csrf
        <div class="element">
            <table class="table table-bordered">
                @foreach ($sheetData as $sheet)
                    <tr>
                        @if (!$sheet[3])
                            <th id="title" colspan="10">
                                {{ $sheet[0] }} </th>
                        @else
                            @foreach (range(0, 4) as $v)
                                <td> {{ $sheet[$v] }} </td>
                            @endforeach
                            <td>
                                @if (\Illuminate\Support\Facades\URL::isValidUrl($sheet[8]))
                                    <a href="{{ $sheet[8] }}">
                                        {{ strip_tags(\Illuminate\Support\Str::limit($sheet[8], 15, '...')) }}
                                    </a>
                                @else
                                    {{ $sheet[8] }}
                                @endif
                            </td>
                            @if ($data->status_id == 3)
                                <td>
                                    @if ($sheet[9])
                                        {{ $sheet[9] }}
                                    @endif
                                </td>
                            @endif
                            @if ($sheet[0] == 'No')
                                <td class="col-2" id="column" hidden>Tilik @if ($data->status_id == 3)
                                        baru
                                    @endif
                                </td>
                            @else
                                <td id="cell" hidden>
                                    @if ($sheet[1])
                                        <textarea name="tilik[]">{{ $sheet[9] ?? '' }}</textarea>
                                    @endif
                                </td>
                            @endif
                        @endif
                    </tr>
                @endforeach
            </table>
            <input type="text" name="id" value="{{ $data->id }}" hidden>
            <div class="text-right">
                <button id="simpan" type="submit" class="btn btn-sm btn-primary" hidden>Simpan tilik</button>
            </div>
        </div>
    </form>

    <script>
        function showTilik() {
            $("#column, #cell, #simpan").removeAttr("hidden");
            $("#tambah").attr("hidden", "hidden");
        }
    </script>
@endsection
