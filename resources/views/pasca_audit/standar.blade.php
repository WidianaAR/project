@extends('layouts.navbar')

@section('title')
    <title>Pasca Audit</title>
@endsection

@section('isi')
    @if (Session::has('success'))
        <div class="alert alert-success" role="alert" id="msg-box">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ Session::get('success') }}
        </div>
    @endif

    <div class="row m-0 align-items-center">
        <div class="col pl-1">
            <span class="text-muted">Daftar komentar / Audit mutu internal / <a href="">{{ $data->prodi->nama_prodi }}
                    {{ $data->tahun }}</a></span>
        </div>
        @if ($data->status_id != 7)
            <button id="tambah" class="btn btn-sm btn-primary mr-2" onclick="showKomentar()">
                @if ($data->status_id == 4 || $data->status_id == 6)
                    Ubah
                @else
                    Tambah
                @endif komentar
            </button>

            <button id="tambah_nilai" class="btn btn-sm btn-primary mr-2" onclick="showNilai()">
                @if ($data->status_id == 5 || $data->status_id == 6)
                    Ubah
                @else
                    Tambah
                @endif Nilai
            </button>
        @endif

        <a href="{{ route('pasca_confirm', $data->id) }}" id="konfirmasi" class="btn btn-sm btn-success mr-2"
            onclick="return confirm('Data yang sudah dikonfirmasi akan tampil pada grafik. Yakin konfirmasi data?');"
            @if ($data->status_id != 6) hidden @endif> Konfirmasi
        </a>

        <a href="{{ route('pasca_cancel_confirm', $data->id) }}" id="konfirmasi" class="btn btn-sm btn-success mr-2"
            @if ($data->status_id != 7) hidden @endif> Batal konfirmasi
        </a>

        <a href="{{ route('pasca_home') }}" type="button" class="btn btn-sm btn-secondary"> <i
                class="fa fa-sm fa-arrow-left" aria-hidden="true"></i>Kembali</a>
    </div>

    <form action="{{ route('pasca_ks_table_save') }}" method="POST">
        @csrf
        <div class="element">
            @for ($i = 0; $i < $sheetCount - 2; $i++)
                <h6>{{ $sheetName[$i] }}</h6>
                <table class="table table-bordered">
                    <thead class="thead">
                        <tr>
                            @foreach ($headers[$i] as $header)
                                @if ($header && $header != 'Satuan' && $header != 'Status Ketercapaian Standar')
                                    <th>{{ $header }}</th>
                                @endif
                            @endforeach
                            <th class="col-2" id="column" hidden>Komentar @if ($data->status_id == 4)
                                    baru
                                @endif
                            </th>
                            <th class="col-1" id="column_nilai" hidden>Nilai @if ($data->status_id == 5)
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
                                    <td> {{ $sheet['H'] }} </td>
                                    <td>
                                        <a href="{{ $sheet['J'] }}">
                                            {{ strip_tags(\Illuminate\Support\Str::limit($sheet['J'], 5, '...')) }}
                                        </a>
                                    </td>
                                    <td>{{ $sheet['K'] }}</td>
                                    @if ($data->status_id == 4)
                                        <td>
                                            @if (array_key_exists('L', $sheet))
                                                {{ $sheet['L'] }}
                                            @endif
                                        </td>
                                    @elseif ($data->status_id == 5)
                                        <td>
                                            @if (array_key_exists('M', $sheet))
                                                {{ $sheet['M'] }}
                                            @endif
                                        </td>
                                    @elseif ($data->status_id == 6 || $data->status_id == 7)
                                        <td>
                                            @if (array_key_exists('L', $sheet))
                                                {{ $sheet['L'] }}
                                            @endif
                                        </td>
                                        <td>
                                            @if (array_key_exists('M', $sheet))
                                                {{ $sheet['M'] }}
                                            @endif
                                        </td>
                                    @endif

                                    <td id="cell" hidden>
                                        <textarea name="{{ $i }}komentar[]">{{ $sheet['L'] ?? '' }}</textarea>
                                    </td>
                                    <td id="cell_nilai" hidden>
                                        <textarea name="{{ $i }}nilai[]">{{ $sheet['M'] ?? '' }}</textarea>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @endfor
            <input type="text" id="kategori" name="kategori" hidden>
            <input type="text" name="id" value="{{ $data->id }}" hidden>
            <div class="text-right">
                <button id="simpan" type="submit" class="btn btn-sm btn-primary" hidden>Simpan</button>
            </div>
        </div>
    </form>

    <script>
        function showKomentar() {
            $("#column, #cell, #simpan").removeAttr("hidden");
            $("#tambah").attr("hidden", "hidden");

            var nilaiAwal = $("#kategori").val();
            $("#kategori").val(nilaiAwal + "komentar");
        }

        function showNilai() {
            $("#column_nilai, #cell_nilai, #simpan").removeAttr("hidden");
            $("#tambah_nilai").attr("hidden", "hidden");

            var nilaiAwal = $("#kategori").val();
            $("#kategori").val(nilaiAwal + "nilai");
        }
    </script>
@endsection
