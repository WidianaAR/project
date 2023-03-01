<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Simjamu</title>
        <link href="{{ URL::asset('css/app.css') }}" rel='stylesheet'>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    </head>
    <body>
        @error('login_gagal')
            <div class="alert alert-danger" role="alert" id="msg-box">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                {{ $message }}
            </div>
        @enderror
        <div class="d-flex align-items-center flex-wrap" id="wrapper">
            <div class="container-fluid">
                <div class="login-box">
                    <div class="login-left">
                        <img src="{{ URL::asset('images/Logo ITK.png') }}" width="50px" height="35px">
                        <h2>Selamat Datang di</h2>
                        <p class="bold">Sistem Informasi Penjaminan Mutu Internal</p>
                        <p class="bold">Institut Teknologi Kalimantan</p>
                        <form action="{{ route('login_action') }}" method="POST">
                            @csrf
                            <input class="login-form" type="email" name="email" placeholder="Email">
                            <input class="login-form" type="password" name="password" placeholder="Password">
                            <input class="blue-button btn btn-primary" type="submit" value="Login" style="margin-top: 7%">
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