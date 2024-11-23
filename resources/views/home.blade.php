@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-11">
                <div class="card">
                    <div class="card-header"><b>Chats</b></div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <div class="d-flex justify-content-between mb-1">
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#exampleModal">
                                Nuevo Chat
                            </button>

                            <!-- New Button -->
                            <button type="button" class="btn btn-dark" data-bs-toggle="modal"
                                data-bs-target="#exampleModal1">
                                Añadir Chat
                            </button>

                            <!-- Modal Create Table -->
                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Nuevo Chat</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form method="POST" action="{{route('chats.store')}}">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="name" name="name"
                                                        placeholder="Ingrese un nombre para su pizarra..." required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cerrar</button>
                                                <button type="submit" class="btn btn-primary">Crear Chat</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Add Table -->
                            <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel"><b>Añadir Chat</b></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form method="POST" action="{{route('chats.add')}}">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="codigo" name="codigo"
                                                        placeholder="Ingrese el código del Chat." required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cerrar</button>
                                                <button type="submit" class="btn btn-primary">Agregar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cards to display chats -->
                        <div class="row">
                            @foreach ($chats as $chat)
                                <div class="col-md-4 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $chat->name }}</h5>
                                            <p class="card-text">Código: {{ $chat->code }}</p>
                                            <a href="{{route('chats.show',$chat->id)}}" class="btn btn-success">Chatear</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="notification" class="notification"></div>
@endsection

@section('scripts')
    <script src="{{ asset('js/datatable.js') }}"></script>

    @if (session('add') == 'ok')
        <script>
            Swal.fire(
                'Añadido correctamente',
                'El Chat: {{ session('name') }} ha sido añadido correctamente.',
                'success'
            )
        </script>
    @endif

    @if (session('create') == 'ok')
    <script>
        Swal.fire(
            'Creado correctamente',
            'El Chat: {{ session('name') }} ha sido creado correctamente.',
            'success'
        )
    </script>
@endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "{{ session('error') }}",
            });
        </script>
    @endif
@endsection

