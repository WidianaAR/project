@extends('layouts.navbar')
@section('isi')
    @if (session('success'))
        <div class="alert alert-success" role="alert" id="msg-box">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="row m-0">
        <div class="col pl-1">
            <h5>Panduan</h5>
        </div>
        <span class="text-muted">Panduan / <a href="">Semua data</a></span>
    </div>

    <div class="element pb-1">
        @if ($panduans->count())
            <table class="table table-bordered">
                <thead class="thead">
                    <th>No</th>
                    <th>Judul</th>
                    <th>Keterangan</th>
                    <th>File</th>
                    <th>Action</th>
                </thead>
                <tbody>
                    @foreach ($panduans as $panduan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $panduan->judul }}</td>
                            <td>{{ strip_tags(\Illuminate\Support\Str::limit($panduan->keterangan, 20, '...')) }}</td>
                            <td>
                                <a
                                    href="{{ route('panduan_download', $panduan->id) }}">{{ strip_tags(\Illuminate\Support\Str::limit(basename($panduan->file_data), 15, '...')) }}</a>
                            </td>
                            <td class="wd-2">
                                <a type="button" class="btn btn-secondary"
                                    href="{{ route('panduans.show', $panduan->id) }}"><i class="fa fa-eye"></i></a>
                                <a type="button" class="btn btn-success"
                                    href="{{ route('panduans.edit', $panduan->id) }}"><i class="fa fa-edit"></i></a>
                                <form action="{{ route('panduans.destroy', $panduan->id) }}" method="POST"
                                    class="d-inline">
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
            {{ $panduans->links() }}
        @else
            <h5 class="text-center">Data kosong</h5>
        @endif
    </div>

    <div class="floating-action-button">
        <a type="button" href="{{ route('panduans.create') }}" class="btn"><i class='fa fa-plus-circle fa-2x'
                style='color: #0D64AC'></i></a>
    </div>
@endsection
