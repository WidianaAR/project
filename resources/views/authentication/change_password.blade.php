@extends('layouts.navbar')
@section('title')
    <title>Ubah password</title>
@endsection
@section('isi')
    <div class="row m-0">
        <div class="col pl-1">
            <h5>Profil</h5>
        </div>
        <span class="text-muted">Profil / <a href="">Ubah password akun</a></span>
    </div>

    <div class="element row justify-content-center">
        <div class="add-form col-6">
            <form action="{{ route('change_password_action') }}" method="POST">
                @csrf
                <label class="mb-1" class="form-label">Password lama</label>
                <div
                    class="d-flex w-100 div-input align-items-center py-1 pl-0 pr-2 form-control form-control-sm @error('old_pass') is-invalid mb-0 @enderror">
                    <input class="mx-2 input-field border-0" type="password" name="old_pass" id="old_pass"
                        aria-describedby="old_pass_error" required>
                    <div onclick="toggleOldPassword()">
                        <i id="icon-toggle-old" class="fa fa-eye fa-sm" style="color: #b5b5b5"></i>
                    </div>
                </div>
                @error('old_pass')
                    <div class="invalid-feedback mb-2" id="old_pass_error">{{ $message }}</div>
                @enderror

                <label class="mb-1" class="form-label">Password baru</label>
                <div
                    class="d-flex w-100 div-input align-items-center py-1 pl-0 pr-2 form-control form-control-sm @error('password') is-invalid mb-0 @enderror">
                    <input class="mx-2 input-field border-0" type="password" name="password" id="password"
                        aria-describedby="password_error" required>
                    <div onclick="togglePassword()">
                        <i id="icon-toggle" class="fa fa-eye fa-sm" style="color: #b5b5b5"></i>
                    </div>
                </div>
                @error('password')
                    <div class="invalid-feedback mb-2" id="password_error">{{ $message }}</div>
                @enderror

                <label class="mb-1" class="form-label">Konfirmasi password baru</label>
                <div
                    class="d-flex w-100 div-input align-items-center py-1 pl-0 pr-2 form-control form-control-sm @error('conf_pass') is-invalid mb-0 @enderror">
                    <input class="mx-2 input-field border-0" type="conf_pass" name="conf_pass" id="conf_pass"
                        aria-describedby="conf_pass_error" required>
                    <div onclick="toggleConfPassword()">
                        <i id="icon-toggle-conf" class="fa fa-eye fa-sm" style="color: #b5b5b5"></i>
                    </div>
                </div>
                @error('conf_pass')
                    <div class="invalid-feedback mb-2" id="conf_pass_error">{{ $message }}</div>
                @enderror

                <div class="d-md-flex justify-content-md-end">
                    <a class="btn btn-sm btn-secondary mr-2" type="button" value="Batal"
                        href="{{ route('ks_chart') }}">Batal</a>
                    <input class="btn btn-sm btn-primary" type="submit" value="Simpan">
                </div>
            </form>
        </div>
    </div>

    <script>
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

        function toggleOldPassword() {
            var x = $("#old_pass");
            if (x.attr("type") === "password") {
                x.attr("type", "text");
                $("#icon-toggle-old").attr("style", "color: #0D64AC");
            } else {
                x.attr("type", "password");
                $("#icon-toggle-old").attr("style", "color: #b5b5b5");
            }
        }

        function toggleConfPassword() {
            var x = $("#conf_pass");
            if (x.attr("type") === "password") {
                x.attr("type", "text");
                $("#icon-toggle-conf").attr("style", "color: #0D64AC");
            } else {
                x.attr("type", "password");
                $("#icon-toggle-conf").attr("style", "color: #b5b5b5");
            }
        }
    </script>
@endsection
