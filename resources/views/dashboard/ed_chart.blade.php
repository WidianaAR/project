<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Evaluasi Diri</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    @extends('layouts.navbar')

    @section('isi')
        @cannot('koorprodi')
            @include('dashboard.files_card')
        @endcannot

        <div class="container text-center p-0 @cannot('koorprodi') mt-5 @endcannot">
            <form method="POST" action="{{ route('ed_chart_post') }}">
                @csrf
                <div class="row align-items-center mb-3">
                    <div class="col text-left">
                        @if ($param)
                            <span class="text-muted">Dashboard / Evaluasi diri /
                                <a href="">{{ $keterangan }}</a>
                            </span>
                        @else
                            <span class="text-muted">Dashboard /
                                <a href="">Evaluasi diri</a>
                            </span>
                        @endif
                    </div>
                    <div class="col-auto text-left box">
                        <button class="simple" type="button" data-toggle="dropdown" aria-expanded="false">
                            Ketegori <i class='fa fa-angle-down fa-sm'></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item {{ Request::is('ed_chart') ? 'active' : '' }}"
                                href="{{ URL('ed_chart') }}">Evaluasi Diri</a>
                            <a class="dropdown-item {{ Request::is('ks_chart') ? 'active' : '' }}"
                                href="{{ URL('ks_chart') }}">Ketercapaian Standar</a>
                        </div>
                    </div>
                    <div class="col-auto text-right p-0 box">
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

                    <div class="col-auto p-0 mr-3 box ">
                        <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
                    </div>
                </div>
            </form>

            @if ($param)
                <div class="row pb-2 justify-content-center">
                    <div class="col-6">
                        <div id="chart_radar"></div>
                    </div>
                    <div class="col-6">
                        <div id="chart_line"></div>
                    </div>
                </div>

                <div class="element text-left">
                    <h6 class="pb-2">Legend / keterangan</h6>
                    <small>
                        <ul style="columns: 2">
                            @for ($i = 0; $i < count($legend); $i++)
                                <li>{{ $legend[$i] }}</li>
                            @endfor
                        </ul>
                    </small>
                </div>
            @else
                <div class="element row mt-4">
                    <div class="col">
                        <h5>Data evaluasi diri masih ditinjau oleh Auditor</h5>
                    </div>
                </div>
            @endif
        </div>

        @can('pjm')
            <div class="floating-action-button">
                <a type="button" class="btn" href="" data-toggle="modal" data-target="#tambahPengumumanModal"><i
                        class='fa fa-bullhorn fa-xl' style='color: #0D64AC'></i></a>
            </div>
        @endcannot

        <script>
            var param = {!! json_encode($param) !!}
            var value = {!! json_encode($value) !!}
            var value2 = {!! json_encode($value2) !!}

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
                    name: 'Nilai capaian',
                    data: value
                }, {
                    name: 'Nilai akhir',
                    data: value2
                }]
            });

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
                    name: 'Nilai capaian',
                    data: value
                }, {
                    type: 'line',
                    name: 'Nilai akhir',
                    data: value2
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

    <div class="modal fade" id="tambahPengumumanModal" role="dialog" arialabelledby="modalLabel" area-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('add_pengumuman') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <h5>Pengumuman</h5>
                        <div class="input-group my-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Judul</span>
                            </div>
                            <input type="text" name="judul" class="form-control form-control-sm" required>
                        </div>
                        <textarea name="isi" rows="5" placeholder="Isi pengumuman" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                        <input class="btn btn-sm btn-primary" type="submit" value="Simpan">
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($pengumuman->count())
        <div class="modal fade" id="pengumumanModal" role="dialog" arialabelledby="modalLabel" area-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('close_pengumuman') }}" method="post">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <h5>Pengumuman</h5>
                            @foreach ($pengumuman as $data)
                                <b>{{ $data->judul }}</b>
                                <p>{{ $data->isi }}</p>
                                <input type="text" name="pengumuman_id[]" value="{{ $data->id }}" hidden>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <input type="submit" class="btn btn-sm btn-secondary" value="Tutup">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</body>

</html>
