@extends('layouts.navbar')

@section('isi')
    @if (session('success'))
        <div class="alert alert-success" role="alert" id="msg-box">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="container text-center p-0">
        <form method="POST" action="{{ route('ks_chart_post') }}">
            @csrf
            <div class="row align-items-center mb-3">
                <div class="col text-left">
                    @if (!!$param)
                        <span class="text-muted">
                            {{ $keterangan }}
                        </span>
                    @else
                        <span class="text-muted">Dashboard /
                            <a href="">Grafik ketercapaian standar</a>
                        </span>
                    @endif
                </div>
                <div class="col-auto text-right p-0 box">
                    <select class="form-control" id="tahun" name="tahun">
                        @if (!!$param)
                            @foreach ($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        @else
                            <option value="">{{ $keterangan }}</option>
                        @endif
                    </select>
                </div>

                <div class="col-auto p-0 box">
                    <select class="form-control select-jurusan" id="jurusan" name="jurusan" onchange="update()">
                        <option value="all">Semua jurusan</option>
                        @foreach ($jurusans as $jurusan)
                            <option value="{{ $jurusan->jurusan->id }}">{{ $jurusan->jurusan->nama_jurusan }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-auto p-0 box">
                    <select class="form-control select-prodi" id="prodi" name="prodi">
                        <option value="all">Semua program studi</option>
                    </select>
                </div>

                <div class="col-auto p-0 mr-3 box">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </div>
            </div>
        </form>

        @if (!!$param)
            <div class="row pb-2">
                <div class="col-6">
                    <div id="chart_column"></div>
                </div>
                <div class="col-6">
                    <div id="chart_radar"></div>
                </div>
            </div>

            <div class="element text-left">
                <h5 class="pb-2">Legend / keterangan</h5>
                <small>
                    <ul style="columns: 2">
                        @for ($i = 0; $i < count($param) - 1; $i++)
                            <li>{{ $param[$i] }}</li>
                        @endfor
                    </ul>
                </small>
            </div>
        @endif
    </div>

    <script>
        var param = {!! json_encode($param) !!}
        var value = {!! json_encode($value) !!}

        Highcharts.chart('chart_radar', {
            chart: {
                polar: true,
            },
            title: {
                text: 'Radar Chart'
            },
            xAxis: {
                categories: param,
                tickmarkPlacement: 'on',
                lineWidth: 0
            },
            yAxis: {
                gridLineInterpolation: 'polygon',
                lineWidth: 0,
                min: 0
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                line: {
                    marker: {
                        radius: 3
                    },
                    lineWidth: 2
                }
            },
            series: [{
                type: 'line',
                name: 'Persentase',
                data: value
            }]
        });

        Highcharts.chart('chart_column', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Column Chart'
            },
            xAxis: {
                categories: param
            },
            yAxis: {
                title: {
                    text: 'Nilai Capaian'
                }
            },
            legend: {
                enabled: false
            },
            series: [{
                name: 'Persentase',
                data: value
            }]
        });

        function update() {
            $('select.select-prodi').find('option').remove().end().append(
                '<option value="all">Semua program studi</option>');
            var selected = $('select.select-jurusan').children("option:selected").val();
            var prodis = {!! json_encode($prodis) !!}
            $.each(prodis, function(i, prodi) {
                if (prodi.jurusan_id == selected) {
                    $('select.select-prodi').append($('<option>', {
                        value: prodi.prodi_id,
                        text: prodi.nama_prodi
                    }))
                }
            })
        }
    </script>
@endsection
