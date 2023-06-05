<div class="row d-flex align-items-center">
    <ul class="breadcrumb">
        <li class="@if ($file->status_id == 1) active @else completed @endif">
            <a href="@can('auditor') {{ route('tilik_home') }} @endcan">
                <b>perubahan diizinkan</b><br>
                <br> <small> <i>{{ date('d-m-Y', strtotime($file->tahap[0]->updated_at)) }}</i> </small>
            </a>
        </li>

        <li class="@if ($file->status_id == 2) active @elseif($file->status_id == 3 || $file->status_id == 6 || $file->status_id == 7) completed @endif">
            <a href="@can('auditor') {{ route('tilik_home') }} @endcan">
                <b>ditinjau</b><br>
                <br> <small> <i>
                        @if ($file->status_id == 2 || $file->status_id == 3 || $file->status_id == 6 || $file->status_id == 7)
                            {{ date('d-m-Y', strtotime($file->tahap[1]->updated_at)) }}
                        @else
                            -
                        @endif
                    </i> </small>

            </a>
        </li>
        <li class="@if ($file->status_id == 3) active @elseif($file->status_id == 6 || $file->status_id == 7) completed @endif">
            <a href="@can('auditor') {{ route('tilik_home') }} @endcan">
                <b>berisi tilik</b><br>
                <br> <small> <i>
                        @if ($file->status_id == 3 || $file->status_id == 6 || $file->status_id == 7)
                            {{ date('d-m-Y', strtotime($file->tahap[2]->updated_at)) }}
                        @else
                            -
                        @endif
                    </i> </small>
            </a>
        </li>
        <li class="@if ($file->status_id == 6) active @elseif($file->status_id == 7) completed @endif">
            <a href="@can('auditor') {{ route('pasca_home') }} @endcan">
                <b>berisi komentar dan nilai <br>akhir</b>
                <br> <small> <i>
                        @if ($file->status_id == 6 || $file->status_id == 7)
                            {{ date('d-m-Y', strtotime($file->tahap[$file->tahap->count() - 1]->updated_at)) }}
                        @else
                            -
                        @endif
                    </i> </small>
            </a>
        </li>
        <li class="@if ($file->status_id == 7) active @endif">
            <a href="@can('auditor') {{ route('pasca_home') }} @endcan">
                <b>dikonfirmasi</b><br>
                <br> <small> <i>
                        @if ($file->status_id == 7)
                            {{ date('d-m-Y', strtotime($file->tahap[$file->tahap->count() - 1]->updated_at)) }}
                        @else
                            -
                        @endif
                    </i> </small>
            </a>
        </li>
    </ul>
</div>
