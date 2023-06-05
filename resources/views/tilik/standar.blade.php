<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tilik</title>
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

        <div class="row m-0 align-items-center">
            <div class="col pl-1">
                <span class="text-muted">Daftar tilik / Standar / <a href="">{{ $data->prodi->nama_prodi }}
                        {{ $data->tahun }}</a></span>
            </div>
            <button id="tambah" type="button" class="btn btn-sm btn-primary mr-2" onclick="showTilik()">
                @if ($data->status_id == 3)
                    Ubah
                @else
                    Tambah
                @endif tilik
            </button>

            <a type="button" class="btn btn-sm btn-success mr-2" href="" data-toggle="modal"
                data-target="#importModal"><i class="fas fa-file-upload"></i> Ganti File Excel</a>

            <a href="{{ route('tilik_home') }}" type="button" class="btn btn-sm btn-secondary"> <i
                    class="fa fa-sm fa-arrow-left" aria-hidden="true"></i>Kembali</a>
        </div>

        <form action="{{ route('tilik_ks_table_save') }}" method="POST">
            @csrf
            <div class="element">
                @for ($i = 0; $i < $sheetCount - 2; $i++)
                    <table class="table table-bordered">
                        <thead class="thead">
                            <th colspan="8" class="py-1">
                                <h5>{{ $sheetName[$i] }}</h5>
                            </th>
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
                                        @if (array_key_exists('K', $sheet))
                                            <td>
                                                {{ $sheet['K'] }}
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

    <div class="modal fade" id="importModal" role="dialog" arialabelledby="modalLabel" area-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('tilik_change') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <h5 class="pb-3">Silahkan unggah file ketercapaian standar</h5>
                        <input class="form-control form-control-sm" type="file" name="file">
                        <input type="text" name="kategori" value="standar" hidden>
                        <input type="text" name="prodi" value="{{ $data->prodi_id }}" hidden>
                        <input type="text" name="tahun" value="{{ $data->tahun }}" hidden>
                        <input type="text" name="id" value="{{ $data->id }}" hidden>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                        <input class="btn btn-sm btn-primary" type="submit" value="Unggah file">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
