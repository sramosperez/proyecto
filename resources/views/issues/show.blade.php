@extends('layouts.app')

@section('title', 'Incidencia #' . $issue->id)

@section('content')
    @if (session('error'))
        <div class="login-error-banner mb-4">{{ session('error') }}</div>
    @endif

    <section class="detail-card issue-detail-card mx-auto border-0 shadow-none">
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
                <div class="p-3 pb-0">
                    <p class="detail-label mb-1">CLIENTE</p>
                    <p class="fw-bold mb-0">{{ $issue->name . ' ' . $issue->surname }}</p>
                    <p class="mt-1">{{ $issue->email }}</p>

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

            <section class="detail-block py-3 py-md-5 text-center">
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
                    <form action="{{ route('issues.update', $issue->id) }}" method="POST" x-data="{ showCloseConfirm: false }">
                        @csrf
                        @method('PATCH')


                        <div class="detail-row border-0 mb-4">
                            <button type="button" class="btn-custom" @click="showCloseConfirm = true">
                                CERRAR INCIDENCIA
                            </button>
                            <button type="button" class="btn-light" onclick="window.history.back()">
                                SALIR SIN MODIFICAR
                            </button>
                        </div>

                        <div x-show="showCloseConfirm" x-cloak class="confirm-modal" @click.self="showCloseConfirm = false"
                            @keydown.escape.window="showCloseConfirm = false">
                            <div class="confirm-dialog text-start">

                                <p class="text-center mb-3 fw-bolder fs-5">¿Seguro de cerrar incidencia?</p>

                                <div class="d-grid gap-2">
                                    <button type="submit" name="action" value="close" class="btn-custom w-100">
                                        SÍ, CERRAR INCIDENCIA
                                    </button>
                                    <button type="button" class="btn-light w-100" @click="showCloseConfirm = false">
                                        CANCELAR
                                    </button>
                                </div>
                                <p class=" small text-muted mb-0 mt-2">Esta acción no se puede deshacer.</p>
                                <p class="small text-muted my-0">No se podrán añadir más comentarios.</p>
                            </div>
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

        <div class="text-center py-3 py-md-5">
            <a href="{{ route('issues.index') }}" class="text-decoration-none text-reset letter-spacing fw-bolder">&larr;
                VOLVER ATRÁS</a>
        </div>
    </section>
@endsection
