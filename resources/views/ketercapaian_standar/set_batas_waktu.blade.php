@extends('layouts.navbar')
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ketercapaian Standar</title>
        <link rel="stylesheet" href="{{ URL::Asset('css/app.css') }}">

        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
        <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        @section('top-navbar')
            <li class="nav-item">
                <a class="nav-link">Set Batas Waktu Pengisian Ketercapaian Standar</a>
            </li>
        @endsection

        @section('isi')
            @if ($errors->any())
                <div class="alert alert-danger" role="alert" id="msg-box">
                    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif
            
            @include('ketercapaian_standar/countdown')

            <form action="{{ route('ks_set_time_action') }}" method="POST">
                @csrf
                <div class="col align-self-center shadow p-3 bg-body-tertiary rounded">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Atur Tanggal : </label>
                        <div class="col-sm-10">
                            @if ($deadline[0] != null)
                                <input type="text" name="id" value="{{ $deadline[1] }}" hidden>
                                <input type="text" name="date" id="datepicker" value="{{ date("Y-m-d",strtotime($deadline[0])) }}">
                            @else
                                <input type="text" name="date" id="datepicker">
                                <input type="text" name="id" value="{{ null }}" hidden>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Atur Waktu : </label>
                        <div class="col-sm-10">
                            @if ($deadline[0] != null)
                                <input type="text" name="time" id="timepicker" value="{{ date("H:i",strtotime($deadline[0])) }}">
                            @else
                                <input type="text" name="time" id="timepicker">
                            @endif
                        </div>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a class="btn btn-danger" style="margin-right: 10px" type="button" value="Batal" href="{{ URL('standar') }}">Batal</a>
                        <input class="btn btn-primary" type="submit" value="Simpan">
                    </div>
                </div>
            </form>

            <script>
                $('#datepicker').datepicker({
                    uiLibrary: 'bootstrap4',
                    format: 'yyyy-mm-dd'
                });

                $('#timepicker').timepicker({
                    uiLibrary: 'bootstrap4'
                });
            </script>
        @endsection
    </body>
</html>