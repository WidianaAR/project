@extends('layouts.navbar')

@section('isi')
    <div class="row m-0">
        <div class="col pl-1">
            <h5>User</h5>
        </div>
        <div class="col p-0 text-right">
            <span class="text-muted">User / <a href="">Ubah user</a></span>
        </div>
    </div>

    <div class="element">
        <div class="add-form">
            <form action="{{ route('change_user_action', $user->id) }}" method="POST">
                @csrf
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect01">Pilih</label>
                    </div>
                    <select name='role_id' class="custom-select select-role" id="inputGroupSelect01" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @if (old('role_id') == $role->id || $user->role_id == $role->id) selected @endif>
                                {{ $role->role_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group mb-3" id="jurusan_option" hidden>
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect02">Pilih</label>
                    </div>
                    <select name='jurusan_id' onChange="update()" class="custom-select select-jurusan"
                        id="inputGroupSelect02">
                        <option value='{{ $user->jurusan_id }}'>Jurusan</option>
                        @foreach ($jurusans as $jurusan)
                            <option value={{ $jurusan->id }}>
                                {{ $jurusan->nama_jurusan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group mb-3" id="prodi_option" hidden>
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect03">Pilih</label>
                    </div>
                    <select name='prodi_id' class="custom-select select-prodi" id="inputGroupSelect03">
                        <option value='{{ $user->prodi_id }}' selected>Program studi</option>
                    </select>
                </div>
                <input name="id" value="{{ $user->id }}" hidden>

                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="form-control @error('name') is-invalid mb-0 @enderror" aria-describedby="name_error" required>
                @error('name')
                    <div id="name_error" class="invalid-feedback mt-0 mb-2">
                        {{ $message }}
                    </div>
                @enderror

                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="form-control @error('email') is-invalid mb-0 @enderror" aria-describedby="email_error" required>
                @error('email')
                    <div id="email_error" class="invalid-feedback mt-0 mb-2">
                        {{ $message }}
                    </div>
                @enderror

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a class="btn btn-danger mr-2" type="button" href="{{ route('user') }}">Batal</a>
                    <input class="btn btn-primary" type="submit" value="Simpan">
                </div>
            </form>
        </div>
    </div>

    <script type="text/javascript">
        function update() {
            $('select.select-prodi').find('option').remove().end().append(
                '<option value="">Program studi</option>');
            var selected = $('select.select-jurusan').children("option:selected").val();
            var prodis = {!! json_encode($prodis) !!}
            $.each(prodis, function(i, prodi) {
                if (prodi.jurusan_id == selected) {
                    $('select.select-prodi').append($('<option>', {
                        value: prodi.id,
                        text: prodi.nama_prodi
                    }))
                }
            })
        }
    </script>
@endsection
