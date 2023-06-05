@extends('layouts.navbar')

@section('title')
    <title>User</title>
@endsection

@section('isi')
    <div class="row m-0">
        <div class="col pl-1">
            <h5>User</h5>
        </div>
        <div class="col p-0 text-right">
            <span class="text-muted">User / <a href="">Tambah user</a></span>
        </div>
    </div>

    <div class="element row justify-content-center">
        <div class="add-form col-8">
            <form action="{{ route('add_user_action') }}" method="POST">
                @csrf
                <label class="mb-1">Jenis pengguna</label>
                <div class="input-group mb-3">
                    <select name='role_id' class="custom-select select-role" id="inputGroupSelect01" required>
                        <option value=""></option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->role_name }}</option>
                        @endforeach
                    </select>
                </div>

                <label class="mb-1" id="jurusan_option_label" hidden>Jurusan</label>
                <div class="input-group mb-3" id="jurusan_option" hidden>
                    <select name='jurusan_id' class="custom-select select-jurusan" id="inputGroupSelect02">
                        <option value='' selected></option>
                        @foreach ($jurusans as $jurusan)
                            <option value={{ $jurusan->id }} {{ old('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                                {{ $jurusan->nama_jurusan }}</option>
                        @endforeach
                    </select>
                </div>

                <label class="mb-1" id="prodi_option_label" hidden>Program studi</label>
                <div class="mb-3 input-group" id="prodi_option" hidden>
                    <select name='prodi_id' class="custom-select select-prodi" id="inputGroupSelect03">
                        <option value='' selected></option>
                        @foreach ($prodis as $prodi)
                            <option value={{ $prodi->id }} {{ old('prodi_id') == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>

                <label class="mb-1" id="option_auditor_label" hidden>Program studi</label>
                <div class="row" id="option_auditor" hidden>
                    <div class="col mr-0 mb-3" id="prodi_option_auditor">
                        <div class="input-group mb-2">
                            <select name='prodi_id_auditor[]' class="custom-select select-prodi" id="inputGroupSelect03">
                                <option value='' selected>-</option>
                                @foreach ($prodis as $prodi)
                                    <option value={{ $prodi->id }}
                                        {{ old('prodi_id') == $prodi->id ? 'selected' : '' }}>
                                        {{ $prodi->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-auto pl-0">
                        <button class="btn" type="button" id="add_prodi_btn">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>

                <label class="mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="form-control form-control-sm @error('name') is-invalid mb-0 @enderror"
                    aria-describedby="name_error" required>
                @error('name')
                    <div id="name_error" class="invalid-feedback mt-0 mb-2">
                        {{ $message }}
                    </div>
                @enderror

                <label class="mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="form-control form-control-sm @error('email') is-invalid mb-0 @enderror"
                    aria-describedby="email_error" required>
                @error('email')
                    <div id="email_error" class="invalid-feedback mt-0 mb-2">
                        {{ $message }}
                    </div>
                @enderror

                <label class="mb-1">Password</label>
                <div
                    class="d-flex w-100 div-input align-items-center py-1 pl-0 pr-2 form-control form-control-sm @error('password') is-invalid mb-0 @enderror">
                    <input class="mx-2 input-field border-0" type="password" name="password" id="password"
                        aria-describedby="password_error" required>
                    <div onclick="togglePassword()">
                        <i id="icon-toggle" class="fa fa-eye fa-sm" style="color: #b5b5b5"></i>
                    </div>
                </div>
                @error('password')
                    <div id="password_error" class="invalid-feedback mt-0 mb-2">
                        {{ $message }}
                    </div>
                @enderror

                <label class="mb-1">Konfirmasi password</label>
                <div
                    class="d-flex w-100 div-input align-items-center py-1 pl-0 pr-2 form-control form-control-sm @error('confirm') is-invalid mb-0 @enderror">
                    <input class="mx-2 input-field border-0" type="password" name="confirm" id="confirm"
                        aria-describedby="confirm_error" required>
                    <div onclick="toggleConfirm()">
                        <i id="icon-toggle-confirm" class="fa fa-eye fa-sm" style="color: #b5b5b5"></i>
                    </div>
                </div>
                @error('confirm')
                    <div id="confirm_error" class="invalid-feedback mt-0 mb-2">
                        {{ $message }}
                    </div>
                @enderror

                <div class="pt-2 d-md-flex justify-content-md-end">
                    <a class="btn btn-sm btn-secondary mr-2" type="button" value="Batal"
                        href="{{ route('user') }}">Batal</a>
                    <input class="btn btn-sm btn-primary" type="submit" value="Tambah pengguna">
                </div>
            </form>
        </div>
    </div>

    <script>
        $("#add_prodi_btn").click(function() {
            var input = `
                <div class="input-group mb-2">
                    <select name="prodi_id_auditor[]" class="custom-select select-prodi" id="inputGroupSelect03">
                        <option value="" selected>-</option>
                        @foreach ($prodis as $prodi)
                            <option value="{{ $prodi->id }}">{{ $prodi->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>
            `;
            $("#prodi_option_auditor").append(input);
        });

        function togglePassword() {
            var x = $("#password");
            if (x.attr("type") === "password") {
                x.attr("type", "text");
                $("#icon-toggle").attr("style", "color: #0D64AC");
            } else {
                x.attr("type", "password");
                $("#icon-toggle").attr("style", "color: #b5b5b5");
            }
        }

        function toggleConfirm() {
            var x = $("#confirm");
            if (x.attr("type") === "password") {
                x.attr("type", "text");
                $("#icon-toggle-confirm").attr("style", "color: #0D64AC");
            } else {
                x.attr("type", "password");
                $("#icon-toggle-confirm").attr("style", "color: #b5b5b5");
            }
        }
    </script>
@endsection
