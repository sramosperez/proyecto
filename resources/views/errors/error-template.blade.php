@extends('layouts.app')

@section('title')
    @yield('error_title')
@endsection

@section('content')
    <div class="container-login d-flex justify-content-center align-items-start align-items-md-center py-5 my-5">
        <section class="login-card p-4 text-center">
            <h1 class="fs-4 detail-label mb-2">@yield('error_code')</h1>

            @hasSection('error_heading')
                <p class="fw-bold mb-3">@yield('error_heading')</p>
            @endif

            <p class="mb-4">@yield('error_message')</p>

            <div class="d-grid gap-2">
                @yield('error_primary_action')
                @yield('error_secondary_action')
            </div>
        </section>
    </div>
@endsection