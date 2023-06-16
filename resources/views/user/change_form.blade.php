@extends('layouts.navbar')

@section('title')
    <title>User</title>
@endsection

@section('isi')
    @if (Session::has('error'))
        <div class="alert alert-danger" role="alert" id="msg-box">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ Session::get('error') }}
        </div>
    @endif

    <div class="row m-0">
        <div class="col pl-1">
            <h5>User</h5>
        </div>
        <div class="col p-0 text-right">
            <span class="text-muted">User / <a href="">Ubah user</a></span>
        </div>
    </div>

    <div class="element row justify-content-center">
        <div class="add-form col-8">
            <form action="{{ route('change_user_action', $user->id) }}" method="POST">
                @csrf
                <label class="mb-1">Jenis pengguna</label>
                <div class="input-group mb-3">
                    <select name='role_id' class="custom-select select-role" id="inputGroupSelect01" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @if ($user->role_id == $role->id) selected @endif>
                                {{ $role->role_name }}</option>
                        @endforeach
                    </select>
                </div>

                <label class="mb-1" id="jurusan_option_label"
                    @if ($user->role_id != 2) hidden @endif>Jurusan</label>
                <div class="input-group mb-3" id="jurusan_option" @if ($user->role_id != 2) hidden @endif>
                    <select name='jurusan_id' class="custom-select select-jurusan" id="inputGroupSelect02">
                        <option value="" selected></option>
                        @foreach ($jurusans as $jurusan)
                            <option value={{ $jurusan->id }} @if (count($user->user_access_file) && $user->user_access_file[0]->jurusan_id == $jurusan->id) selected @endif>
                                {{ $jurusan->nama_jurusan }}</option>
                        @endforeach
                    </select>
                </div>

                <label class="mb-1" id="prodi_option_label" @if ($user->role_id != 3) hidden @endif>Program
                    studi</label>
                <div class="input-group mb-3" id="prodi_option" @if ($user->role_id != 3) hidden @endif>
                    <select name='prodi_id' class="custom-select select-prodi">
                        <option value='' selected></option>
                        @foreach ($prodis as $prodi)
                            <option value={{ $prodi->id }} @if (count($user->user_access_file) && $user->user_access_file[0]->prodi_id == $prodi->id) selected @endif>
                                {{ $prodi->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>

                <label class="mb-1" id="option_auditor_label" @if ($user->role_id != 4) hidden @endif>Program
                    studi</label>
                <div class="row" id="option_auditor" @if ($user->role_id != 4) hidden @endif>
                    <div class="col mr-0 mb-3" id="prodi_option_auditor">
                        @if ($prodi_auditor)
                            @foreach ($prodi_auditor as $data)
                                <div class="input-group mb-2">
                                    <select name='prodi_id_auditor[]' class="custom-select select-prodi">
                                        <option value='' selected>-</option>
                                        @foreach ($prodis as $prodi)
                                            <option value="{{ $prodi->id }}"
                                                @if ($data == $prodi->id) selected @endif>
                                                {{ $prodi->nama_prodi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        @else
                            <div class="input-group mb-2">
                                <select name='prodi_id_auditor[]' class="custom-select select-prodi">
                                    <option value='' selected>-</option>
                                    @foreach ($prodis as $prodi)
                                        <option value={{ $prodi->id }}>{{ $prodi->nama_prodi }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                    <div class="col-auto pl-0">
                        <button class="btn" type="button" id="add_prodi_btn">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>

                <input name="id" value="{{ $user->id }}" hidden>

                <label class="mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="form-control form-control-sm @error('name') is-invalid mb-0 @enderror"
                    aria-describedby="name_error" required>
                @error('name')
                    <div id="name_error" class="invalid-feedback mt-0 mb-2">
                        {{ $message }}
                    </div>
                @enderror

                <label class="mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="form-control form-control-sm @error('email') is-invalid mb-0 @enderror"
                    aria-describedby="email_error" required>
                @error('email')
                    <div id="email_error" class="invalid-feedback mt-0 mb-2">
                        {{ $message }}
                    </div>
                @enderror

                <label class="mb-1">Password</label>
                <div
                    class="d-flex w-100 div-input align-items-center pl-0 pr-2 mb-0 form-control form-control-sm @error('password') is-invalid @enderror">
                    <input class="mx-2 input-field border-0" type="password" name="password" id="password"
                        aria-describedby="password_error">
                    <div onclick="togglePassword()">
                        <i id="icon-toggle" class="fa fa-eye fa-sm" style="color: #b5b5b5"></i>
                    </div>
                </div>
                @error('password')
                    <div id="password_error" class="invalid-feedback mt-0 mb-2">
                        {{ $message }}
                    </div>
                @else
                    <small class="text-danger"> <i class="fa fa-circle-info"></i> Isi jika ingin merubah password
                        akun</small>
                @enderror

                <div class="d-md-flex justify-content-md-end">
                    <a class="btn btn-sm btn-secondary mr-2" type="button" href="{{ route('user') }}">Batal</a>
                    <input class="btn btn-sm btn-primary" type="submit" value="Simpan perubahan">
                </div>
            </form>
        </div>
    </div>

    <script>
        $("#add_prodi_btn").click(function() {
            var input = `
                <div class="input-group mb-2">
                    <select name="prodi_id_auditor[]" class="custom-select select-prodi">
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
    </script>
@endsection
