@extends('layouts.navbar')

@section('title')
    <title>Prodi</title>
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
            <h5>Program studi</h5>
        </div>
        <div class="box col-auto text-right">
            <button data-toggle="dropdown" aria-expanded="false" class="simple">
                Jurusan <i class='fa fa-chevron-down fa-sm'></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="moduleDropDown">
                <a class="dropdown-item {{ Request::is('prodis') ? 'active' : '' }}" href="{{ URL('prodis') }}">Semua</a>
                @foreach ($jurusans as $jurusan)
                    <a class="dropdown-item {{ Request::is('prodis/filter/' . $jurusan->id) ? 'active' : '' }}"
                        href="{{ route('prodis_filter', $jurusan->id) }}">{{ $jurusan->nama_jurusan }}</a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="element pb-1">
        @if ($prodis->count())
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode Program Studi</th>
                        <th>Jurusan</th>
                        <th>Nama Program Studi</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($prodis as $prodi)
                        <tr>
                            <td>{{ $prodi->kode_prodi }}</td>
                            <td>{{ $prodi->jurusan->nama_jurusan }}</td>
                            <td>{{ $prodi->nama_prodi }}</td>
                            <td>
                                <a type="button" class="btn btn-outline-success btn-sm"
                                    href="{{ route('prodis.edit', $prodi->id) }}"><i class="fa fa-sm fa-edit"></i> Ubah</a>
                                <form action="{{ route('prodis.destroy', $prodi->id) }}" method="POST" class="d-inline">
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
            {{ $prodis->links() }}
        @else
            <h6>Maaf data tidak ditemukan</h6>
        @endif
    </div>

    <div class="floating-action-button">
        <a type="button" href="{{ route('prodis.create') }}" class="btn"><i class='fa fa-plus-circle fa-2x'
                style='color: #0D64AC'></i></a>
    </div>
@endsection
