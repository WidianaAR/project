<div class="row mb-4 px-3">
    <div class="col px-3 py-2 text-left rounded" style="background-color: #3D88C9">
        <a href="{{ route('ed_home') }}" class="decoration-none text-white">
            <h6>Evaluasi diri ({{ date('Y') }})</h6>
            <h4><b>{{ $file_ed }}</b> File</h4>
        </a>
    </div>

    <div class="col ml-2 px-3 py-2 text-left text-white rounded" style="background-color: #3D88C9">
        <a href="{{ route('ks_home') }}" class="decoration-none text-white">
            <h6>Ketercapaian standar ({{ date('Y') }})</h6>
            <h4><b>{{ $file_ks }}</b> File</h4>
        </a>
    </div>

    <div class="col mx-2 px-3 py-2 text-left text-white rounded" style="background-color: #D66B5A">
        <a href="@if (Auth::user()->role_id == 4) {{ route('tilik_home_auditor') }} @else {{ route('tilik_home') }} @endif"
            class="decoration-none text-white">
            <h6>Berisi tilik ({{ date('Y') }})</h6>
            <h4><b>{{ $file_tilik }}</b> File</h4>
        </a>
    </div>

    <div class="col mr-2 px-3 py-2 text-left text-white rounded" style="background-color: #EBB160">
        <a href="@if (Auth::user()->role_id == 4) {{ route('pasca_home_auditor') }} @else {{ route('pasca_home') }} @endif"
            class="decoration-none text-white">
            <h6>Berisi komentar & nilai akhir ({{ date('Y') }})</h6>
            <h4><b>{{ $file_kn }}</b> File</h4>
        </a>
    </div>

    <div class="col px-3 py-2 text-left text-white rounded" style="background-color: #76B969">
        <a href="@if (Auth::user()->role_id == 4) {{ route('pasca_home_auditor') }} @else {{ route('pasca_home') }} @endif"
            class="decoration-none text-white">
            <h6>Dikonfirmasi Auditor ({{ date('Y') }})</h6>
            <h4><b>{{ $file_conf }}</b> File</h4>
        </a>
    </div>

</div>
