<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incidents - Login</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main>
        <div class="ui-brand">
            <span class="ui-brand-square" aria-hidden="true"></span>
            <span>Incidents</span>
        </div>

        <hr class="ui-divider">

        <h1 class="ui-title">Login</h1>

        @if ($errors->has('login'))
            <div class="ui-alert-error" role="alert">
                {{ $errors->first('login') }}
            </div>
        @endif

        <form
            action="{{ route('login') }}"
            method="POST"
            x-data="{ loading: false }"
            @submit="loading = true"
        >
            @csrf

            <label for="employee_id" class="ui-label">User</label>
            <input
                type="number"
                id="employee_id"
                name="employee_id"
                value="{{ old('employee_id') }}"
                placeholder="Employee ID"
                class="ui-field"
                required
                autofocus
                inputmode="numeric"
                autocomplete="username"
            >

            <label for="password" class="ui-label">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="******"
                class="ui-field"
                required
                autocomplete="current-password"
            >

            <button type="submit" class="ui-btn-primary" :disabled="loading">
                <span x-show="!loading">Login</span>
                <span x-show="loading" x-cloak>Cargando...</span>
            </button>
        </form>
    </main>
</body>
</html>
