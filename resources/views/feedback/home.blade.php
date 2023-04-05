@extends('layouts.navbar')
@section('isi')
    <div class="row m-0">
        <div class="col pl-1">
            <h5>Feedback</h5>
        </div>
        <span class="text-muted">Feedback / <a href="">Semua data</a></span>
    </div>

    @cannot('koorprodi')
        <form method="POST" action="{{ route('feedback_filter') }}">
            @csrf
            <div class="row align-items-center mb-3">
                <div class="col text-left">
                    <span class="text-muted">Feedback /
                        @if (!!$feedbacks)
                            <a href="">{{ $keterangan }}</a>
                        @else
                            <a href="">Data kosong</a>
                        @endif
                    </span>
                </div>
                <div class="col-auto text-right p-0 box">
                    <select class="form-control" id="tahun" name="tahun">
                        <option value="all">Semua tahun</option>
                        @foreach ($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-auto p-0 box" @can('kajur') ? hidden : @endcan>
                    <select class="form-control select-jurusan" id="jurusan" name="jurusan" onchange="update()">
                        <option value="all">Semua jurusan</option>
                        @foreach ($jurusans as $jurusan)
                            @foreach ($jurusan as $data)
                                <option value="{{ $data->prodi->jurusan->id }}">{{ $data->prodi->jurusan->nama_jurusan }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <div class="col-auto p-0 box">
                    <select class="form-control select-prodi" id="prodi" name="prodi">
                        <option value="all">Semua program studi</option>
                        @can('kajur')
                            @foreach ($prodis as $datas)
                                @foreach ($datas as $prodi)
                                    <option value="{{ $prodi->prodi->id }}">{{ $prodi->prodi->nama_prodi }}</option>
                                @endforeach
                            @endforeach
                        @endcan
                    </select>
                </div>

                <div class="col-auto p-0 mr-3 box">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </div>
            </div>
        </form>
    @endcannot

    <div class="element">
        @if (!!$feedbacks)
            @foreach ($feedbacks as $feedback)
                <a href="{{ route('feedback_detail', $feedback->id) }}" class="feedback-link">
                    <div class="card feedback-list mb-2">
                        <div class="card-text p-1">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-regular fa-file-lines fa-lg pl-2" style="color: #ababab;"></i>
                                </div>
                                <div class="col p-0">
                                    Temuan dan rekomendasi audit {{ $feedback->prodi->jurusan->nama_jurusan }} program studi
                                    {{ $feedback->prodi->nama_prodi }} <br>
                                    <small><span
                                            class="text-muted">{{ date('d-m-Y', strtotime($feedback->tanggal_audit)) }}</span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        @else
            <h5 class="text-center">Data kosong</h5>
        @endif
    </div>

    @cannot('koorprodi')
        <script>
            function update() {
                $('select.select-prodi').find('option').remove().end().append(
                    '<option value="all">Semua program studi</option>');
                var selected = $('select.select-jurusan').children("option:selected").val();
                var prodis = {!! json_encode($prodis) !!}
                $.each(prodis, function(i, prodi) {
                    $.each(prodi, function(i, data) {
                        if (data.prodi.jurusan_id == selected) {
                            $('select.select-prodi').append($('<option>', {
                                value: data.prodi.id,
                                text: data.prodi.nama_prodi
                            }))
                        }
                    })
                })
            }
        </script>
    @endcannot
@endsection
