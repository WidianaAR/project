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
            <li class="nav-item">
                <a class="nav-link">Ubah Data Evaluasi Diri</a>
            </li>
        @endsection

        @section('isi')
        {{-- Countdown Time --}}
            <div>
                @include('evaluasi_diri/countdown')
            </div>

            <div class="add-form">
                <form action="{{ route('ed_change_action') }}" method="POST" enctype="multipart/form-data">
                @csrf
                    <input type="text" name="id_evaluasi" value="{{$data->id}}" hidden>
                    <input type="file" name="file" class="form-control" aria-describedby="inputGroup-sizing-default" value="{{$data->file_data}}">
                    <select name='prodi' class="custom-select" id="inputGroupSelect03">
                        @foreach ($prodis as $prodi)
                            @if ($prodi->id == $data->prodi_id)
                                <option value="{{$prodi->id}}" selected>{{$prodi->nama_prodi}}</option>
                            @else
                                <option value="{{$prodi->id}}">{{$prodi->nama_prodi}}</option>
                            @endif
                        @endforeach
                    </select>
                    <input type="text" name="jurusan" class="form-control" value="{{Auth::user()->jurusan_id}}" aria-describedby="inputGroup-sizing-default" hidden>
                    <input type="text" name="tahun" class="form-control" value="{{ date('Y') }}" aria-describedby="inputGroup-sizing-default" hidden>
                    <div class="d-grid mt-3 gap-2 d-md-flex justify-content-md-end">
                        <a class="btn btn-danger" style="margin-right: 10px" type="button" value="Batal" href="{{ route('ed_home') }}">Batal</a>
                        <input class="btn btn-primary" type="submit" value="Simpan">
                    </div>
                </form>
            </div>
        @endsection
    </body>
</html>