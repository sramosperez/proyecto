@extends('layouts.app')

@section('title', 'Incidencias')

@section('content')
    @php
        $userRole = auth()->user()?->role?->name;
        $isDirector = $userRole === 'Dirección';
        $maskSurname = function (?string $surname): string {
            $value = trim((string) $surname);
            if ($value === '') {
                return '—';
            }

            $parts = preg_split('/\s+/u', $value, -1, PREG_SPLIT_NO_EMPTY);
            if (!$parts) {
                return '—';
            }

            $maskedParts = array_map(function (string $part): string {
                $visible = \Illuminate\Support\Str::substr($part, 0, 2);
                $length = \Illuminate\Support\Str::length($part);

                if ($length <= 2) {
                    return $part;
                }

                return $visible . str_repeat('*', max($length - 2, 2));
            }, $parts);

            return implode(' ', $maskedParts);
        };
    @endphp
    <div x-data="{ loading: false }">

        <div class="d-flex justify-content-center align-items-start align-items-md-center py-2">
            <div class="search-card p-4">
                <div class="mb-4">
                    <h1 class="h4 fw-bold mb-0">BUSCADOR</h1>
                    <p class="mt-3">INTRODUCE EL CÓDIGO DE LA INCIDENCIA</p>
                </div>

                <form action="{{ route('issues.index') }}" method="GET" class="form-search mb-4" @submit="loading = true">
                    <div class="d-flex gap-2 align-items-stretch">
                        <input type="number" name="search_id" class="form-control form-control-lg" placeholder="000000"
                            value="{{ isset($issue) ? '' : request('search_id') }}" inputmode="numeric" required>
                        <button type="submit" class="btn btn-custom px-4 fw-bold" :disabled="loading">
                            <span x-show="!loading">BUSCAR</span>
                            <span x-show="loading" x-cloak>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </span>
                        </button>
                    </div>
                </form>

                @if ($isDirector)
                    <div class="my-5">
                        <a href="{{ route('issues.index', ['show_all' => 1]) }}"
                            class="btn btn-light fw-bold d-inline-flex align-items-center justify-content-center">
                            VER INCIDENCIAS DE LA TIENDA {{ auth()->user()?->store_code ?? '—' }}
                        </a>
                    </div>
                @endif

                @if (session('error'))
                    <div class="login-error-banner mb-4">{{ session('error') }}</div>
                @endif

                @if (session('success'))
                    <div class="alert border-0 fw-bold mb-4 p-3 alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @isset($issue)
                    <div class="card my-5 py-5 border-0">
                        <div class="card-body py-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h2 class="fw-bold mb-0">
                                        {{ $issue->id }}
                                    </h2>

                                </div>
                                <span class="status {{ $issue->status === 'Open' ? 'status-open' : 'status-closed' }}">
                                    {{ $issue->status === 'Open' ? 'PENDIENTE' : 'CERRADA' }}
                                </span>
                            </div>

                            <hr class="my-3">

                            <dl class="row mb-3 small">
                                <dt class="col-5 issue-label">Nº de Pedido
                                </dt>
                                <dd class="col-7 fw-semibold mb-2">{{ $issue->orderNumber ?: '—' }}</dd>
                                <dt class="col-5  issue-label">Cliente
                                </dt>
                                <dd class="col-7 fw-semibold mb-2">
                                    @if ($userRole === 'Empleado')
                                        {{ trim(($issue->name ?? '') . ' ' . ($issue->surname ? $maskSurname($issue->surname) : '')) ?: '—' }}
                                    @else
                                        {{ trim(($issue->name ?? '') . ' ' . ($issue->surname ?? '')) ?: '—' }}
                                    @endif
                                </dd>
                                <dt class="col-5 issue-label">Fecha</dt>
                                <dd class="col-7 fw-semibold mb-0">
                                    {{ $issue->createdAt ? \Illuminate\Support\Str::before(\Illuminate\Support\Str::before($issue->createdAt, 'T'), ' ') : '—' }}
                                </dd>
                            </dl>

                            @if ($userRole !== 'Empleado')
                                <a href="{{ route('issues.show', $issue->id) }}"
                                    class="btn btn-custom w-100 fw-bold d-flex align-items-center justify-content-center mt-4">
                                    VER INCIDENCIA COMPLETA
                                </a>
                            @endif
                        </div>
                    </div>
                @endisset
            </div>
        </div>

        @if (($showAll ?? false) && $isDirector)
            <div class="d-flex justify-content-center py-2">
                <div class="list-card border-0 shadow-sm">
                    <div class="bg-light border-0 p-3">
                        <h2 class="fw-bold mb-0 fs-5">LISTADO DE INCIDENCIAS
                        </h2>
                    </div>

                    @if (count($issues) > 0)
                        <div class="d-flex flex-column">
                            @foreach ($issues as $item)
                                <a href="{{ route('issues.show', $item->id) }}"
                                    class="text-reset list-item text-decoration-none p-4 d-flex align-items-center justify-content-between gap-3">
                                    <div>
                                        <p class="fw-bold mb-2">Nº {{ $item->id ?: '—' }}</p>
                                        <p class="mb-2 text-muted">
                                            {{ $item->createdAt ? \Illuminate\Support\Str::upper(\Carbon\Carbon::parse($item->createdAt)->locale('es')->translatedFormat('F d, Y')) : 'SIN FECHA' }}
                                        </p>
                                        <p class="mb-2 issue-label fw-bold">
                                            {{ $item->name ? $item->name . ' ' . $item->surname : 'Sin nombre' }}
                                        </p>
                                        <p class="badge-light fw-bolder mb-0">EMPLEADO {{ $item->updatedBy ?: '—' }}</p>
                                    </div>

                                    <div
                                        class="list-status d-flex flex-column align-items-end align-self-stretch justify-content-between">
                                        <span
                                            class="status {{ $item->status === 'Open' ? 'status-open' : 'status-closed' }}">
                                            {{ $item->status === 'Open' ? 'PENDIENTE' : 'RESUELTA' }}
                                        </span>
                                        <span class="display-4 fw-bold" aria-hidden="true">›</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        @if (method_exists($issues, 'links'))
                            <div class="d-flex justify-content-center">
                                {{ $issues->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    @else
                        <div class="card-body text-muted">No hay incidencias para mostrar.</div>
                    @endif
                </div>
            </div>
        @endif

    </div>
@endsection
