<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ketercapaian Standar</title>
        <link rel="stylesheet" href="{{ URL::Asset('css/app.css') }}">
    </head>

    <body>
        @extends('layouts.navbar')
            
        @section('top-navbar')
            @if (!!$years)
            <li class="nav-item dropdown">
                <a class="dropdown-toggle nav-link" style="padding-right: 6vh" href="#" id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
                    Pilih Tahun
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="{{route('ks_home')}}">All</a>
                    @foreach ($years as $year)
                        <a class="dropdown-item" href="{{ route('ks_filter_year', $year) }}">{{$year}}</a>
                    @endforeach
                </div>
            </li>
            @endif
            @if (Auth::user()->role_id == 1)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('ks_set_time') }}">Set Batas Waktu Pengisian</a>
            </li>
            @endif
        @endsection

        @section('isi')
        {{-- Countdown Time --}}
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
                <div class="col">
                    @include('ketercapaian_standar/countdown')
                </div>

                <div class="col text-right">
                    <h6>Filter By</h6>
                </div>

                @if (Auth::user()->role_id != 2)
                <div class="col-auto text-left">
                    <div class="dropdown">
                        <button class="btn" type="button" data-toggle="dropdown" aria-expanded="false">
                            Jurusan <i class='fa fa-angle-down'></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="moduleDropDown">
                            @foreach ($jurusans as $jurusan)
                                <a class="dropdown-item" href="{{ route('ks_filter_jurusan', $jurusan->jurusan_id) }}">{{$jurusan->nama_jurusan}}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <div class="col-auto text-left">
                    <div class="dropdown">
                        <button class="btn" type="button" data-toggle="dropdown" aria-expanded="false">
                            Prodi <i class='fa fa-angle-down'></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="moduleDropDown">
                            @foreach ($prodis as $prodi)
                                <a class="dropdown-item" href="{{ route('ks_filter_prodi', $prodi->prodi_id) }}">{{$prodi->nama_prodi}}</a>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if (Auth::user()->role_id == 1)
                <div class="col-auto text-left">
                    <form action="{{ route('ks_export_all') }}" method="POST">
                        @csrf
                        @foreach($data as $file)
                            <input name="data[]" type="hidden" value="{{ $file->file_data }}">
                        @endforeach
                        <input type="submit" class="btn btn-primary" value="Export All File">
                    </form>
                </div>
                @endif

                @if (Auth::user()->role_id == 2)
                    @if ($deadline[0] != null)
                    <div class="floating-action-button">
                        <a type="button" href="{{route('ks_import')}}" class="btn"><i class='fa fa-plus-circle fa-2x' style='color: #0D64AC'></i></a>
                    </div>
                    @endif
                @endif
            </div>
                
            <div class="mt-3 text-center">
                @if (!!$data)
                <table class="table table-bordered">
                    <tr>
                        <th>Nama File</th>
                        @if (Auth::user()->role_id != 2)
                            <th>Jurusan</th>
                        @endif
                        <th>Program Studi</th>
                        <th>Size</th>
                        <th>Tahun</th>
                        <th>Status</th>
                        @if (Auth::user()->role_id == 2)
                            <th>Action</th>
                        @endif
                    </tr>
                    @foreach ($data as $file)
                        <tr>
                            <td><a href="{{ route('ks_table', $file->id_standar) }}">{{$file->file_data}}</a></td>
                            @if (Auth::user()->role_id != 2)
                                <td>{{$file->nama_jurusan}}</td>
                            @endif
                            <td>{{$file->nama_prodi}}</td>
                            <td>{{$file->size}} byte</td>
                            <td>{{$file->tahun}}</td>
                            <td>{{$file->status}}</td>
                            @if (Auth::user()->role_id == 2)
                            <td>
                                <a type="button" class="btn btn-success" href="{{ route('ks_ubah', $file->id_standar) }}"><i class="fa fa-edit"></i></a>
                                <a type="button" href="{{ route('ks_delete', $file->id_standar) }}" onclick="return confirm('Apakah Anda Yakin Menghapus Data?');" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                            </td>
                            @endif
                        </tr>
                    @endforeach
                </table>
                @else
                <h4>Data Kosong</h4>
                @endif
            </div>
        @endsection
    </body>
</html>