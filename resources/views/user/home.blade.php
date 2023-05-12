@extends('layouts.navbar')

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

    <div class="row m-0">
        <div class="col pl-1">
            <h5>User</h5>
        </div>
        <div class="box col-auto text-right">
            <button data-toggle="dropdown" aria-expanded="false" class="simple">
                Jenis User <i class='fa fa-chevron-down fa-sm'></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="moduleDropDown">
                <a class="dropdown-item {{ Request::is('user') ? 'active' : '' }}" href="{{ URL('user') }}">Semua</a>
                <a class="dropdown-item {{ Request::is('user/filter/pjm') ? 'active' : '' }}"
                    href="{{ route('user_pjm') }}">PJM</a>
                <a class="dropdown-item {{ Request::is('user/filter/kajur') ? 'active' : '' }}"
                    href="{{ route('user_kajur') }}">Ketua Jurusan</a>
                <a class="dropdown-item {{ Request::is('user/filter/koorprodi') ? 'active' : '' }}"
                    href="{{ route('user_koorprodi') }}">Koordinator Program Studi</a>
                <a class="dropdown-item {{ Request::is('user/filter/auditor') ? 'active' : '' }}"
                    href="{{ route('user_auditor') }}">Auditor</a>
            </div>
        </div>
    </div>
    <div class="element pb-1">
        @if ($users->count())
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Role</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Jurusan</th>
                        <th>Prodi</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td> {{ $user->role->role_name }} </td>
                            <td> {{ $user->name }} </td>
                            <td> {{ $user->email }} </td>
                            @if ($user->role_id == 2)
                                <td>{{ $user->jurusan->nama_jurusan }}</td>
                                <td>-</td>
                            @elseif ($user->role_id == 3 || $user->role_id == 4)
                                <td>{{ $user->jurusan->nama_jurusan }}</td>
                                <td>{{ $user->prodi->nama_prodi }}</td>
                            @else
                                <td>-</td>
                                <td>-</td>
                            @endif
                            <td>
                                <a type="button" class="btn btn-outline-success btn-sm"
                                    href="{{ route('change_user', $user->id) }}"><i class="fa fa-edit"></i> Ubah</a>
                                <a type="button" href="{{ route('delete_user', $user->id) }}"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data?');"
                                    class="btn btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Hapus</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $users->links() }}
        @else
            <h5>Data kosong</h5>
        @endif
    </div>

    <div class="floating-action-button">
        <a type="button" href="{{ route('add_user') }}" class="btn"><i class='fa fa-plus-circle fa-2x'
                style='color: #0D64AC'></i></a>
    </div>
@endsection
