<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incidents - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <nav class="navbar bg-white border-bottom shadow-sm">
        <div class="container d-flex justify-content-between align-items-center py-2">
            <div class="navbar-brand d-flex align-items-center">
                <i class="bi bi-map-fill p-2"></i>
                <a href="{{ url('/') }}" class="fs-5 fw-bold text-decoration-none text-dark">INCIDENCIAS</a>
            </div>
            <div class="d-flex align-items-center gap-3">
                @auth
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-circle me-2 fs-5"></i>
                        <span class="fw-semibold">{{ auth()->user()->name }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-light fw-bold px-3">
                            SALIR
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @yield('content')
    </main>
</body>

</html>
