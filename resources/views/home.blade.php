@extends('layouts.navbar')

@section('top-navbar')
    <li class="nav-item dropdown">
        <a class="dropdown-toggle nav-link" style="padding-right: 6vh" href="#" id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
            2022
        </a>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
            <a class="dropdown-item" href="#">2021</a>
            <a class="dropdown-item" href="#">2020</a>
        </div>
    </li>
    <li class="nav-item dropdown">
        <a class="dropdown-toggle nav-link" style="padding-right: 6vh" href="#" id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
            Informatika
        </a>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
            <a class="dropdown-item" href="#">Sistem Informasi</a>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">Link</a>
    </li>
@endsection

@section('isi')
    <h2> {{ Auth::user()->name }} </h2>
@endsection