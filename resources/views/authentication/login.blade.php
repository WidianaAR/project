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
    @if ($errors->any())
        <div class="alert alert-danger" role="alert" id="msg-box">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif
    <div class="d-flex align-items-center flex-wrap" id="login-bg">
        <div class="container-fluid p-0">
            <div class="login-box pl-2 m-auto">
                <div class="login-left">
                    <img src="/images/Logo ITK.png" width="50px" height="35px">
                    <h4 class="mb-0 mt-2">Selamat Datang di</h4>
                    <p class="bold">Sistem Informasi Penjaminan Mutu Internal</p>
                    <p class="bold">Institut Teknologi Kalimantan</p>
                    <form action="{{ route('login_action') }}" method="POST">
                        @csrf
                        <label class="mb-0 mt-3"><b>Email</b></label>
                        <div class="p-2 d-flex w-100 div-input align-items-center" style="border-radius: 0.75rem">
                            <i class="icon fa fa-sm fa-envelope" style="color: #b5b5b5"></i>
                            <input class="form-control form-control-sm mx-2 py-0 input-field border-0" type="text"
                                name="email" required>
                        </div>

                        <label class="mb-0 mt-2"><b>Password</b></label>
                        <div class="p-2 d-flex w-100 div-input align-items-center" style="border-radius: 0.75rem">
                            <i class="icon fa fa-sm fa-lock" style="color: #b5b5b5"></i>
                            <input class="form-control form-control-sm mx-2 py-0 input-field border-0" type="password"
                                name="password" id="password" minlength="8" required>
                            <div onclick="togglePassword()">
                                <i id="icon-toggle" class="fa fa-sm fa-eye" style="color: #b5b5b5"></i>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col d-flex justify-content-end">
                                <a class="d-flex text-right" href="{{ route('forget_password') }}"><small>Lupa
                                        password?</small></a>
                            </div>
                        </div>
                        <div class="mt-4">
                            <input class="blue-button btn btn-primary btn-sm" type="submit" value="Login">
                        </div>
                    </form>
                </div>
                <div class="login-right">
                    <img src="images/itk.jpg" width="100%" height="100%">
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
    </script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
</body>

</html>
