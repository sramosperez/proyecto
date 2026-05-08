@extends('errors.error-template')

@section('error_title', 'Sesion expirada')
@section('error_code', 'ERROR 419')
@section('error_heading', 'SESION EXPIRADA')
@section('error_message', 'Tu sesion ha expirado por inactividad. Vuelve a iniciar sesion para continuar.')

@section('error_primary_action')
    <a href="{{ route('login') }}" class="btn btn-custom fw-bold d-flex align-items-center justify-content-center">
        INICIAR SESION
    </a>
@endsection

@section('error_secondary_action')
    <button type="button" class="btn btn-light fw-bold" onclick="window.history.back()">
        VOLVER ATRAS
    </button>
@endsection
