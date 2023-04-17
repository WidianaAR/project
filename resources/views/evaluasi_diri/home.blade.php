@extends('layouts.navbar')

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
        @if ($deadline[0])
            <div class="col-auto pr-0">
                Batas akhir upload file :
            </div>
            <div class="col text-left">
                @include('evaluasi_diri/countdown')
            </div>
        @else
            <div class="col">
                <h5>Evaluasi Diri<span class="text-muted"> / {{ $keterangan }}</span></h5>
            </div>
        @endif

        @if ($years)
            <div class="col-auto text-left box">
                <button class="simple" type="button" data-toggle="dropdown" aria-expanded="false">
                    Tahun <i class='fa fa-angle-down fa-sm'></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="moduleDropDown">
                    <a class="dropdown-item {{ Request::is('evaluasi') ? 'active' : '' }}"
                        href="{{ route('ed_home') }}">Semua</a>
                    @foreach ($years as $year)
                        <a class="dropdown-item {{ Request::is('evaluasi/filter/year/' . $year) ? 'active' : '' }}"
                            href="{{ route('ed_filter_year', $year) }}">{{ $year }}</a>
                    @endforeach
                </div>
            </div>
        @endif

        @cannot('kajur')
            <div class="col-auto text-left box">
                <button class="simple" type="button" data-toggle="dropdown" aria-expanded="false">
                    Jurusan <i class='fa fa-angle-down fa-sm'></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="moduleDropDown">
                    @foreach ($jurusans as $jurusan)
                        <a class="dropdown-item {{ Request::is('evaluasi/filter/jurusan/' . $jurusan->id) ? 'active' : '' }}"
                            href="{{ route('ed_filter_jurusan', $jurusan->id) }}">{{ $jurusan->nama_jurusan }}</a>
                    @endforeach
                </div>
            </div>
        @endcannot

        @can('pjm')
            <div class="col-auto text-left box">
            @else
                <div class="col-auto text-left box mr-3">
                @endcan

                <button class="simple" type="button" data-toggle="dropdown" aria-expanded="false">
                    Prodi <i class='fa fa-angle-down fa-sm'></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="moduleDropDown">
                    @foreach ($prodis as $prodi)
                        <a class="dropdown-item {{ Request::is('evaluasi/filter/prodi/' . $prodi->id) ? 'active' : '' }}"
                            href="{{ route('ed_filter_prodi', $prodi->id) }}">{{ $prodi->nama_prodi }}</a>
                    @endforeach
                </div>
            </div>

            @can('pjm')
                <div class="col-auto text-left">
                    <form action="{{ route('ed_export_all') }}" method="POST">
                        @csrf
                        @foreach ($data as $file)
                            <input name="data[]" type="hidden" value="{{ $file->file_data }}">
                        @endforeach
                        <input type="submit" class="btn btn-primary" value="Export Semua File">
                    </form>
                </div>
            @endcan

            @can('kajur')
                @if ($deadline[0] != null)
                    <div class="floating-action-button">
                        <a type="button" href="{{ route('ed_import') }}" class="btn"><i class='fa fa-plus-circle fa-2x'
                                style='color: #0D64AC'></i></a>
                    </div>
                @endif
            @endcan
        </div>

        <div class="element pb-1">
            <div class="text-center">
                @if ($data->count())
                    <table class="table table-bordered">
                        <thead class="thead">
                            <tr>
                                <th>#</th>
                                <th>Nama File</th>
                                @cannot('kajur')
                                    <th>Jurusan</th>
                                @endcannot
                                <th>Program Studi</th>
                                <th>Tahun</th>
                                <th>Status</th>
                                @can('kajur')
                                    @if ($deadline[0])
                                        <th>Action</th>
                                    @endif
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $file)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><a href="{{ route('ed_table', $file->id) }}">{{ basename($file->file_data) }}</a>
                                    </td>
                                    @cannot('kajur')
                                        <td>{{ $file->prodi->jurusan->nama_jurusan }}</td>
                                    @endcannot
                                    <td>{{ $file->prodi->nama_prodi }}</td>
                                    <td>{{ $file->tahun }}</td>
                                    <td>{{ $file->status }}</td>

                                    @can('kajur')
                                        @if ($deadline[0])
                                            <td>
                                                <a type="button" class="btn btn-success"
                                                    href="{{ route('ed_change', $file->id) }}"><i class="fa fa-edit"></i></a>
                                                <a type="button" href="{{ route('ed_delete', $file->id) }}"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data?');"
                                                    class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                            </td>
                                        @endif
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $data->links() }}
                @else
                    <h5>Data Kosong</h5>
                @endif
            </div>
        </div>

        @can('pjm')
            <div class="floating-action-button">
                <a type="button" href="{{ route('ed_set_time') }}" class="btn"><i class='fa fa-clock fa-2x'
                        style='color: #0D64AC'></i></a>
            </div>
        @endcan
    @endsection
