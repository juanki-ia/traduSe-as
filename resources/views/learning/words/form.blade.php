@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ isset($word) ? 'Edit Word' : 'Create Word' }}</div>

                <div class="card-body">
                    <form action="{{ isset($word) ? route('words.update', [$category->id, $word->id]) : route('words.store', $category->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($word))
                            @method('PUT')
                        @endif

                        <div class="form-group">
                            <label for="name">Word Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', isset($word) ? $word->name : '') }}">
                        </div>

                        <div class="form-group">
                            <label for="gif_path">Video</label>
                            <input type="file" class="form-control" id="gif_path" name="gif_path">
                            @if(isset($word) && $word->gif_path)
                                <video width="320" height="240" autoplay loop muted>
                                    <source src="{{ asset('storage/videos/' . $category->name . '/' . $word->gif_path) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @endif
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">{{ isset($word) ? 'Edit' : 'Create' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
