<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Simjamu</title>
    <link href="/css/app.css" rel='stylesheet'>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"
        integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
</head>

<body>
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
                    <h2>Selamat Datang di</h2>
                    <p class="bold">Sistem Informasi Penjaminan Mutu Internal</p>
                    <p class="bold">Institut Teknologi Kalimantan</p>
                    <form action="{{ route('login_action') }}" method="POST">
                        @csrf
                        <input class="login-form p-2 mt-3" type="email" name="email" placeholder="Email" required>
                        <input class="login-form p-2 mt-3" type="password" name="password" placeholder="Password"
                            required>
                        <input class="blue-button btn btn-primary mt-3" type="submit" value="Login">
                    </form>
                </div>
                <div class="login-right">
                    <img src="{{ URL::asset('images/itk.jpg') }}" width="100%" height="100%">
                </div>
            </div>
        </div>
    </div>
</body>

</html>
