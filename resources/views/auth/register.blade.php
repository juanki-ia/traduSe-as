@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 d-flex align-items-center justify-content-center" style="height: 80vh;">
            <div class="card w-100" style="box-shadow: 0 4px 8px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden;">
                
                <!-- Columna de Imagen -->
                <div class="image-section" style="flex: 1; background: url('{{ asset('images/imagen2.png') }}') no-repeat center center; background-size: cover;" aria-label="Imagen descriptiva">
                </div>

                <!-- Columna de Formulario -->
                <div class="form-section" style="flex: 1; background-color: #f8f9fa; padding: 2rem;">
                    <div class="card-header text-center" style="background-color: #28a745; color: white; font-size: 2.5rem; font-weight: bold;">
                        {{ __('Registrarse') }}
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('register') }}" style="font-family: 'Arial';">
                            @csrf

                            <!-- Nombre -->
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('Nombre') }}</label>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('Dirección de correo') }}</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Contraseña -->
                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('Contraseña') }}</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div class="mb-3">
                                <label for="password-confirm" class="form-label">{{ __('Confirmar Contraseña') }}</label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>

                            <!-- Botón de registro -->
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="submit" class="btn btn-dark" style="padding: 10px 20px;">
                                    {{ __('Registrarse') }}
                                </button>
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
