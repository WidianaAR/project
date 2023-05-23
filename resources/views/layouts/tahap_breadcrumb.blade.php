<div class="row d-flex align-items-center">
    <ul class="breadcrumb">
        <li class="@if ($file->status_id == 1) active @else completed @endif">
            <a
                href="@if (Auth::user()->role_id != 4) @if ($file->kategori == 'evaluasi') {{ route('ed_home') }} @else {{ route('ks_home') }} @endif
@else
{{ route('tilik_home_auditor') }} @endif">
                <b>perubahan diizinkan</b><br>
                <br> <small> <i>{{ date('d-m-Y', strtotime($file->tahap[0]->created_at)) }}</i> </small>
            </a>
        </li>

        <li class="@if ($file->status_id == 2) active @elseif($file->status_id == 3 || $file->status_id == 6 || $file->status_id == 7) completed @endif">
            <a
                href="@if (Auth::user()->role_id == 4) {{ route('tilik_home_auditor') }} @else @if ($file->kategori == 'evaluasi') {{ route('ed_home') }} @else {{ route('ks_home') }} @endif @endif">
                <b>ditinjau</b><br>
                <br> <small> <i>
                        @if ($file->status_id == 2 || $file->status_id == 3 || $file->status_id == 6 || $file->status_id == 7)
                            {{ date('d-m-Y', strtotime($file->tahap[1]->created_at)) }}
                        @else
                            -
                        @endif
                    </i> </small>

            </a>
        </li>
        <li class="@if ($file->status_id == 3) active @elseif($file->status_id == 6 || $file->status_id == 7) completed @endif">
            <a
                href="@if (Auth::user()->role_id == 4) {{ route('tilik_home_auditor') }} @else {{ route('tilik_home') }} @endif">
                <b>berisi tilik</b><br>
                <br> <small> <i>
                        @if ($file->status_id == 3 || $file->status_id == 6 || $file->status_id == 7)
                            {{ date('d-m-Y', strtotime($file->tahap[2]->created_at)) }}
                        @else
                            -
                        @endif
                    </i> </small>
            </a>
        </li>
        <li class="@if ($file->status_id == 6) active @elseif($file->status_id == 7) completed @endif">
            <a
                href="@if (Auth::user()->role_id == 4) {{ route('pasca_home_auditor') }} @else {{ route('pasca_home') }} @endif">
                <b>berisi komentar dan nilai <br>akhir</b>
                <br> <small> <i>
                        @if ($file->status_id == 6 || $file->status_id == 7)
                            {{ date('d-m-Y', strtotime($file->tahap[$file->tahap->count() - 1]->created_at)) }}
                        @else
                            -
                        @endif
                    </i> </small>
            </a>
        </li>
        <li class="@if ($file->status_id == 7) active @endif">
            <a
                href="@if (Auth::user()->role_id == 4) {{ route('pasca_home_auditor') }} @else {{ route('pasca_home') }} @endif">
                <b>dikonfirmasi</b><br>
                <br> <small> <i>
                        @if ($file->status_id == 7)
                            {{ date('d-m-Y', strtotime($file->tahap[$file->tahap->count() - 1]->created_at)) }}
                        @else
                            -
                        @endif
                    </i> </small>
            </a>
        </li>
    </ul>
</div>
