<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User</title>
        <link rel="stylesheet" href="{{ URL::Asset('css/app.css') }}">
    </head>

    <body>
        @extends('layouts.navbar')
            
        @section('top-navbar')
            <li class="nav-item">
                <a class="nav-link">Tambah Data User</a>
            </li>
        @endsection
            
        @section('isi')
        @if ($errors->any())
            <div class="alert alert-danger" role="alert" id="msg-box">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif
        
        <div class="add-form">
            <form action="{{ route('add_user_action') }}" method="POST">
            @csrf
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect01">Pilih</label>
                    </div>
                    <select name='role_id' class="custom-select select-role" id="inputGroupSelect01">
                        <option selected>.:: Role User ::.</option>
                        <option value=1>Pusat Penjaminan Mutu (PJM)</option>
                        <option value=2>Kepala Jurusan (Kajur)</option>
                        <option value=3>Koordinator Program Studi (Koorprodi)</option>
                        <option value=4>Auditor</option>
                    </select>
                </div>
                <div class="input-group mb-3" id="jurusan_option" hidden>
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect02">Pilih</label>
                    </div>
                    <select name='jurusan_id' onChange="update()" class="custom-select select-jurusan" id="inputGroupSelect02">
                        <option value='' selected>.:: Jurusan ::.</option>
                        @foreach ($jurusans as $jurusan)
                            <option value={{$jurusan->id}}>{{$jurusan->nama_jurusan}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group mb-3" id="prodi_option" hidden>
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect03">Pilih</label>
                    </div>
                    <select name='prodi_id' class="custom-select select-prodi" id="inputGroupSelect03">
                        <option value='' selected>.:: Program Studi ::.</option>
                    </select>
                </div>
                <input type="text" name="name" placeholder="Name" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                <input type="email" name="email" placeholder="Email" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                <input type="password" name="password" placeholder="Password" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                <input type="password" name="password_confirm" placeholder="Confirm Password" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a class="btn btn-danger" style="margin-right: 10px" type="button" value="Batal" href="{{ route('user') }}">Batal</a>
                    <input class="btn btn-primary" type="submit" value="Tambah">
                </div>
            </form>
        </div>

        <script type="text/javascript">
            function update() {
                $('select.select-prodi').find('option').remove().end().append('<option value="">.:: Program Studi ::.</option>');
                var selected = $('select.select-jurusan').children("option:selected").val();
                var prodis = {!! json_encode($prodis) !!}
                $.each(prodis, function (i, prodi) {
                    if (prodi.jurusan_id == selected) {
                        $('select.select-prodi').append($('<option>', {
                            value: prodi.id,
                            text : prodi.nama_prodi
                        }))
                    }
                })
            }
        </script>
        @endsection
    </body>
</html>