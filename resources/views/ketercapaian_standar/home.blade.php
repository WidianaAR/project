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
            <li class="nav-item dropdown">
                <a class="dropdown-toggle nav-link" style="padding-right: 6vh" href="#" id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
                    Pilih Tahun
                </a>
            </li>
            @if (Auth::user()->role_id == 1)
            <li class="nav-item">
                <a class="nav-link" href="{{ URL('standar/set_waktu') }}">Set Batas Waktu Pengisian</a>
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
            </div>
        @endsection
    </body>
</html>