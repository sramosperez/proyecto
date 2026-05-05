@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="container-login d-flex justify-content-center align-items-start align-items-md-center py-5 my-5">

        <div class="login-card p-4">
            <h1 class="h3 text-start fw-bold mb-4">LOGIN</h1>

            <form class="form-login" action="{{ route('login') }}" method="POST" x-data="{ loading: false, employeeId: '{{ old('employee_id') }}', warn: false, t: null, showLoginError: {{ $errors->any() ? 'true' : 'false' }} }"
                @submit="$el.classList.add('was-validated'); if ($el.checkValidity()) loading = true">
                @csrf


                <div class="mb-3">
                    <label for="employee_id" class="form-label fw-bold">USER</label>
                    <input type="text" id="employee_id" name="employee_id" x-model="employeeId"
                        @focus="showLoginError = false"
                        @keydown="if ($event.key.length === 1 && !/^\d$/.test($event.key)) { $event.preventDefault(); warn = true; clearTimeout(t); t = setTimeout(() => warn = false, 1000) }"
                        @input="employeeId = $event.target.value = $event.target.value.replace(/\D/g, '')"
                        class="form-control @error('employee_id')
is-invalid
@enderror"
                        style="--bs-secondary-color: rgba(0, 0, 0, 0.25);" inputmode="numeric" pattern="[0-9]*"
                        placeholder="Introduce tu ID" required>
                    <div class="small mt-1" style="min-height: 1rem;">
                        <span x-show="warn" x-cloak x-transition.opacity.duration.150ms
                            class="text-danger fw-semibold">Número de empleado</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label fw-bold ">PASSWORD</label>
                    <input type="password" id="password" name="password" @focus="showLoginError = false"
                        class="form-control @error('password') is-invalid @enderror"
                        style="--bs-secondary-color: rgba(0, 0, 0, 0.25);" placeholder="••••••••" required>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-custom py-2 fw-bold" :disabled="loading">
                        <span x-show="!loading">LOGIN</span>
                        <span x-show="loading" x-cloak>
                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                            CARGANDO...
                        </span>
                    </button>
                </div>

                @if ($errors->any())
                    <div x-show="showLoginError" x-cloak class="login-error-banner mt-5 pt-4" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif
            </form>

        </div>
    </div>
@endsection
