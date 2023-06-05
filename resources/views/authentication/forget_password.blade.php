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
    @if (Session::has('success'))
        <div class="alert alert-success" role="alert" id="msg-box">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ Session::get('success') }}
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
                            <form action="{{ route('forget_password_action') }}" method="POST">
                                @csrf
                                <h4>Lupa password ?</h4>
                                <small class="text-muted">Masukkan alamat email yang terdaftar untuk merubah
                                    password</small>
                                <div class="card-body text-left px-0 pt-0 pb-2">
                                    <label class="mb-0"><b>Email</b></label>
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
                                    <div class="mt-2">
                                        <input class="blue-button btn btn-primary btn-sm" type="submit"
                                            value="Reset password">
                                    </div>
                                    <div class="text-center mt-2">
                                        <a href="{{ route('login') }}"><i class="fa fa-sm fa-arrow-left"></i><small>
                                                Kembali ke halaman login</small></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
