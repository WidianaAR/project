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
            <li class="nav-item">
                <a class="nav-link">Import File Ketercapaian Standar</a>
            </li>
        @endsection

        @section('isi')
        {{-- Countdown Time --}}
            <div>
                @include('ketercapaian_standar/countdown')
            </div>

            <div class="add-form">
                <form action="{{ route('ks_import_action') }}" method="POST" enctype="multipart/form-data">
                @csrf
                    <input type="file" name="file" class="form-control" aria-describedby="inputGroup-sizing-default">
                    <select name='prodi' class="custom-select" id="inputGroupSelect03">
                        <option value='' selected>.:: Pilih Program Studi ::.</option>
                        @foreach ($prodis as $prodi)
                            <option value="{{$prodi->id}}">{{$prodi->nama_prodi}}</option>
                        @endforeach
                    </select>
                    <input type="text" name="jurusan" class="form-control" value="{{Auth::user()->jurusan_id}}" aria-describedby="inputGroup-sizing-default" hidden>
                    <input type="text" name="tahun" class="form-control" value="{{ date('Y') }}" aria-describedby="inputGroup-sizing-default" hidden>
                    <div class="d-grid mt-3 gap-2 d-md-flex justify-content-md-end">
                        <a class="btn btn-danger" style="margin-right: 10px" type="button" value="Batal" href="{{ route('ks_home') }}">Batal</a>
                        <input class="btn btn-primary" type="submit" value="Simpan">
                    </div>
                </form>
            </div>
        @endsection
    </body>
</html>