<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Vidar Digital Accounting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --cream: #f0ece3;
            --gold: #b8962e;
            --gold-light: #d4af4a;
            --dark: #2c3328;
            --sidebar-bg: #1e2219;
            --sidebar-hover: #2c3328;
            --muted: #7a7060;
        }
        body {
            background: var(--cream);
            font-family: 'Inter', sans-serif;
            color: var(--dark);
        }
        .sidebar {
            min-height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid rgba(184,150,46,.15);
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: .6rem;
            text-decoration: none;
            padding: .25rem 0 1.25rem;
            border-bottom: 1px solid rgba(184,150,46,.2);
            margin-bottom: 1rem;
        }
        .sidebar-brand img {
            width: 36px;
            height: 36px;
            object-fit: contain;
        }
        .sidebar-brand-text {
            font-family: 'Playfair Display', serif;
            font-size: 1.05rem;
            color: #f0ece3;
            line-height: 1.15;
        }
        .sidebar-brand-text span { color: var(--gold); }
        .sidebar-brand-sub {
            font-size: .6rem;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: var(--gold);
            opacity: .7;
            display: block;
        }
        .sidebar .nav-link {
            color: #c8c0b0;
            border-radius: 2px;
            font-size: .875rem;
            padding: .5rem .75rem;
            letter-spacing: .02em;
            transition: all .15s;
        }
        .sidebar .nav-link:hover { background: var(--sidebar-hover); color: #fff; }
        .sidebar .nav-link.active {
            background: rgba(184,150,46,.18);
            color: var(--gold-light);
            border-left: 2px solid var(--gold);
        }
        .sidebar .nav-link i { opacity: .7; }
        .sidebar .nav-link.active i, .sidebar .nav-link:hover i { opacity: 1; }
        .sidebar hr { border-color: rgba(184,150,46,.15); }
        .sidebar .logout-btn {
            color: #c8c0b0;
            border-radius: 2px;
            font-size: .875rem;
            padding: .5rem .75rem;
            transition: all .15s;
            width: 100%;
            text-align: left;
            background: transparent;
            border: none;
        }
        .sidebar .logout-btn:hover { background: var(--sidebar-hover); color: #fff; }
        main { background: var(--cream); }
        .page-title {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            color: var(--dark);
            font-size: 1.5rem;
        }
        .card {
            border: none;
            border-radius: 2px;
            box-shadow: 0 1px 4px rgba(44,51,40,.07);
            background: #fff;
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #ede8df;
            font-size: .8rem;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 500;
        }
        .btn-primary {
            background: var(--gold);
            border-color: var(--gold);
            color: #fff;
            border-radius: 2px;
            letter-spacing: .06em;
            font-size: .85rem;
        }
        .btn-primary:hover, .btn-primary:focus {
            background: var(--gold-light);
            border-color: var(--gold-light);
        }
        .btn-outline-primary { color: var(--gold); border-color: var(--gold); border-radius: 2px; }
        .btn-outline-primary:hover { background: var(--gold); border-color: var(--gold); }
        .table th {
            font-size: .75rem;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 500;
            border-bottom-color: #ede8df;
        }
        .table td { border-color: #f5f0e8; }
        .badge.bg-success { background: #4a7c59 !important; }
        .badge.bg-warning { background: var(--gold) !important; color: #fff !important; }
        .badge.bg-danger  { background: #8b3a3a !important; }
        .form-control, .form-select {
            border-color: #e0d9cc;
            border-radius: 2px;
            background: #faf8f4;
            color: var(--dark);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(184,150,46,.12);
        }
        .form-label {
            font-size: .8rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 500;
        }
        .alert-success { background: #f0f5f1; border-color: #4a7c59; color: #2c4a36; }
        .alert-danger  { background: #f5f0f0; border-color: #8b3a3a; color: #5c2626; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar p-3">
            <a href="{{ route('dashboard') }}" class="sidebar-brand">
                <img src="{{ asset('images/vidar-logo.png') }}" alt="Vidar Digital Logo" onerror="this.style.display='none'">
                <div>
                    <div class="sidebar-brand-text">Vidar <span>Digital</span></div>
                    <span class="sidebar-brand-sub">Accounting</span>
                </div>
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

            <hr>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn"><i class="bi bi-box-arrow-right me-2"></i>Log out</button>
            </form>
        </nav>

        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="page-title mb-0">@yield('heading', 'Dashboard')</h1>
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
