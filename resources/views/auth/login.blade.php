<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retail Support - Acceso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-sm">
       

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            @if ($errors->has('login'))
                <div class="bg-red-50 border-l-4 border-red-400 p-3 mb-6 rounded-r-xl text-red-800 text-sm font-medium">
                    {{ $errors->first('login') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="employee_id" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">
                        ID de empleado
                    </label>
                    <input
                        type="number"
                        id="employee_id"
                        name="employee_id"
                        value="{{ old('employee_id') }}"
                        required
                        autofocus
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-500 focus:bg-white transition-all text-lg"
                        placeholder="ID empleado"
                    >
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">
                        Contraseña
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-500 focus:bg-white transition-all text-lg"
                    >
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-bold hover:bg-indigo-700 transition-all shadow-lg active:scale-95 mt-2">
                    ENTRAR
                </button>
            </form>
        </div>
    </div>

</body>
</html>
