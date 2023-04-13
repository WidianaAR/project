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
            <span class="text-muted">Feedback / evaluasi diri / <a href="">{{ $data->prodi->nama_prodi }}
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
        <a href="{{ route('feedback') }}" type="button" class="btn btn-danger"><i class="fa fa-arrow-left"
                aria-hidden="true"></i>Kembali</a>
    </div>
    <form action="{{ route('fb_ed_table_save') }}" method="POST">
        @csrf
        <div class="text-center element">
            <table class="table table-bordered" style="table-layout: fixed">
                @foreach ($sheetData as $sheet)
                    <tr>
                        @if (!$sheet[3])
                            <td id="title" colspan="@if ($temuan) 10 @else 9 @endif">
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
                            @if ($temuan)
                                <td>
                                    @if ($sheet[9])
                                        {{ $sheet[9] }}
                                    @endif
                                </td>
                            @endif
                            @if ($sheet[0] == 'No')
                                <td id="column" hidden>Temuan @if ($temuan)
                                        baru
                                    @endif
                                </td>
                            @else
                                <td id="cell" hidden>
                                    @if ($sheet[1])
                                        <textarea name="temuan[]"></textarea>
                                    @endif
                                </td>
                            @endif
                        @endif
                    </tr>
                @endforeach
            </table>
            <input type="text" name="id" value="{{ $data->id }}" hidden>
            <div class="text-right">
                <button id="simpan" type="submit" class="btn btn-primary" hidden>Simpan temuan</button>
            </div>
        </div>
    </form>

    <script>
        function showTemuan() {
            var colspan = parseInt($('#title').attr('colspan'));
            $("#column, #cell, #simpan").removeAttr("hidden");
            $("#tambah").attr("hidden", "hidden");
            $('#title').attr('colspan', colspan + 1);
        }
    </script>
@endsection
