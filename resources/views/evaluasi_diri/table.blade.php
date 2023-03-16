<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Evaluasi Diri</title>
        <link rel="stylesheet" href="{{ URL::Asset('css/app.css') }}">
    </head>

    <body>
        @extends('layouts.navbar')
            
        @section('top-navbar')
            @if (Auth::user()->role_id == 3)
                @if (!!$years)
                <li class="nav-item dropdown">
                    <a class="dropdown-toggle nav-link" style="padding-right: 6vh" href="#" id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
                        Pilih Tahun
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        @foreach ($years as $year)
                            <a class="dropdown-item" href="{{ route('ed_filter_year', $year) }}">{{$year}}</a>
                        @endforeach
                    </div>
                </li>
                @endif
            @endif
            @if (Auth::user()->role_id == 1)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('ed_set_time') }}">Set Batas Waktu Pengisian</a>
            </li>
            @endif
        @endsection

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

            <div class="row align-items-start">
                {{-- Countdown Time --}}
                <div class="col">
                    @include('evaluasi_diri/countdown')
                </div>

                @if (Auth::user()->role_id == 1)
                <div class="col text-right p-0">
                    <form action="{{ route('ed_export_file') }}" method="POST">
                        @csrf
                        <input name="filename" type="hidden" value="{{ $data->file_data }}">
                        <input type="submit" class="btn btn-primary" value="Export File">
                    </form>
                </div>
                @endif

                {{-- Data --}}
                @if (Auth::user()->role_id == 3)
                    @if (!!$deadline[0])
                    <div class="col text-right">
                        @if (!!$id_evaluasi)
                            <a type="button" class="btn btn-danger" href="{{ route('ed_delete', $id_evaluasi) }}" onclick="return confirm('Apakah Anda yakin menghapus file?');"><i class="fas fa-trash"></i> Hapus File Excel</a>
                            <a type="button" class="btn btn-primary" href="" data-toggle="modal" data-target="#importModal"><i class="fas fa-file-upload"></i> Ganti File Excel</a>
                        @else
                            <a type="button" class="btn btn-primary" href="" data-toggle="modal" data-target="#importModal"><i class="fas fa-file-upload"></i> Import File Excel</a>
                        @endif
                    </div>
                    @endif
                @endif
                @if (Auth::user()->role_id != 3)
                    <div class="col-auto text-left">
                        @if (Auth::user()->role_id == 4)
                            @if ($data->status == 'ditinjau')
                                <a type="button" class="btn btn-primary" href="" data-toggle="modal" data-target="#feedbackModal"> Perlu Perbaikan</a>
                                <a type="button" class="btn btn-success" href="{{ route('ed_confirm', $id_evaluasi) }}" onclick="return confirm('Apakah Anda yakin menyetujui data ini? Data yang sudah disetujui akan disimpan ke dalam statistik');"> Konfirmasi</a>
                            @elseif ($data->status == 'disetujui')
                                <a type="button" class="btn btn-primary" href="{{ route('ed_cancel_confirm', $id_evaluasi) }}" onclick="return confirm('Apakah Anda yakin membatalkan data ini? Data yang sudah dibatalkan akan dihapus dari statistik');"> Batal Setujui</a>
                            @endif
                            <a type="button" class="btn btn-primary" href="" data-toggle="modal" data-target="#importModal"><i class="fas fa-file-upload"></i> Ganti File Excel</a>
                        @endif
                        <a type="button" class="btn btn-danger" href="{{route('ed_home')}}"><i class="fa fa-arrow-left" aria-hidden="true"></i> Kembali</a>
                    </div>
                @endif
                
                @if (!!$data and !!$data->keterangan)
                <div class="row m-3" style="width: 100%">
                    <span class="border border-danger p-2" style="display: inline-block; width: 100%">
                        <b>Yang perlu diperbaiki:</b>
                        <br>
                        {{$data->keterangan}}
                    </span>
                </div>
                @endif
                
                {{-- Table --}}
                @if (!! $sheetData)
                    <div class="m-3 text-center">
                        <table class="table table-bordered">
                            @foreach ($sheetData as $sheet)
                                <tr>
                                @if (!!! $sheet[3])
                                    <td colspan="9"> {{ $sheet[0] }} </td>
                                @else
                                    @foreach(range(0, 8) as $v)
                                        <td> {{ $sheet[$v] }} </td>
                                    @endforeach
                                @endif
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif
            </div>
        @endsection
        
        <div class="modal fade" id="importModal" role="dialog" arialabelledby="modalLabel" area-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('ed_import_action') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <h2 >Pilih File</h2>
                            <input type="file" name="file">
                            @if (!!Auth::user()->prodi_id)
                                <input type="text" name="prodi" value="{{Auth::user()->prodi_id}}" hidden>
                                <input type="text" name="jurusan" value="{{Auth::user()->jurusan_id}}" hidden>
                            @else
                                <input type="text" name="prodi" value="{{$data->prodi_id}}" hidden>
                                <input type="text" name="jurusan" value="{{$data->jurusan_id}}" hidden>
                            @endif
                            <input type="text" name="tahun" value="{{ date('Y') }}" hidden>
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
                            <input type="text" name="id_evaluasi" value="{{$id_evaluasi}}" hidden>
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