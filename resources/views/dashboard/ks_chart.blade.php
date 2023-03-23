@extends('layouts.navbar')

@section('isi')
    <div class="container text-center p-0">
        <form method="POST" action="{{ route('ks_chart_post') }}">
            @csrf
            <div class="row align-items-center mb-3">
                <div class="col text-left">
                    <span class="text-muted">Dashboard / <a href="">Grafik ketercapaian standar</a></span>
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

                <div class="col-auto p-0 box">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </div>
            </div>
        </form>

        @if (!!$param)
            <div class="row">
                <div class="col-6">
                    <div id="chart_column"></div>
                </div>
                <div class="col-6">
                    <div id="chart_line"></div>
                </div>
            </div>
        @endif
    </div>

    <script>
        var param = {!! json_encode($param) !!}
        var value = {!! json_encode($value) !!}

        Highcharts.chart('chart_line', {
            chart: {
                type: 'line'
            },
            title: {
                text: 'Line Chart'
            },
            xAxis: {
                categories: param
            },
            yAxis: {
                title: {
                    text: 'Nilai Capaian'
                }
            },
            plotOptions: {
                line: {
                    lineWidth: 2,
                    marker: {
                        radius: 3
                    }
                }
            },
            series: [{
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
