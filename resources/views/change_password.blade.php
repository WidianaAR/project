@extends('layouts.navbar')
@section('isi')
    <div class="row m-0">
        <div class="col pl-1">
            <h5>Profil</h5>
        </div>
        <span class="text-muted">Profil / <a href="">Ubah password akun</a></span>
    </div>

    <div class="element">
        <div class="add-form">
            <form action="{{ route('change_password_action') }}" method="POST">
                @csrf
                <input type="password" name="old_pass" class="form-control @error('old_pass') is-invalid mb-0 @enderror"
                    placeholder="Password lama" aria-describedby="old_pass_error" required>
                @error('old_pass')
                    <div class="invalid-feedback mb-2" id="old_pass_error">{{ $message }}</div>
                @enderror

                <input type="password" name="password" class="form-control @error('password') is-invalid mb-0 @enderror"
                    placeholder="Password baru" aria-describedby="password_error" required>
                @error('password')
                    <div class="invalid-feedback mb-2" id="password_error">{{ $message }}</div>
                @enderror

                <input type="password" name="conf_pass" class="form-control @error('conf_pass') is-invalid mb-0 @enderror"
                    placeholder="Konfirmasi password" aria-describedby="conf_pass_error" required>
                @error('conf_pass')
                    <div class="invalid-feedback mb-2" id="conf_pass_error">{{ $message }}</div>
                @enderror

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a class="btn btn-danger mr-2" type="button" value="Batal" href="{{ route('ks_chart') }}">Batal</a>
                    <input class="btn btn-primary" type="submit" value="Ubah">
                </div>
            </form>
        </div>
    </div>
@endsection
