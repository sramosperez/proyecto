@extends('errors.error-template')

@section('error_title', 'Error del servidor')
@section('error_code', 'ERROR 500')
@section('error_heading', 'ERROR INTERNO DEL SERVIDOR')
@section('error_message', 'Ha ocurrido un error inesperado. Intentalo de nuevo en unos minutos.')

@section('error_primary_action')
    <button type="button" class="btn btn-light fw-bold" onclick="window.location.reload()">
        REINTENTAR
    </button>
@endsection

@section('error_secondary_action')
    <a href="{{ url('/') }}" class="btn btn-custom fw-bold d-flex align-items-center justify-content-center">
        IR AL INICIO
    </a>
@endsection
