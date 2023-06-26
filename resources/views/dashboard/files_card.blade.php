<div class="row mb-4 px-3">
    <div class="col px-3 py-2 text-left border border-primary rounded bg-white">
        <a href="@cannot('auditor') {{ route('ed_home') }} @else {{ route('tilik_home') }} @endcannot"
            class="decoration-none">
            <h6 class="text-secondary">Instrumen simulasi akreditasi ({{ date('Y') }})</h6>
            <h4 class="text-primary"><b>{{ $file_ed }}</b> File</h4>
        </a>
    </div>

    <div class="col ml-2 px-3 py-2 text-left rounded bg-white border border-primary">
        <a href="@cannot('auditor') {{ route('ks_home') }} @else {{ route('tilik_home') }} @endcannot"
            class="decoration-none">
            <h6 class="text-secondary">Instrumen AMI ({{ date('Y') }})</h6>
            <h4 class="text-primary"><b>{{ $file_ks }}</b> File</h4>
        </a>
    </div>

    <div class="col mx-2 px-3 py-2 text-left rounded bg-white border border-primary">
        <a href="@can('auditor') {{ route('tilik_home') }} @endcan" class="decoration-none">
            <h6 class="text-secondary">Berisi tilik ({{ date('Y') }})</h6>
            <h4 class="text-primary"><b>{{ $file_tilik }}</b> File</h4>
        </a>
    </div>

    <div class="col mr-2 px-3 py-2 text-left rounded bg-white border border-primary">
        <a href="@can('auditor') {{ route('pasca_home') }} @endcan" class="decoration-none">
            <h6 class="text-secondary">Berisi komentar & nilai akhir ({{ date('Y') }})</h6>
            <h4 class="text-primary"><b>{{ $file_kn }}</b> File</h4>
        </a>
    </div>

    <div class="col px-3 py-2 text-left rounded bg-white border border-primary">
        <a href="@can('auditor') {{ route('pasca_home') }} @endcan" class="decoration-none">
            <h6 class="text-secondary">Dikonfirmasi Auditor ({{ date('Y') }})</h6>
            <h4 class="text-primary"><b>{{ $file_conf }}</b> File</h4>
        </a>
    </div>

</div>
