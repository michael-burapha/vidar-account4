<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Vidar Digital Accounting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f5f6f8; }
        .sidebar { min-height: 100vh; background: #0f172a; }
        .sidebar .nav-link { color: #cbd5e1; border-radius: .375rem; }
        .sidebar .nav-link:hover { background: #1e293b; color: #fff; }
        .sidebar .nav-link.active { background: #2563eb; color: #fff; }
        .brand { color: #fff; font-weight: 700; letter-spacing: .04em; }
        .card { border: none; box-shadow: 0 1px 2px rgba(15,23,42,.06); }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar p-3">
            <a href="{{ route('dashboard') }}" class="brand d-block fs-5 mb-4 text-decoration-none">
                <i class="bi bi-cash-coin me-1"></i> Vidar Digital
            </a>
            <ul class="nav flex-column gap-1">
                @php $r = request()->route()->getName(); @endphp
                <li><a class="nav-link {{ $r === 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li><a class="nav-link {{ str_starts_with($r, 'invoices') ? 'active' : '' }}" href="{{ route('invoices.index') }}"><i class="bi bi-receipt me-2"></i>Invoices</a></li>
                <li><a class="nav-link {{ str_starts_with($r, 'clients') ? 'active' : '' }}" href="{{ route('clients.index') }}"><i class="bi bi-people me-2"></i>Clients</a></li>
                <li><a class="nav-link {{ str_starts_with($r, 'bank-accounts') ? 'active' : '' }}" href="{{ route('bank-accounts.index') }}"><i class="bi bi-bank me-2"></i>Bank Accounts</a></li>
                <li><a class="nav-link {{ str_starts_with($r, 'exchange-rates') ? 'active' : '' }}" href="{{ route('exchange-rates.index') }}"><i class="bi bi-currency-exchange me-2"></i>Exchange Rates</a></li>
                <li><a class="nav-link {{ str_starts_with($r, 'settings') ? 'active' : '' }}" href="{{ route('settings.edit') }}"><i class="bi bi-gear me-2"></i>Settings</a></li>
            </ul>
            <hr class="text-secondary">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="nav-link border-0 bg-transparent text-start w-100"><i class="bi bi-box-arrow-right me-2"></i>Log out</button>
            </form>
        </nav>

        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3 mb-0">@yield('heading', 'Dashboard')</h1>
                <div>@yield('actions')</div>
            </div>

            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
