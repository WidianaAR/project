<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Evaluasi Diri</title>
</head>

<body>
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

        <div class="row align-items-center">
            @if ($deadline[0])
                <div class="col-auto pr-0">
                    Batas akhir upload file :
                </div>
                <div class="col text-left">
                    @include('layouts.countdown')
                </div>
            @else
                <div class="col">
                    <h5>Evaluasi Diri<span class="text-muted"> / {{ $keterangan }}</span></h5>
                </div>
            @endif

            <div class="col-auto text-left pr-1">
                <form action="{{ route('ed_export_all') }}" method="POST">
                    @csrf
                    @foreach ($data as $file)
                        <input name="data[]" type="hidden" value="{{ $file->file_data }}">
                    @endforeach
                    <input type="submit" class="btn btn-sm btn-primary" value="Export semua file">
                </form>
            </div>
            @can('pjm')
                <div class="col-auto text-left px-0">
                    <a href="{{ route('ed_set_time') }}" type="button" class="btn btn-success btn-sm"><i
                            class="fa fa-clock-o fa-sm"></i> Atur
                        deadline</a>
                </div>
            @endcan
            <div class="col-auto text-left pl-1">
                <button class="btn btn-secondary btn-sm" type="button" data-toggle="collapse" data-target="#collapseFilter"
                    aria-expanded="false" aria-controls="collapseFilter">
                    <i class="fa fa-sm fa-filter"></i>
                    Filter
                </button>
            </div>
        </div>

        <div class="collapse mt-2" id="collapseFilter">
            <div class="card card-body py-2">
                <form action="{{ route('ed_home') }}" method="GET">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col">
                            <label class="mb-0" for="status">Status</label>
                            <select class="form-control form-control-sm" name="status" id="status">
                                <option value="">Semua</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->keterangan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col">
                            <label class="mb-0" for="tahun">Tahun</label>
                            <select class="form-control form-control-sm" name="tahun" id="tahun">
                                <option value="">Semua</option>
                                @foreach ($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>

                        @cannot('kajur')
                            <div class="col">
                                <label class="mb-0" for="jurusan">Jurusan</label>
                                <select class="form-control form-control-sm" name="jurusan" id="jurusan">
                                    <option value="">Semua</option>
                                    @foreach ($jurusans as $jurusan)
                                        <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endcannot

                        <div class="col">
                            <label class="mb-0" for="prodi">Program studi</label>
                            <select class="form-control form-control-sm" name="prodi" id="prodi">
                                <option value="">Semua</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-auto">
                            <input type="submit" class="btn btn-sm btn-primary" value="Terapkan">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="element pb-1 table-responsive">
            @if ($data->count())
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>File</th>
                            @cannot('kajur')
                                <th>Jurusan</th>
                            @endcannot
                            <th>Program Studi</th>
                            <th>Tahun</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $file)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ strip_tags(\Illuminate\Support\Str::limit(basename($file->file_data), 20, '...')) }}
                                    @cannot('kajur')
                                    <td>{{ $file->prodi->jurusan->nama_jurusan }}</td>
                                @endcannot
                                <td>{{ $file->prodi->nama_prodi }}</td>
                                <td>{{ $file->tahun }}</td>
                                <td>
                                    <a href="" data-toggle="modal"
                                        data-target="#tahapModal{{ $loop->iteration }}">{{ $file->status->keterangan }}</a>
                                </td>

                                <td>
                                    <a type="button" class="btn btn-outline-secondary btn-sm"
                                        href="{{ route('ed_table', $file->id) }}"><i class="fa fa-sm fa-eye"></i>
                                        Lihat</a>
                                    @can('kajur')
                                        @if ($deadline[0] && $file->tahun == date('Y'))
                                            <a type="button" class="btn btn-outline-success btn-sm"
                                                href="{{ route('ed_change', $file->id) }}"><i class="fa fa-sm fa-edit"></i>
                                                Ubah</a>
                                            <a type="button" href="{{ route('ed_delete', $file->id) }}"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus data?');"
                                                class="btn btn-outline-danger btn-sm"><i class="fa fa-sm fa-trash"></i>
                                                Hapus</a>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $data->links() }}
            @else
                <h6>Maaf data tidak ditemukan</h6>
            @endif
        </div>

        @can('kajur')
            @if ($deadline[0])
                <div class="floating-action-button">
                    <a type="button" href="{{ route('ed_import') }}" class="btn"><i class='fa fa-plus-circle fa-2x'
                            style='color: #0D64AC'></i></a>
                </div>
            @endif
        @endcan
    @endsection

    @foreach ($data as $file)
        <div class="modal fade" id="tahapModal{{ $loop->iteration }}" role="dialog" arialabelledby="modalLabel"
            area-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="row pl-5">
                        @include('layouts.tahap_breadcrumb')
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</body>

</html>
