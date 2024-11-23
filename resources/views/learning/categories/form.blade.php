@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ isset($category) ? 'Editar Sección' : 'Create Category' }}</div>

                    <div class="card-body">
                        <form action="{{ isset($category) ? route('categories.update', $category->id) : route('categories.store') }}" method="POST">
                            @csrf
                            @if(isset($category))
                                @method('PUT')
                            @endif

                            <div class="form-group">
                                <label for="name">Nombre de la Sección</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', isset($category) ? $category->name : '') }}">
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">{{ isset($category) ? 'Confirmar' : 'Crear' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

