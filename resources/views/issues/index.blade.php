<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retail Support - Gestión de Incidencias</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen">
    @php
        $userRole = auth()->user()?->role?->name;
        $canUpdateIssue = in_array($userRole, ['Responsable', 'Dirección'], true);
        $isDirector = $userRole === 'Dirección';

        $maskEmail = function (?string $email): string {
            if (! $email || ! str_contains($email, '@')) {
                return 'No disponible';
            }

            [$local, $domain] = explode('@', $email, 2);
            $localVisible = substr($local, 0, 2);
            $localMasked = $localVisible.str_repeat('*', max(strlen($local) - strlen($localVisible), 2));

            $domainParts = explode('.', $domain);
            $domainName = $domainParts[0] ?? '';
            $domainTld = isset($domainParts[1]) ? '.'.implode('.', array_slice($domainParts, 1)) : '';
            $domainVisible = substr($domainName, 0, 1);
            $domainMasked = $domainVisible.str_repeat('*', max(strlen($domainName) - strlen($domainVisible), 2));

            return $localMasked.'@'.$domainMasked.$domainTld;
        };
    @endphp

    <div class="bg-slate-900 text-white px-4 py-3 sticky top-0 z-50">
        <div class="max-w-3xl mx-auto flex justify-between items-center">
            <span class="text-sm font-medium">{{ auth()->user()->name ?? 'Usuario' }}</span>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-sm font-semibold hover:text-indigo-400 transition-colors">Cerrar sesión</button>
            </form>
        </div>
    </div>

    <div class="max-w-3xl mx-auto py-12 px-4">
        <header class="text-center mb-10">
    
            <p class="text-slate-500 mt-2 font-medium">Buscador y Validación de Incidencias en Tienda</p>
        </header>

        <section class="bg-white p-2 rounded-2xl shadow-sm border border-slate-200 mb-8">
            <form action="{{ route('issues.index') }}" method="GET" class="flex items-center gap-2">
                <input type="number" name="search_id" required placeholder="ID de incidencia (101 o 102)" 
                    class="flex-grow px-6 py-4 rounded-xl text-lg outline-none focus:bg-slate-50 transition-all" value="{{ request('search_id') }}">
                <button type="submit" class="bg-indigo-600 text-white px-8 py-4 rounded-xl font-bold hover:bg-indigo-700 transition-all shadow-lg active:scale-95">BUSCAR</button>
            </form>

            @if($isDirector)
                <div class="pt-3 px-1">
                    <a href="{{ route('issues.index', ['show_all' => 1]) }}" class="inline-block bg-slate-900 text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-slate-700 transition-all">
                        VER TODAS
                    </a>
                </div>
            @endif
        </section>

        @if(($showAll ?? false) && $isDirector)
            <section class="bg-white rounded-2xl shadow-sm border border-slate-200 mb-8 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-sm font-bold tracking-wider text-slate-600 uppercase">Listado de incidencias</h2>
                </div>

                @if(!empty($issues))
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-100 text-slate-600 uppercase tracking-wider text-xs">
                                <tr>
                                    <th class="text-left px-6 py-3">ID</th>
                                    <th class="text-left px-6 py-3">Referencia</th>
                                    <th class="text-left px-6 py-3">Estado</th>
                                    <th class="text-left px-6 py-3">Tienda</th>
                                    <th class="text-left px-6 py-3">Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($issues as $item)
                                    <tr class="border-t border-slate-100">
                                        <td class="px-6 py-3 font-semibold">#{{ $item->id }}</td>
                                        <td class="px-6 py-3">{{ $item->reference }}</td>
                                        <td class="px-6 py-3">
                                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $item->status === 'Open' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3">{{ $item->storeCode ?: '-' }}</td>
                                        <td class="px-6 py-3">
                                            <a href="{{ route('issues.index', ['search_id' => $item->id]) }}" class="text-indigo-600 font-semibold hover:underline">Ver detalle</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-6 text-slate-500">No hay incidencias para mostrar.</div>
                @endif
            </section>
        @endif

        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 mb-8 rounded-r-xl text-emerald-900 font-bold">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 mb-8 rounded-r-xl text-rose-900 font-bold">
                {{ session('error') }}
            </div>
        @endif

        @isset($issue)
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-200">
                <div class="bg-slate-900 px-8 py-6 flex justify-between items-center text-white">
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.2em] text-slate-400 font-bold mb-1">Ref: {{ $issue->reference }}</p>
                        <h2 class="font-mono text-2xl font-bold italic">#{{ $issue->id }}</h2>
                    </div>
                    <span class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider {{ $issue->status === 'Open' ? 'bg-amber-400 text-amber-950' : 'bg-blue-500 text-white' }}">
                        {{ $issue->status }}
                    </span>
                </div>
                
                <div class="p-8">
                    <div class="mb-6">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Descripción</h3>
                        <p class="text-lg text-slate-800 leading-tight font-semibold italic">"{{ $issue->description }}"</p>
                    </div>

                    <div class="mb-8 p-5 bg-indigo-50 rounded-2xl border border-indigo-100">
                        <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1">Resolución sugerida</h3>
                        <p class="text-indigo-900 font-medium">{{ $issue->resolution }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8 p-5 bg-slate-50 rounded-2xl border border-slate-200">
                        <div>
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Numero de pedido</h3>
                            <p class="font-semibold text-slate-800">{{ $issue->orderNumber ?: 'No disponible' }}</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Fecha de incidencia</h3>
                            <p class="font-semibold text-slate-800">{{ $issue->createdAt ?: 'No disponible' }}</p>
                        </div>
                    </div>

                    <div class="mb-8 p-5 bg-slate-50 rounded-2xl border border-slate-200">
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Datos de cliente</h3>

                        @if($userRole === 'Empleado')
                            <p class="text-sm text-slate-500">No tienes permisos para visualizar datos sensibles del cliente.</p>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Nombre</p>
                                    <p class="font-medium text-slate-800">{{ $issue->customerName ?: 'No disponible' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Email (enmascarado)</p>
                                    <p class="font-medium text-slate-800">{{ $maskEmail($issue->customerEmail) }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($issue->status === 'Closed')
                        <div class="space-y-4 pt-6 border-t border-slate-100">
                            <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Comentario registrado</p>
                            <textarea rows="3" readonly class="w-full p-4 bg-slate-100 border-2 border-slate-200 rounded-2xl text-slate-600">{{ $issue->comment ?? 'Sin comentarios.' }}</textarea>
                            <p class="text-sm text-slate-500">Esta incidencia esta cerrada. Solo disponible para visualizacion.</p>
                        </div>
                    @else
                        @if($canUpdateIssue)
                            <form action="{{ route('issues.update', $issue->id) }}" method="POST" class="space-y-4 pt-6 border-t border-slate-100">
                                @csrf
                                @method('PATCH')

                                <textarea name="comment" rows="3" class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-500 focus:bg-white text-slate-700" placeholder="Observaciones (opcional)...">{{ old('comment', $issue->comment ?? '') }}</textarea>

                                <div class="grid grid-cols-2 gap-4">
                                    <button type="submit" name="action" value="comment" class="bg-slate-200 text-slate-700 py-4 rounded-xl font-bold hover:bg-slate-300 transition-all">
                                        AÑADIR COMENTARIO
                                    </button>
                                    <button type="submit" name="action" value="close" class="bg-indigo-600 text-white py-4 rounded-xl font-bold hover:bg-indigo-700 transition-all shadow-lg active:scale-95">
                                        VALIDAR Y CERRAR
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="space-y-4 pt-6 border-t border-slate-100">
                                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Comentario</p>
                                <textarea rows="3" readonly class="w-full p-4 bg-slate-100 border-2 border-slate-200 rounded-2xl text-slate-600">{{ $issue->comment ?? '' }}</textarea>
                                <p class="text-sm text-slate-500">Tu perfil tiene acceso de solo lectura para esta incidencia.</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @endisset
    </div>
</body>
</html>