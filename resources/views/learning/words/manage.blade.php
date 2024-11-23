@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <a href="{{ route('categories.manage') }}" class="btn btn-primary">Categorías</a>
                    <span>Words in {{ $category->name }}</span>
                    <a href="{{ route('words.create', $category->id) }}" class="btn btn-success">New Word</a>
                </div>

                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($words as $word)
                                <tr>
                                    <td>{{ $word->name }}</td>
                                    <td>
                                        <a href="{{ route('words.show', [$category->id, $word->id]) }}" class="btn btn-info btn-sm">View</a>
                                        <a href="{{ route('words.edit', [$category->id, $word->id]) }}" class="btn btn-primary btn-sm">Edit</a>
                                        <form action="{{ route('words.destroy', [$category->id, $word->id]) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this word?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

