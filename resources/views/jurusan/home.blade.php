@extends('layouts.navbar')

@section('title')
    <title>Jurusan</title>
@endsection

@section('isi')
    @if (session('success'))
        <div class="alert alert-success" role="alert" id="msg-box">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" role="alert" id="msg-box">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ session('error') }}
        </div>
    @endif

    <div class="row m-0">
        <div class="col pl-1">
            <h5>Jurusan</h5>
        </div>
        <span class="text-muted">Jurusan / <a href="">Semua data</a></span>
    </div>

    <div class="element pb-1">
        @if ($jurusans->count())
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode Jurusan</th>
                        <th>Singkatan</th>
                        <th>Nama Jurusan</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jurusans as $jurusan)
                        <tr>
                            <td>{{ $jurusan->kode_jurusan }}</td>
                            <td>{{ $jurusan->nama_jurusan }}</td>
                            <td>{{ $jurusan->keterangan }}</td>
                            <td>
                                <a type="button" class="btn btn-outline-success btn-sm"
                                    href="{{ route('jurusans.edit', $jurusan->id) }}"><i class="fa fa-sm fa-edit"></i>
                                    Ubah</a>
                                <form action="{{ route('jurusans.destroy', $jurusan->id) }}" method="POST"
                                    class="d-inline">
                                    @method('delete')
                                    @csrf
                                    <button onclick="return confirm('Apakah Anda yakin ingin menghapus data?');"
                                        class="btn btn-outline-danger btn-sm"><i class="fa fa-sm fa-trash"></i>
                                        Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $jurusans->links() }}
        @else
            <h5>Data kosong</h5>
        @endif
    </div>

    <div class="floating-action-button">
        <a type="button" href="{{ route('jurusans.create') }}" class="btn"><i class='fa fa-plus-circle fa-2x'
                style='color: #0D64AC'></i></a>
    </div>
@endsection
