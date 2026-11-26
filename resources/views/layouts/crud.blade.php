<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - MedStock.id</title>
    <style>
        body{font-family:system-ui,sans-serif;margin:0;background:#f5f7f9;color:#1a2b33}
        .wrap{max-width:900px;margin:32px auto;padding:0 16px}
        .card{background:#fff;border-radius:10px;padding:24px;box-shadow:0 1px 3px #0001}
        table{width:100%;border-collapse:collapse}
        th,td{text-align:left;padding:10px;border-bottom:1px solid #e5e9ec}
        .btn{display:inline-block;padding:7px 14px;border-radius:6px;border:0;background:#0d9373;color:#fff;text-decoration:none;cursor:pointer;font-size:14px}
        .btn-gray{background:#6b7c85}.btn-red{background:#c0392b}
        input{width:100%;padding:8px;border:1px solid #c9d1d6;border-radius:6px;box-sizing:border-box}
        label{display:block;margin:14px 0 4px;font-weight:600}
        .ok{background:#e3f6ef;color:#0b6b52;padding:10px;border-radius:6px;margin-bottom:14px}
        .err{background:#fdeaea;color:#a12b20;padding:10px;border-radius:6px;margin-bottom:14px}
    </style>
</head>
<body><div class="wrap"><div class="card">
    <p><a href="{{ route('admin.dashboard') }}">&larr; Kembali ke dashboard</a></p>
    <h2>@yield('title')</h2>
    @if(session('success'))<div class="ok">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="err">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="err">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
    @yield('content')
</div></div></body>
</html>