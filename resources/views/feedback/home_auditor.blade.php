@extends('layouts.navbar')

@section('isi')
    @if (!!session('success'))
        <div class="alert alert-success" role="alert" id="msg-box">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ session('success') }}
        </div>
    @endif

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

            @cannot('kajur')
                <div class="col-auto p-0 box">
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
            @endcannot

            <div class="col-auto p-0 box">
                <select class="form-control select-prodi" id="prodi" name="prodi">
                    <option value="all">Semua program studi</option>
                    @can('kajur')
                        @foreach ($prodis as $prodi)
                            <option value="{{ $prodi->prodi->id }}">{{ $prodi->prodi->nama_prodi }}</option>
                        @endforeach
                    @endcannot
                </select>
            </div>

            <div class="col-auto p-0 mr-3 box">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </div>
        </div>
    </form>

    <div class="element">
        @if (!!$feedbacks)
            <table class="table table-bordered">
                <thead class="thead">
                    <th>No</th>
                    <th>Tanggal audit</th>
                    <th>Jurusan</th>
                    <th>Program Studi</th>
                    <th>Action</th>
                </thead>
                <tbody>
                    @foreach ($feedbacks as $feedback)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ date('d-m-Y', strtotime($feedback->tanggal_audit)) }}</td>
                            <td>{{ $feedback->prodi->jurusan->nama_jurusan }}</td>
                            <td>{{ $feedback->prodi->nama_prodi }}</td>
                            <td class="wd-2">
                                <a type="button" class="btn btn-secondary"
                                    href="{{ route('feedbacks.show', $feedback->id) }}"><i class="fa fa-eye"></i></a>
                                <a type="button" class="btn btn-success"
                                    href="{{ route('feedbacks.edit', $feedback->id) }}"><i class="fa fa-edit"></i></a>
                                <form action="{{ route('feedbacks.destroy', $feedback->id) }}" method="POST"
                                    class="d-inline">
                                    @method('delete')
                                    @csrf
                                    <button onclick="return confirm('Apakah Anda yakin ingin menghapus data?');"
                                        class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <h5 class="text-center">Data kosong</h5>
        @endif
    </div>

    <div class="floating-action-button">
        <a type="button" href="{{ route('feedbacks.create') }}" class="btn"><i class='fa fa-plus-circle fa-2x'
                style='color: #0D64AC'></i></a>
    </div>

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
@endsection
