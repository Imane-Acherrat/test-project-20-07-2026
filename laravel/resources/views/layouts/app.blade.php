<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Factory Log Sifter')</title>
    <style>
        :root {
            --bg: #0f172a;
            --panel: #1e293b;
            --panel-light: #f8fafc;
            --border: #334155;
            --text: #e2e8f0;
            --muted: #94a3b8;
            --accent: #38bdf8;
            --warning: #f59e0b;
            --critical: #ef4444;
            --success: #22c55e;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
        }
        nav {
            background: var(--bg);
            color: white;
            padding: 14px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav .brand { font-weight: 700; font-size: 1.1rem; }
        nav a {
            color: #cbd5e1;
            text-decoration: none;
            margin-left: 18px;
            font-size: 0.95rem;
        }
        nav a:hover { color: white; }
        nav form { display: inline; }
        nav button.link {
            background: none;
            border: none;
            color: #cbd5e1;
            cursor: pointer;
            font-size: 0.95rem;
            margin-left: 18px;
        }
        nav button.link:hover { color: white; }
        main {
            max-width: 1100px;
            margin: 32px auto;
            padding: 0 20px;
        }
        h1 { font-size: 1.5rem; margin-bottom: 4px; }
        .card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 18px;
            text-align: center;
        }
        .stat-card .value { font-size: 1.8rem; font-weight: 700; color: var(--bg); }
        .stat-card .label { color: #64748b; font-size: 0.85rem; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 10px 12px; border-bottom: 1px solid #e2e8f0; font-size: 0.92rem; }
        th { color: #64748b; text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.05em; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 0.78rem; font-weight: 600; }
        .badge-critical { background: #fee2e2; color: #b91c1c; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-info { background: #e0f2fe; color: #0369a1; }
        .badge-open { background: #fee2e2; color: #b91c1c; }
        .badge-resolved { background: #dcfce7; color: #15803d; }
        .badge-not-important { background: #e2e8f0; color: #475569; }
        tr.row-critical { background: #fef2f2; }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            background: var(--bg);
            color: white;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover { opacity: 0.9; }
        .btn-danger { background: var(--critical); }
        .btn-sm { padding: 5px 10px; font-size: 0.8rem; }
        .btn-outline { background: white; color: var(--bg); border: 1px solid var(--border); }
        select, input[type=email], input[type=password], input[type=file] {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.95rem;
            margin-top: 4px;
        }
        label { font-weight: 600; font-size: 0.88rem; }
        .field { margin-bottom: 16px; }
        .alert-box {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 0.9rem;
        }
        .alert-success { background: #dcfce7; color: #15803d; }
        .alert-error { background: #fee2e2; color: #b91c1c; }
        .actions form { display: inline-block; margin-right: 6px; }
        .muted { color: #64748b; font-size: 0.85rem; }
    </style>
</head>
<body>
    @auth
    <nav>
        <span class="brand"> Factory Log Sifter</span>
        <div>
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('sensor-alerts.index') }}">Alerts</a>
            <a href="{{ route('sensor-alerts.import.show') }}">Import CSV</a>
            <span class="muted" style="margin-left: 18px;">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="link">Logout</button>
            </form>
        </div>
    </nav>
    @endauth

    <main>
        @if (session('status'))
            <div class="alert-box alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert-box alert-error">
                <ul style="margin:0; padding-left: 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
