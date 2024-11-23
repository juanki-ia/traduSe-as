@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 d-flex align-items-center justify-content-center" style="height: 80vh;">
            <div class="card w-100" style="box-shadow: 0 4px 8px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden;">
                
                <!-- Columna de Imagen -->
                <div class="image-section" style="flex: 1; background: url('{{ asset('images/imagen1.jpg') }}') no-repeat center center; background-size: cover;" aria-label="Imagen descriptiva">
                </div>

                <!-- Columna de Formulario -->
                <div class="form-section" style="flex: 1; background-color: #f8f9fa; padding: 2rem;">
                    <div class="card-header text-center" style="background-color: #000; color: white; font-size: 1.5rem;">
                        {{ __('Iniciar sesión') }}
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}" style="font-family: 'Arial';">
                            @csrf

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('Dirección de correo') }}</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Contraseña -->
                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('Contraseña') }}</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Recordarme -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    {{ __('Recordarme') }}
                                </label>
                            </div>

                            <!-- Botones -->
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="submit" class="btn btn-dark" style="padding: 10px 20px;">
                                    {{ __('Ingresar') }}
                                </button>
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}" style="color: #0056b3;">
                                        {{ __('¿Olvidaste tu contraseña?') }}
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<style>
    .image-section {
        display: none;
    }

    @media (min-width: 768px) {
        .image-section {
            display: block;
        }

        .card {
            display: flex;
            flex-direction: row;
        }
    }
</style>
@endsection
