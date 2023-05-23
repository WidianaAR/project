@extends('layouts.navbar')

@section('isi')
    @if ($errors->any())
        <div class="alert alert-danger" role="alert" id="msg-box">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif

    <div class="row align-items-center">
        @if ($deadline[0])
            <div class="col-auto pr-0">
                Batas akhir upload file :
            </div>
            <div class="col text-left">
                @include('ketercapaian_standar/countdown')
            </div>
        @else
            <div class="col">
                <h5>Ketercapaian standar</h5>
            </div>
        @endif
        <div class="col text-right">
            <span class="text-muted">Ketercapaian standar / <a href="">Atur deadline</a></span>
        </div>
    </div>

    <div class="element row justify-content-center">
        <div class="add-form col-6">
            <form action="{{ route('ks_set_time_action') }}" method="POST">
                @csrf
                <label class="mb-1">Tanggal</label>
                @if ($deadline[0])
                    <input type="text" name="id" value="{{ $deadline[1] }}" hidden>
                    <input type="date" name="date" value="{{ date('Y-m-d', strtotime($deadline[0])) }}"
                        class="w-100 form-control" required>
                @else
                    <input type="text" name="id" value="" hidden>
                    <input type="date" name="date" class="w-100 form-control" required>
                @endif

                <label class="mb-1">Waktu</label>
                @if ($deadline[0])
                    <input type="time" name="time" value="{{ date('H:i', strtotime($deadline[0])) }}"
                        class="w-100 form-control" required>
                @else
                    <input type="time" name="time" class="w-100 form-control" required>
                @endif

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a class="btn btn-sm btn-secondary mr-2" type="button" value="Batal"
                        href="{{ URL('standar') }}">Batal</a>
                    <input class="btn btn-sm btn-primary" type="submit" value="Atur deadline">
                </div>
            </form>
        </div>
    </div>
@endsection
