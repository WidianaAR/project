<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Simjamu</title>
    <link href="/css/app.css" rel='stylesheet'>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"
        integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/js/all.min.js"></script>
</head>

<body>
    @if ($errors->get('token_error'))
        <div class="alert alert-danger" role="alert" id="msg-box">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ $errors->get('token_error') }}
        </div>
    @endif
    <div class="d-flex align-items-center flex-wrap text-center" id="login-bg">
        <div class="container-fluid d-flex justify-content-center">
            <div class="bg-white p-4" style="width: 30vw; border-radius: 40px">
                <div class="row">
                    <div class="col mb-4">
                        <i class="fa fa-key fa-2x" style="color: #0D64AC"></i>
                    </div>
                </div>
                <div class="row">
                    <div class="col form-group">
                        <div class="card border-0">
                            <form action="{{ route('reset_password_action') }}" method="POST">
                                @csrf
                                <h4>Ubah password</h4>
                                <small class="text-muted">Masukkan alamat email yang terdaftar dan password baru</small>
                                <input type="hidden" name="token" value="{{ $token }}">
                                <div class="card-body text-left px-0 pt-0 pb-2">

                                    <label class="mb-0 mt-3"><b>Email</b></label>
                                    <div class="p-2 d-flex w-100 div-input align-items-center"
                                        style="border-radius: 0.75rem">
                                        <i class="icon fa fa-sm fa-envelope" style="color: #b5b5b5"></i>
                                        <input
                                            class="form-control form-control-sm mx-2 py-0 input-field border-0 @error('email') is-invalid @enderror"
                                            type="text" name="email" aria-describedby="email_error" required>
                                    </div>
                                    @error('email')
                                        <div id="email_error" class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                    <label class="mb-0 mt-2"><b>Password baru</b></label>
                                    <div class="p-2 d-flex w-100 div-input align-items-center"
                                        style="border-radius: 0.75rem">
                                        <i class="icon fa fa-sm fa-lock" style="color: #b5b5b5"></i>
                                        <input
                                            class="form-control form-control-sm mx-2 py-0 input-field border-0 @error('password') is-invalid @enderror"
                                            type="password" name="password" id="password" minlength="8"
                                            aria-describedby="password_error" required>
                                        <div onclick="togglePassword()">
                                            <i id="icon-toggle" class="fa fa-sm fa-eye" style="color: #b5b5b5"></i>
                                        </div>
                                    </div>
                                    @error('password')
                                        <div id="password_error" class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                    <label class="mb-0 mt-2"><b>Konfirmasi password</b></label>
                                    <div class="p-2 d-flex w-100 div-input align-items-center"
                                        style="border-radius: 0.75rem">
                                        <i class="icon fa fa-sm fa-lock" style="color: #b5b5b5"></i>
                                        <input
                                            class="form-control form-control-sm mx-2 py-0 input-field border-0 @error('password_conf') is-invalid @enderror"
                                            type="password" name="password_conf" id="password_conf" minlength="8"
                                            aria-describedby="password_conf_error" required>
                                        <div onclick="togglePasswordConf()">
                                            <i id="icon-toggle-conf" class="fa fa-sm fa-eye" style="color: #b5b5b5"></i>
                                        </div>
                                    </div>
                                    @error('password_conf')
                                        <div id="password_conf_error" class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>
                                <div class="mt-1">
                                    <input class="blue-button btn btn-primary btn-sm" type="submit"
                                        value="Simpan password">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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

        function togglePasswordConf() {
            var x = $("#password_conf");
            if (x.attr("type") === "password") {
                x.attr("type", "text");
                $("#icon-toggle-conf").attr("style", "color: #0D64AC");
            } else {
                x.attr("type", "password");
                $("#icon-toggle-conf").attr("style", "color: #b5b5b5");
            }
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
</body>

</html>
