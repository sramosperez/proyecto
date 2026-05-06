@extends('layouts.app')

@section('title', 'Incidencia #' . $issue->id)

@section('content')
    @php
        $userRole = auth()->user()?->role?->name;
        $canUpdateIssue = in_array($userRole, ['Responsable', 'Dirección'], true);

        $maskSurname = function (?string $surname): string {
            $value = trim((string) $surname);
            if ($value === '') {
                return 'No disponible';
            }

            $parts = preg_split('/\s+/u', $value, -1, PREG_SPLIT_NO_EMPTY);
            if (!$parts) {
                return 'No disponible';
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

        $maskEmail = function (?string $email): string {
            if (!$email || !str_contains($email, '@')) {
                return 'No disponible';
            }
            [$local, $domain] = explode('@', $email, 2);
            $localVisible = substr($local, 0, 2);
            $localMasked = $localVisible . str_repeat('*', max(strlen($local) - strlen($localVisible), 2));
            $domainParts = explode('.', $domain);
            $domainName = $domainParts[0] ?? '';
            $domainTld = isset($domainParts[1]) ? '.' . implode('.', array_slice($domainParts, 1)) : '';
            $domainVisible = substr($domainName, 0, 1);
            $domainMasked = $domainVisible . str_repeat('*', max(strlen($domainName) - strlen($domainVisible), 2));
            return $localMasked . '@' . $domainMasked . $domainTld;
        };
    @endphp


    <div class="mb-4">
        <a href="{{ route('issues.index') }}" class="small fw-bold text-decoration-none text-dark">
            ← Volver al buscador
        </a>
    </div>

    @if (session('error'))
        <div class="login-error-banner mb-4">{{ session('error') }}</div>
    @endif

    @if (session('success'))
        <div class="alert border-0 fw-bold mb-4 p-3" style="background: #d1fae5; color: #065f46; border-radius: 0;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 0; max-width: 620px;">

        {{-- Cabecera de la tarjeta --}}
        <div class="card-header border-0 p-4" style="background: black; color: white;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0"
                        style="font-size: 0.68rem; letter-spacing: 0.15em; text-transform: uppercase; color: #9ca3af; font-weight: 700;">
                        Ref: {{ $issue->reference }}
                    </p>
                    <h2 class="fw-bold mb-0" style="font-family: monospace; font-size: 1.9rem;">
                        #{{ $issue->id }}
                    </h2>
                </div>
                <span
                    class="issue-status-badge {{ $issue->status === 'Open' ? 'issue-status-open' : 'issue-status-closed' }}">
                    {{ $issue->status === 'Open' ? 'PENDIENTE' : 'CERRADA' }}
                </span>
            </div>
        </div>

        <div class="card-body p-4">

            {{-- Descripción --}}
            <div class="mb-4">
                <p class="mb-1"
                    style="font-size: 0.68rem; letter-spacing: 0.12em; text-transform: uppercase; font-weight: 700; color: #6b7280;">
                    Descripción</p>
                <p class="fw-semibold fst-italic mb-0">"{{ $issue->description }}"</p>
            </div>

            {{-- Resolución sugerida --}}
            <div class="p-3 mb-4" style="background: #eff6ff; border-left: 3px solid #3b82f6;">
                <p class="mb-1"
                    style="font-size: 0.68rem; letter-spacing: 0.12em; text-transform: uppercase; font-weight: 700; color: #1d4ed8;">
                    Resolución sugerida</p>
                <p class="mb-0 fw-medium" style="color: #1e3a5f;">{{ $issue->resolution }}</p>
            </div>

            {{-- Nº pedido + Fecha --}}
            <div class="row g-3 mb-4 p-3" style="background: #f9fafb; border: 1px solid #e5e7eb;">
                <div class="col-12 col-sm-6">
                    <p class="mb-1">
                        Nº de pedido</p>
                    <p class="fw-semibold mb-0">{{ $issue->orderNumber ?: 'No disponible' }}</p>
                </div>
                <div class="col-12 col-sm-6">
                    <p class="mb-1"
                        style="font-size: 0.68rem; letter-spacing: 0.1em; text-transform: uppercase; font-weight: 700; color: #6b7280;">
                        Fecha de incidencia</p>
                    <p class="fw-semibold mb-0">
                        {{ $issue->createdAt ? \Illuminate\Support\Str::before(\Illuminate\Support\Str::before($issue->createdAt, 'T'), ' ') : 'No disponible' }}
                    </p>
                </div>
            </div>

            {{-- Datos del cliente --}}
            <div class="p-3 mb-4" style="background: #f9fafb; border: 1px solid #e5e7eb;">
                <p class="mb-2"
                    style="font-size: 0.68rem; letter-spacing: 0.1em; text-transform: uppercase; font-weight: 700; color: #6b7280;">
                    Datos de cliente</p>

                @if ($userRole === 'Empleado')
                    <div class="row g-2">
                        <div class="col-12 col-sm-6">
                            <p class="mb-0"
                                style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.08em; color: #6b7280; font-weight: 700;">
                                Nombre</p>
                            <p class="fw-medium mb-0">{{ $issue->name ?: 'No disponible' }}</p>
                        </div>
                        <div class="col-12 col-sm-6">
                            <p class="mb-0"
                                style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.08em; color: #6b7280; font-weight: 700;">
                                Apellido</p>
                            <p class="fw-medium mb-0">{{ $maskSurname($issue->surname) }}</p>
                        </div>
                    </div>
                @else
                    <div class="row g-2">
                        <div class="col-12 col-sm-6">
                            <p class="mb-0"
                                style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.08em; color: #6b7280; font-weight: 700;">
                                Nombre completo</p>
                            <p class="fw-medium mb-0">
                                {{ trim(($issue->name ?? '') . ' ' . ($issue->surname ?? '')) ?: 'No disponible' }}</p>
                        </div>
                        <div class="col-12 col-sm-6">
                            <p class="mb-0"
                                style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.08em; color: #6b7280; font-weight: 700;">
                                Email</p>
                            <p class="fw-medium mb-0">{{ $maskEmail($issue->customerEmail) }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Acciones --}}
            <div class="pt-3 border-top">
                @if ($issue->status === 'Closed')
                    <p class="mb-2"
                        style="font-size: 0.68rem; letter-spacing: 0.1em; text-transform: uppercase; font-weight: 700; color: #6b7280;">
                        Comentario registrado</p>
                    <textarea rows="3" readonly class="form-control mb-2"
                        style="border-radius: 0; background: #f3f4f6; border: none; resize: none; color: #4b5563;">{{ $issue->comment ?? 'Sin comentarios.' }}</textarea>
                    <p class="small text-muted mb-0">Esta incidencia está cerrada. Solo disponible para visualización.</p>
                @elseif($canUpdateIssue)
                    <form action="{{ route('issues.update', $issue->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <p class="mb-2"
                            style="font-size: 0.68rem; letter-spacing: 0.1em; text-transform: uppercase; font-weight: 700; color: #6b7280;">
                            Observaciones</p>
                        <textarea name="comment" rows="3" class="form-control mb-3"
                            style="border-radius: 0; border: none; border-bottom: 1px solid black; resize: none; background: #f9fafb;"
                            placeholder="Observaciones (opcional)...">{{ old('comment', $issue->comment ?? '') }}</textarea>
                        <div class="row g-2">
                            <div class="col-6">
                                <button type="submit" name="action" value="comment" class="btn w-100 fw-bold py-3"
                                    style="border-radius: 0; background: #e5e7eb; color: #374151; border: none;">
                                    AÑADIR COMENTARIO
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="submit" name="action" value="close"
                                    class="btn btn-custom w-100 fw-bold py-3">
                                    VALIDAR Y CERRAR
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <p class="mb-2"
                        style="font-size: 0.68rem; letter-spacing: 0.1em; text-transform: uppercase; font-weight: 700; color: #6b7280;">
                        Comentario</p>
                    <textarea rows="3" readonly class="form-control mb-2"
                        style="border-radius: 0; background: #f3f4f6; border: none; resize: none; color: #4b5563;">{{ $issue->comment ?? '' }}</textarea>
                    <p class="small text-muted mb-0">Tu perfil tiene acceso de solo lectura para esta incidencia.</p>
                @endif
            </div>

        </div>
    </div>
@endsection
