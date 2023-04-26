@extends('layouts.navbar')
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
        <span class="text-muted">Program studi / <a href="">Semua data</a></span>
    </div>

    <div class="element pb-1">
        @if ($prodis->count())
            <table class="table table-bordered">
                <thead class="thead">
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
                                <a type="button" class="btn btn-success" href="{{ route('prodis.edit', $prodi->id) }}"><i
                                        class="fa fa-edit"></i></a>
                                <form action="{{ route('prodis.destroy', $prodi->id) }}" method="POST" class="d-inline">
                                    @method('delete')
                                    @csrf
                                    <button onclick="return confirm('Apakah Anda yakin ingin menghapus data?');"
                                        class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $prodis->links() }}
        @else
            <h5>Data kosong</h5>
        @endif
    </div>

    <div class="floating-action-button">
        <a type="button" href="{{ route('prodis.create') }}" class="btn"><i class='fa fa-plus-circle fa-2x'
                style='color: #0D64AC'></i></a>
    </div>
@endsection
