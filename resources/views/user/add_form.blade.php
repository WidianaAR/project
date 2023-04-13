@extends('layouts.navbar')

@section('isi')
    <div class="row m-0">
        <div class="col pl-1">
            <h5>User</h5>
        </div>
        <div class="col p-0 text-right">
            <span class="text-muted">User / <a href="">Tambah user</a></span>
        </div>
    </div>

    <div class="element">
        <div class="add-form">
            <form action="{{ route('add_user_action') }}" method="POST">
                @csrf
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect01">Pilih</label>
                    </div>
                    <select name='role_id' class="custom-select select-role" id="inputGroupSelect01" required>
                        <option value="" selected>Role user</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->role_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group mb-3" id="jurusan_option" hidden>
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect02">Pilih</label>
                    </div>
                    <select name='jurusan_id' class="custom-select select-jurusan" id="inputGroupSelect02">
                        <option value='' selected>Jurusan</option>
                        @foreach ($jurusans as $jurusan)
                            <option value={{ $jurusan->id }}>{{ $jurusan->nama_jurusan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group mb-3" id="prodi_option" hidden>
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect03">Pilih</label>
                    </div>
                    <select name='prodi_id' class="custom-select select-prodi" id="inputGroupSelect03">
                        <option value='' selected>Program studi</option>
                        @foreach ($prodis as $prodi)
                            <option value={{ $prodi->id }}>{{ $prodi->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>

                <input type="text" name="name" placeholder="Nama user" value="{{ old('name') }}"
                    class="form-control @error('name') is-invalid mb-0 @enderror" aria-describedby="name_error" required>
                @error('name')
                    <div id="name_error" class="invalid-feedback mt-0 mb-2">
                        {{ $message }}
                    </div>
                @enderror

                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid mb-0 @enderror" aria-describedby="email_error" required>
                @error('email')
                    <div id="email_error" class="invalid-feedback mt-0 mb-2">
                        {{ $message }}
                    </div>
                @enderror

                <input type="password" name="password" placeholder="Password"
                    class="form-control @error('password') is-invalid mb-0 @enderror" aria-describedby="password_error"
                    required>
                @error('password')
                    <div id="password_error" class="invalid-feedback mt-0 mb-2">
                        {{ $message }}
                    </div>
                @enderror

                <input type="password" name="confirm" placeholder="Confirm Password"
                    class="form-control @error('confirm') is-invalid mb-0 @enderror" aria-describedby="confirm_error"
                    required>
                @error('confirm')
                    <div id="confirm_error" class="invalid-feedback mt-0 mb-2">
                        {{ $message }}
                    </div>
                @enderror

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a class="btn btn-danger mr-2" type="button" value="Batal" href="{{ route('user') }}">Batal</a>
                    <input class="btn btn-primary" type="submit" value="Tambah">
                </div>
            </form>
        </div>
    </div>
@endsection
