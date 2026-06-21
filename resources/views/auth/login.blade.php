<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in — Vidar Digital Accounting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --cream: #f0ece3;
            --gold: #b8962e;
            --gold-light: #d4af4a;
            --dark: #2c3328;
            --muted: #7a7060;
        }
        body {
            background: var(--cream);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-wrap { width: 100%; max-width: 400px; padding: 2rem 1rem; }
        .brand-logo { width: 80px; height: 80px; object-fit: contain; }
        .brand-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: var(--dark);
            letter-spacing: .02em;
        }
        .brand-title span { color: var(--gold); }
        .brand-sub {
            font-size: .8rem;
            letter-spacing: .2em;
            text-transform: uppercase;
            color: var(--gold);
            font-weight: 500;
        }
        .divider { width: 40px; height: 1px; background: var(--gold); margin: 1rem auto; opacity: .6; }
        .card { border: none; border-radius: 2px; background: #fff; box-shadow: 0 4px 32px rgba(44,51,40,.08); }
        .form-label {
            font-size: .8rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 500;
        }
        .form-control {
            border: 1px solid #e0d9cc;
            border-radius: 2px;
            background: var(--cream);
            color: var(--dark);
            font-size: .95rem;
        }
        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(184,150,46,.12);
            background: #fff;
        }
        .btn-gold {
            background: var(--gold);
            color: #fff;
            border: none;
            border-radius: 2px;
            letter-spacing: .1em;
            text-transform: uppercase;
            font-size: .85rem;
            font-weight: 500;
            padding: .7rem;
            transition: background .2s;
        }
        .btn-gold:hover { background: var(--gold-light); color: #fff; }
        .form-check-input:checked { background-color: var(--gold); border-color: var(--gold); }
        .text-muted { color: var(--muted) !important; }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="text-center mb-4">
        <img src="/images/vidar-logo.png" alt="Vidar Digital" class="brand-logo mb-3" onerror="this.style.display='none'">
        <div class="brand-title">Vidar <span>Digital</span></div>
        <div class="brand-sub mt-1">Accounting &amp; Invoicing</div>
        <div class="divider"></div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger py-2 mb-3" style="font-size:.875rem">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-check mb-4">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label for="remember" class="form-check-label" style="font-size:.875rem;color:var(--muted)">Remember me</label>
                </div>
                <button class="btn btn-gold w-100">Sign in</button>
            </form>
        </div>
    </div>

    <p class="text-center mt-4" style="font-size:.75rem;color:var(--muted);letter-spacing:.05em">
        &copy; {{ date('Y') }} Vidar Digital &middot; IT Park Uzbekistan
    </p>
</div>
</body>
</html>
