@extends('layouts.app')

@section('title', 'Incidencia #' . $issue->id)

@section('content')
    @php
        $userRole = auth()->user()?->role?->name;

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
    @endphp


    @if (session('error'))
        <div class="login-error-banner mb-4">{{ session('error') }}</div>
    @endif

    @if (session('success'))
        <div class="alert border-0 fw-bold mb-4 p-3" style="background: #d1fae5; color: #065f46; border-radius: 0;">
            {{ session('success') }}
        </div>
    @endif

    <section class="detail-card mx-auto border-0 shadow-none">
        <article>
            <header class="detail-block border-top-0">
                <h1 class="fw-bold mb-0">INC.{{ $issue->id }}</h1>
            </header>
            <section class="detail-row">
                <div class="p-3" style="border-right: 1px solid var(--color-accent-dark);">
                    <p class="detail-label mb-1">FECHA</p>
                    <p class="fw-bold mb-0">
                        {{ $issue->createdAt ? \Illuminate\Support\Str::before(\Illuminate\Support\Str::before($issue->createdAt, 'T'), ' ') : 'No disponible' }}
                    </p>
                </div>
                <div class="p-3">
                    <p class="detail-label mb-1">CLIENTE</p>
                    <p class="fw-bold mb-0">{{ $issue->name . ' ' . $issue->surname }}</p>
                </div>
            </section>

            <section class="detail-row">
                <div class="p-3" style="border-right: 1px solid var(--color-accent-dark);">
                    <p class="detail-label mb-1">REFERENCIA DEL ARTÍCULO</p>
                    <p class="fw-bold mb-0">
                        {{ $issue->reference }}
                    </p>
                </div>
                <div class="p-3">
                    <p class="detail-label mb-1">PEDIDO Nº</p>
                    <p class="fw-bold mb-0">{{ $issue->orderNumber }}</p>
                </div>
            </section>

            <section class="detail-block">
                <p class="detail-label mb-2">DESCRIPCIÓN DE LA INCIDENCIA</p>
                <p class="mb-0">
                    {{ $issue->description }}
                </p>
            </section>

            <section class="detail-block">
                <p class="detail-label mb-2">RESOLUCIÓN</p>
                <p class="fw-bolder mb-0 text-uppercase"">
                    {{ $issue->resolution ?: $issue->description }}</p>
            </section>

            <section class="detail-block py-5 text-center">
                @if ($issue->status === 'Closed')
                    <div class="status-closed status mb-3 py-2">ESTA INCIDENCIA ESTA
                        CERRADA
                    </div>
                    <div x-data="{ showClosedNotice: false }">
                        <p class="detail-label mb-2 mt-3 text-start">COMENTARIOS</p>
                        <textarea rows="4" readonly class="detail-textarea" @focus="showClosedNotice = true"
                            @keydown.prevent="showClosedNotice = true" @paste.prevent="showClosedNotice = true">{{ $issue->comment ?? 'Sin comentarios adicionales.' }}</textarea>

                        <div x-show="showClosedNotice" x-cloak
                            class="alert alert-secondary mt-3 mb-0 text-start text-danger rounded-0" role="status"
                            aria-live="polite">
                            No puedes anadir comentarios, la incidencia esta cerrada.
                        </div>
                    </div>
                @else
                    <form action="{{ route('issues.update', $issue->id) }}" method="POST">
                        @csrf
                        @method('PATCH')


                        <div class="detail-row border-0 mb-4">
                            <button type="submit" name="action" value="close" class="btn-custom">
                                RESOLVER
                            </button>
                            <button type="submit" name="action" value="comment" class="btn-light">
                                NO RESOLVER
                            </button>
                        </div>

                        <p class="detail-label mb-2 text-start ms-1">COMENTARIOS</p>
                        <textarea name="comment" rows="4" class="detail-textarea" placeholder="Añadir comentarios adicionales...">{{ old('comment', $issue->comment ?? '') }}</textarea>

                        <button type="submit" name="action" value="comment" class="btn-custom mt-4 w-100">
                            ENVIAR
                        </button>
                    </form>
                @endif
            </section>
        </article>

        <div class="text-center py-5">
            <a href="{{ route('issues.index') }}" class="text-decoration-none text-reset letter-spacing fw-bolder">&larr;
                VOLVER ATRÁS</a>
        </div>
    </section>
@endsection
