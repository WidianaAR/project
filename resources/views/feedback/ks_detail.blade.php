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
            <span class="text-muted">Feedback / ketercapaian standar / <a href="">{{ $data->prodi->nama_prodi }}
                    {{ $data->tahun }}</a></span>
        </div>
        @can('auditor')
            <button id="tambah" type="button" class="btn btn-success mr-2" onclick="showTemuan()">
                @if ($temuan)
                    Ubah
                @else
                    Tambah
                @endif temuan
            </button>
        @endcan
        <a href="{{ route('feedback', 'standar') }}" type="button" class="btn btn-danger"><i class="fa fa-arrow-left"
                aria-hidden="true"></i>Kembali</a>
    </div>

    <form action="{{ route('fb_ks_table_save') }}" method="POST">
        @csrf
        <div class="text-center element">
            @for ($i = 0; $i <= 3; $i++)
                <h5>{{ $sheetName[$i] }}</h5>
                <table class="table table-bordered" style="table-layout: fixed">
                    <thead class="thead">
                        <tr>
                            @foreach ($headers[$i] as $header)
                                @if ($header)
                                    <th>{{ $header }}</th>
                                @endif
                            @endforeach
                            <th id="column" hidden>Temuan @if ($temuan)
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
                                    @foreach (range('G', 'I') as $v)
                                        <td> {{ $sheet[$v] }} </td>
                                    @endforeach
                                    <td>
                                        <a href="{{ $sheet['J'] }}">
                                            {{ strip_tags(\Illuminate\Support\Str::limit($sheet['J'], 5, '...')) }}
                                        </a>
                                    </td>
                                    @if ($temuan)
                                        <td>
                                            @if (array_key_exists('K', $sheet))
                                                {{ $sheet['K'] }}
                                            @endif
                                        </td>
                                    @endif
                                    <td id="cell" hidden>
                                        <textarea name="{{ $i }}temuan[]"></textarea>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @endfor
            <input type="text" name="id" value="{{ $data->id }}" hidden>
            <div class="text-right">
                <button id="simpan" type="submit" class="btn btn-primary" hidden>Simpan temuan</button>
            </div>
        </div>
    </form>

    <script>
        function showTemuan() {
            $("#column, #cell, #simpan").removeAttr("hidden");
            $("#tambah").attr("hidden", "hidden");
        }
    </script>
@endsection
