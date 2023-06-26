<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pasca Audit</title>
</head>

<body>
    @extends('layouts.navbar')

    @section('isi')
        <div class="row align-items-center">
            <div class="col">
                <span class="text-muted">Daftar pasca / <a href="">{{ $keterangan }}</a></span>
            </div>
            <div class="col-auto text-left pl-0">
                <button class="btn btn-secondary btn-sm" type="button" data-toggle="collapse" data-target="#collapseFilter"
                    aria-expanded="false" aria-controls="collapseFilter">
                    <i class="fa fa-sm fa-sm fa-filter"></i>
                    Filter
                </button>
            </div>
        </div>

        <div class="collapse mt-2" id="collapseFilter">
            <div class="card card-body py-2">
                <form action="{{ route('pasca_filter') }}" method="POST">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col">
                            <label class="mb-0" for="kategori">Kategori</label>
                            <select class="form-control form-control-sm" name="kategori" id="kategori">
                                <option value="">Semua</option>
                                <option value="evaluasi">Simulasi akreditasi</option>
                                <option value="standar">Audit mutu internal</option>
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
                            <th>Kategori</th>
                            <th>Program Studi</th>
                            <th>Tahun</th>
                            <th>Status</th>
                            <th>Action</th>
                    </thead>
                    <tbody>
                        @foreach ($data as $file)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ strip_tags(\Illuminate\Support\Str::limit(basename($file->file_data), 20, '...')) }}
                                </td>
                                <td>{{ $file->kategori }}</td>
                                <td>{{ $file->prodi->nama_prodi }}</td>
                                <td>{{ $file->tahun }}</td>
                                <td>
                                    <a href="" data-toggle="modal"
                                        data-target="#tahapModal{{ $loop->iteration }}">{{ $file->status->keterangan }}</a>
                                </td>
                                <td>
                                    @if ($file->status_id != 7)
                                        <a href="@if ($file->kategori == 'evaluasi') {{ route('pasca_ed_table', $file->id) }} @else {{ route('pasca_ks_table', $file->id) }} @endif"
                                            class="btn btn-sm btn-outline-primary">Komentar/Nilai</a>
                                    @else
                                        <a href="@if ($file->kategori == 'evaluasi') {{ route('pasca_ed_table', $file->id) }} @else {{ route('pasca_ks_table', $file->id) }} @endif"
                                            class="btn btn-sm btn-outline-success">Lihat</a>
                                    @endif
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
