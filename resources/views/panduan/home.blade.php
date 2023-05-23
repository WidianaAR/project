@extends('layouts.navbar')
@section('isi')
    <div class="row m-0">
        <div class="col pl-1">
            <h5>Panduan</h5>
        </div>
        <span class="text-muted">Panduan / <a href="">Semua data</a></span>
    </div>

    <div class="element">
        @if ($panduans->count())
            <div class="row justify-content-center">
                @foreach ($panduans as $panduan)
                    <div class="col-auto m-3">
                        <div class="card border-info" style="width: 18rem; height: 14rem">
                            <div class="card-body">
                                <h5 class="card-title">{{ $panduan->judul }}</h5>
                                <p class="card-text">
                                    {{ strip_tags(\Illuminate\Support\Str::limit($panduan->keterangan, 100, '...')) }}</p>
                                <div class="row">
                                    <a class="col" href="{{ route('panduan_detail', $panduan->id) }}">Read more</a>
                                    <div class="col"></div>
                                    <a href="{{ route('panduan_download', $panduan->id) }}" class="col-auto mr-1"><i
                                            class="fa-solid fa-download"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-end">
                {{ $panduans->links() }}
            </div>
        @else
            <h5 class="text-center">Data kosong</h5>
        @endif
    </div>
@endsection
