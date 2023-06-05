@extends('layouts.navbar')

@section('title')
    <title>Logs Aplikasi</title>
@endsection

@section('isi')
    <div class="row m-0">
        <div class="col pl-1">
            <h5>Riwayat aktivitas</h5>
        </div>
        <div class="col p-0 text-right">
            <span class="text-muted">Riwayat aktivitas / <a href="">Semua data</a></span>
        </div>
    </div>

    <div class="element pb-1">
        @if (count($datas))
            <p>Show entries:
                <a href="{{ route('logs', ['per_page' => 5]) }}">5</a> |
                <a href="{{ route('logs', ['per_page' => 10]) }}">10</a> |
                <a href="{{ route('logs', ['per_page' => 25]) }}">25</a>
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
                            <td>{{ $log->created_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                {{ $datas->links() }}
            </div>
        @else
            <h6>Data kosong</h6>
        @endif
    </div>
@endsection
