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
            <div class="d-flex align-items-center">
                @auth
                    <div class="dropdown">
                        <button
                            class="btn pe-3 border-0 bg-transparent d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                            type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-circle me-2 fs-5"></i>
                                <span class="fw-semibold">{{ auth()->user()->name }}</span>
                            </div>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="userMenuDropdown">

                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item fw-bold d-flex align-items-center py-2">
                                        <i class="bi bi-box-arrow-right me-2"></i> SALIR
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <main class="py-5 px-0">
        @yield('content')
    </main>
</body>

</html>
