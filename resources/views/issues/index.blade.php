@extends('layouts.app')

@section('title', 'Incidencias')

@section('content')
    @php
        $userRole = auth()->user()?->role?->name;
        $isDirector = $userRole === 'Dirección';
    @endphp
    <div x-data="{ loading: false }">

        <div class="d-flex justify-content-center align-items-start align-items-md-center py-2">
            <div class="search-card p-4">
                <div class="mb-4">
                    <h1 class="h4 fw-bold mb-0">BUSCADOR</h1>
                    <p class="mt-3">Introduce el código de la incidencia</p>
                </div>

                {{-- Formulario de búsqueda --}}
                <form action="{{ route('issues.index') }}" method="GET" class="mb-4" @submit="loading = true">
                    <div class="d-flex gap-2 align-items-stretch">
                        <input type="number" name="search_id" class="form-control form-control-lg"
                            style="border: none; border-radius: 0; border-bottom: 2px solid black;" placeholder="000000"
                            value="{{ request('search_id') }}" inputmode="numeric" required>
                        <button type="submit" class="btn btn-custom px-4 fw-bold" :disabled="loading">
                            <span x-show="!loading">BUSCAR</span>
                            <span x-show="loading" x-cloak>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </span>
                        </button>
                    </div>
                </form>

                @if ($isDirector)
                    <div class="mb-4">
                        <a href="{{ route('issues.index', ['show_all' => 1]) }}" class="btn btn-sm fw-bold"
                            style="border-radius: 0; border: 1px solid black;">
                            VER TODAS
                        </a>
                    </div>
                @endif

                {{-- Mensajes de sesión --}}
                @if (session('error'))
                    <div class="login-error-banner mb-4">{{ session('error') }}</div>
                @endif

                @if (session('success'))
                    <div class="alert border-0 fw-bold mb-4 p-3"
                        style="background: #d1fae5; color: #065f46; border-radius: 0;">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Tarjeta de resumen --}}
                @isset($issue)
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0; max-width: 480px;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <p class="text-muted mb-0"
                                        style="font-size: 0.7rem; letter-spacing: 0.12em; text-transform: uppercase; font-weight: 700;">
                                        Ref: {{ $issue->reference }}
                                    </p>
                                    <h2 class="fw-bold mb-0" style="font-size: 1.8rem; font-family: monospace;">
                                        #{{ $issue->id }}
                                    </h2>
                                </div>
                                <span class="fw-bold px-3 py-1"
                                    style="border-radius: 0; font-size: 0.72rem; letter-spacing: 0.1em; text-transform: uppercase;
                                   background: {{ $issue->status === 'Open' ? '#fef3c7; color: #92400e' : '#dbeafe; color: #1e40af' }};">
                                    {{ $issue->status === 'Open' ? 'Abierta' : 'Cerrada' }}
                                </span>
                            </div>

                            <hr class="my-3">

                            <dl class="row mb-3 small">
                                @if ($userRole !== 'Empleado')
                                    <dt class="col-5 text-muted fw-bold"
                                        style="letter-spacing: 0.06em; text-transform: uppercase; font-size: 0.7rem;">Cliente
                                    </dt>
                                    <dd class="col-7 fw-semibold mb-2">{{ $issue->customerName ?: '—' }}</dd>
                                @endif
                                <dt class="col-5 text-muted fw-bold"
                                    style="letter-spacing: 0.06em; text-transform: uppercase; font-size: 0.7rem;">Fecha</dt>
                                <dd class="col-7 fw-semibold mb-0">{{ $issue->createdAt ?: '—' }}</dd>
                            </dl>

                            <a href="{{ route('issues.show', $issue->id) }}" class="btn btn-custom w-100 fw-bold">
                                VER INCIDENCIA COMPLETA
                            </a>
                        </div>
                    </div>
                @endisset
            </div>
        </div>
        {{-- Listado completo (solo Dirección) --}}
        @if (($showAll ?? false) && $isDirector)
            <div class="card border-0 shadow-sm" style="border-radius: 0;">
                <div class="card-header bg-light border-0 py-3">
                    <h2 class="h6 fw-bold mb-0 text-uppercase" style="letter-spacing: 0.1em;">Listado de incidencias
                    </h2>
                </div>

                @if (!empty($issues))
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 small">
                            <thead class="table-light">
                                <tr class="text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.08em;">
                                    <th class="fw-bold px-4 py-3">ID</th>
                                    <th class="fw-bold px-4 py-3">Referencia</th>
                                    <th class="fw-bold px-4 py-3">Estado</th>
                                    <th class="fw-bold px-4 py-3">Tienda</th>
                                    <th class="fw-bold px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($issues as $item)
                                    <tr>
                                        <td class="px-4 py-3 fw-semibold">#{{ $item->id }}</td>
                                        <td class="px-4 py-3">{{ $item->reference }}</td>
                                        <td class="px-4 py-3">
                                            <span class="fw-bold px-2 py-1"
                                                style="font-size: 0.68rem; border-radius: 0; text-transform: uppercase; letter-spacing: 0.06em;
                                                background: {{ $item->status === 'Open' ? '#fef3c7; color: #92400e' : '#dbeafe; color: #1e40af' }};">
                                                {{ $item->status === 'Open' ? 'Abierta' : 'Cerrada' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">{{ $item->storeCode ?: '—' }}</td>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('issues.show', $item->id) }}"
                                                class="fw-bold text-decoration-none" style="color: black;">
                                                Ver →
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="card-body text-muted">No hay incidencias para mostrar.</div>
                @endif
            </div>
        @endif

    </div>
@endsection
