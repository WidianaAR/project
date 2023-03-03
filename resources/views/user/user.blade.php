<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User</title>
        <link rel="stylesheet" href="{{ URL::Asset('css/app.css') }}">
    </head>

    <body>
        @extends('layouts.navbar')
        
        @section('top-navbar')
            <li class="nav-item dropdown">
                <a class="dropdown-toggle nav-link" style="padding-right: 6vh" href="#" id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
                    Jenis User
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item {{ Request::is('user') ? 'active' : '' }}" href="{{ URL('user') }}">All</a>
                    <a class="dropdown-item {{ Request::is('user/filter/pjm') ? 'active' : '' }}" href="{{ URL('user/filter/pjm') }}">PJM</a>
                    <a class="dropdown-item {{ Request::is('user/filter/kajur') ? 'active' : '' }}" href="{{ URL('user/filter/kajur') }}">Kepala Jurusan</a>
                    <a class="dropdown-item {{ Request::is('user/filter/koorprodi') ? 'active' : '' }}" href="{{ URL('user/filter/koorprodi') }}">Koordinator Program Studi</a>
                    <a class="dropdown-item {{ Request::is('user/filter/auditor') ? 'active' : '' }}" href="{{ URL('user/filter/auditor') }}">Auditor</a>
                </div>
            </li>
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

            <table class="table">
                <thead class="thead">
                <tr>
                    <th scope="col">Role</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Jurusan</th>
                    <th scope="col">Prodi</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($users as $user)            
                    <tr>
                        <td> 
                            @if ($user->id_role == 1)
                                PJM
                            @elseif ($user->id_role == 2)
                                Kajur
                            @elseif ($user->id_role == 3)
                                Koorprodi
                            @elseif ($user->id_role == 4)
                                Auditor
                            @endif
                        </td>
                        <td> {{ $user->name }} </td>
                        <td> {{ $user->email }} </td>
                        @if ($user->role_id == 2)
                            <td>{{$user->jurusan->nama_jurusan}}</td>
                            <td>-</td>
                        @elseif ($user->role_id == 3)
                            <td>{{$user->jurusan->nama_jurusan}}</td>
                            <td>{{$user->prodi->nama_prodi}}</td>
                        @else
                            <td>-</td>
                            <td>-</td>
                        @endif
                        <td>
                            <a type="button" class="btn btn-success" href="{!! route('change_user', $user->id) !!}"><i class="fa fa-edit"></i></a>
                            <a type="button" href="{!! route('delete_user', $user->id) !!}" onclick="return confirm('Apakah Anda Yakin Menghapus Data?');" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="floating-action-button">
                <a type="button" href="{{route('add_user')}}" class="btn"><i class='fa fa-plus-circle fa-2x' style='color: #0D64AC'></i></a>
            </div>
        @endsection
    </body>
</html>