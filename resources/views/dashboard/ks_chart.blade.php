@extends('layouts.navbar')

@section('title')
    <title>Dashboard Audit Mutu Internal</title>
@endsection

@section('isi')
    @cannot('koorprodi')
        @include('dashboard.files_card')
    @endcannot

    <div class="container text-center p-0 @cannot('koorprodi') mt-5 @endcannot">
        <form method="POST" action="{{ route('ks_chart_post') }}">
            @csrf
            <div class="row align-items-center mb-3">
                <div class="col text-left">
                    @if ($param)
                        <span class="text-muted">Dashboard / Audit Mutu Internal /
                            <a href="">{{ $keterangan }}</a>
                        </span>
                    @else
                        <span class="text-muted">Dashboard /
                            <a href="">Audit Mutu Internal</a>
                        </span>
                    @endif
                </div>
                <div class="col-auto text-left box">
                    <button class="simple" type="button" data-toggle="dropdown" aria-expanded="false">
                        Ketegori <i class='fa fa-angle-down fa-sm'></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="moduleDropDown">
                        <a class="dropdown-item {{ Request::is('ed_chart') ? 'active' : '' }}"
                            href="{{ URL('ed_chart') }}">Simulasi Akreditasi</a>
                        <a class="dropdown-item {{ Request::is('ks_chart') ? 'active' : '' }}"
                            href="{{ URL('ks_chart') }}">Audit Mutu Internal</a>
                    </div>
                </div>
                <div class="col-auto p-0 box">
                    <select class="form-control form-control-sm" id="tahun" name="tahun">
                        @if ($param)
                            @foreach ($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        @else
                            <option value="">{{ $keterangan }}</option>
                        @endif
                    </select>
                </div>

                @can('pjm')
                    <div class="col-auto p-0 box">
                        <select class="form-control form-control-sm select-jurusan" id="jurusan" name="jurusan"
                            onchange="update()">
                            <option value="">Semua jurusan</option>
                            @foreach ($jurusans as $data)
                                @foreach ($data as $jurusan)
                                    <option value="{{ $jurusan->prodi->jurusan->id }}">
                                        {{ $jurusan->prodi->jurusan->nama_jurusan }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                @endcan

                @cannot('koorprodi')
                    <div class="col-auto p-0 box">
                        <select class="form-control form-control-sm @can('pjm') select-prodi @endcan" id="prodi"
                            name="prodi">
                            <option value="">Semua program studi</option>
                            @cannot('pjm')
                                @foreach ($prodis as $data)
                                    @foreach ($data as $prodi)
                                        <option value="{{ $prodi->prodi->id }}">
                                            {{ $prodi->prodi->nama_prodi }}
                                        </option>
                                    @endforeach
                                @endforeach
                            @endcannot
                        </select>
                    </div>
                @endcannot

                <div class="col-auto p-0 mr-3 box">
                    <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
                </div>
            </div>
        </form>

        @if ($param)
            <div class="row pb-2">
                <div class="col-6">
                    <div id="chart_column"></div>
                </div>
                <div class="col-6">
                    <div id="chart_radar"></div>
                </div>
            </div>

            <div class="element text-left">
                <h6 class="pb-2">Legend / keterangan</h6>
                <small>
                    <ul style="columns: 2">
                        @for ($i = 0; $i < count($param); $i++)
                            <li>{{ $param[$i] }}</li>
                        @endfor
                    </ul>
                </small>
            </div>
        @else
            <div class="element row mt-4">
                <div class="col">
                    <h5>Instrumen AMI masih ditinjau oleh Auditor</h5>
                </div>
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
                text: 'Diagram Instrumen Audit Mutu Internal'
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
                text: 'Diagram Instrumen Audit Mutu Internal'
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
                '<option value="">Semua program studi</option>');
            var selected = $('select.select-jurusan').children("option:selected").val();
            var prodis = {!! json_encode($prodis) !!}
            $.each(prodis, function(i, data) {
                $.each(data, function(i, prodi) {
                    if (prodi.prodi.jurusan_id == selected) {
                        $('select.select-prodi').append($('<option>', {
                            value: prodi.prodi.id,
                            text: prodi.prodi.nama_prodi
                        }))
                    }
                })
            })
        }
    </script>
@endsection
