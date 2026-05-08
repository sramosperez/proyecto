@extends('errors.error-template')

@section('error_title', 'Acceso denegado')
@section('error_code', 'ERROR 403')
@section('error_message', 'No tienes permisos para acceder a esta sección.')

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
