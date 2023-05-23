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
            <span class="text-muted">Daftar tilik / Standar / <a href="">{{ $data->prodi->nama_prodi }}
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
            type="button" class="btn btn-sm btn-secondary"> <i class="fa fa-arrow-left" aria-hidden="true"></i>Kembali</a>
    </div>

    <form action="{{ route('tilik_ks_table_save') }}" method="POST">
        @csrf
        <div class="element">
            @for ($i = 0; $i < $sheetCount - 2; $i++)
                <h5>{{ $sheetName[$i] }}</h5>
                <table class="table table-bordered">
                    <thead class="thead">
                        <tr>
                            @foreach ($headers[$i] as $header)
                                @if ($header && $header != 'Satuan')
                                    <th>{{ $header }}</th>
                                @endif
                            @endforeach
                            <th class="col-2" id="column" hidden>Tilik @if ($data->status_id == 3)
                                    baru
                                @endif
                            </th>
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
                                            {{ strip_tags(\Illuminate\Support\Str::limit($sheet['J'], 5, '...')) }}
                                        </a>
                                    </td>
                                    @if ($data->status_id == 3)
                                        <td>
                                            @if (array_key_exists('K', $sheet))
                                                {{ $sheet['K'] }}
                                            @endif
                                        </td>
                                    @endif
                                    <td id="cell" hidden>
                                        <textarea name="{{ $i }}tilik[]">{{ $sheet['K'] ?? '' }}</textarea>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @endfor
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
