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
            <span class="text-muted">Daftar pasca / Simulasi akreditasi / <a href="">{{ $data->prodi->nama_prodi }}
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

        <a href="{{ route('pasca_home') }}" class="btn btn-sm btn-secondary"><i class="fa fa-sm fa-arrow-left"
                aria-hidden="true"></i>Kembali</a>
    </div>
    <form action="{{ route('pasca_ed_table_save') }}" method="POST">
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
                            <td>{{ $sheet[9] }}</td>
                            @if ($data->status_id == 4)
                                <td>
                                    @if ($sheet[10])
                                        {{ $sheet[10] }}
                                    @endif
                                </td>
                            @elseif ($data->status_id == 5)
                                <td>
                                    @if ($sheet[11])
                                        {{ $sheet[11] }}
                                    @endif
                                </td>
                            @elseif ($data->status_id == 6 || $data->status_id == 7)
                                <td>
                                    @if ($sheet[10])
                                        {{ $sheet[10] }}
                                    @endif
                                </td>
                                <td>
                                    @if ($sheet[11])
                                        {{ $sheet[11] }}
                                    @endif
                                </td>
                            @endif

                            @if ($sheet[0] == 'No')
                                <td class="col-2" id="column" hidden>Komentar @if ($data->status_id == 4)
                                        baru
                                    @endif
                                </td>
                                <td class="col-1" id="column_nilai" hidden>Nilai @if ($data->status_id == 5)
                                        baru
                                    @endif
                                </td>
                            @else
                                <td id="cell" hidden>
                                    @if ($sheet[1])
                                        <textarea>{{ $sheet[10] ?? '' }}</textarea>
                                    @endif
                                </td>
                                <td id="cell_nilai" hidden>
                                    @if ($sheet[1])
                                        <textarea>{{ $sheet[11] ?? '' }}</textarea>
                                    @endif
                                </td>
                            @endif
                        @endif
                    </tr>
                @endforeach
            </table>
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
            $('#cell textarea').attr('name', 'komentar[]');
        }

        function showNilai() {
            $("#column_nilai, #cell_nilai, #simpan").removeAttr("hidden");
            $("#tambah_nilai").attr("hidden", "hidden");
            $('#cell_nilai textarea').attr('name', 'nilai[]');
        }
    </script>
@endsection
