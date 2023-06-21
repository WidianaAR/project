@extends('layouts.navbar')

@section('title')
    <title>Logs Aplikasi</title>
@endsection

@section('isi')
    <div class="row m-0 align-items-center">
        <div class="col pl-1">
            <h5>Riwayat aktivitas</h5>
        </div>
        <div class="col p-0 text-right">
            <button class="btn btn-secondary btn-sm" type="button" data-toggle="collapse" data-target="#collapseFilter"
                aria-expanded="false" aria-controls="collapseFilter">
                <i class="fa fa-sm fa-filter"></i>
                Filter
            </button>
        </div>
    </div>

    <div class="collapse mt-2" id="collapseFilter">
        <div class="card card-body py-2">
            <form action="{{ route('logs') }}" method="GET">
                @csrf
                <div class="row align-items-end">
                    <div class="col-3">
                        <label class="mb-0" for="activity">Jenis aktivitas</label>
                        <select class="form-control form-control-sm" name="activity" id="activity">
                            <option value="">Semua</option>
                            @foreach ($activities as $activity)
                                <option value="{{ $activity }}">{{ $activity }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-3">
                        <label class="mb-0" for="user">Jenis pengguna</label>
                        <select class="form-control form-control-sm" name="user" id="user">
                            <option value="">Semua</option>
                            <option value="1">PJM</option>
                            <option value="2">Ketua jurusan</option>
                            <option value="3">Koordinator program studi</option>
                            <option value="4">Auditor</option>
                        </select>
                    </div>

                    <div class="col-4">
                        <label class="mb-0">Rentang tanggal</label>
                        <div class="row m-0 align-items-end">
                            <div class="col p-0">
                                <input class="w-100 form-control form-control-sm" type="date" name="start_date">
                            </div>
                            <div class="col-auto p-0">
                                <pre> - </pre>
                            </div>
                            <div class="col p-0">
                                <input class="w-100 form-control form-control-sm" type="date" name="end_date">
                            </div>
                        </div>
                    </div>

                    <div class="col-2">
                        <input type="submit" class="w-100 btn btn-sm btn-primary" value="Terapkan">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="element pb-1">
        @if (count($datas))
            <p>Show entries:
                <a href="{{ request()->fullUrlWithQuery(['per_page' => '5']) }}">5</a> |
                <a href="{{ request()->fullUrlWithQuery(['per_page' => '10']) }}">10</a> |
                <a href="{{ request()->fullUrlWithQuery(['per_page' => '25']) }}">25</a>
            </p>
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Aktivitas</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datas as $log)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $log->causer->name ?? '-' }}</td>
                            <td>{{ $log->description }}</td>
                            <td>{{ date('d-m-Y h:m', strtotime($log->created_at)) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                {{ $datas->links() }}
            </div>
        @else
            <h6>Maaf data tidak ditemukan</h6>
        @endif
    </div>
@endsection
