@extends('errors.error-template')

@section('error_title', 'Pagina no encontrada')
@section('error_code', 'ERROR 404')
@section('error_heading', 'PAGINA NO ENCONTRADA')
@section('error_message', 'La pagina que buscas no existe o ha sido movida.')

@section('error_primary_action')
    <button type="button" class="btn btn-light fw-bold" onclick="window.history.back()">
        VOLVER ATRAS
    </button>
@endsection

@section('error_secondary_action')
    <a href="{{ url('/') }}" class="btn btn-custom fw-bold d-flex align-items-center justify-content-center">
        IR AL INICIO
    </a>
@endsection
