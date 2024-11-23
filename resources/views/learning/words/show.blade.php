@extends('layouts.app')

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/control_utils/control_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/holistic/holistic.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-backend-wasm/dist/tf-backend-wasm.js"></script>
    <script type="module" src="{{ asset('js/script4.js') }}"></script>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <!-- Card para el video -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Categoría: {{ $category->name }}</span>
                    <a href="{{ URL::previous() }}" class="btn btn-primary btn-sm">Back</a>
                </div>
                <div class="card-body">
                    <div>
                        <video width="100%" autoplay loop muted>
                            <source src="{{ asset('storage/videos/' . $category->name . '/' . $word->gif_path) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    <h3 style="text-align: center;">{{ $word->name }}</h3>
                </div>
            </div>
        </div>

        <!-- Card para la detección -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Detection</span>
                    <button id="toggle-camera" class="btn btn-primary">Toggle Camera</button>
                </div>
                <br>
                <div id="loading">Loading...</div>
                    <canvas id="output_canvas" width="640" height="480" style="display: none;"></canvas>
                    <div id="sentence-container"></div>
                    {{-- <div style="margin-top: 15px;"></div>   --}}
                    {{-- <canvas id="output_canvas" class="output_canvas" width="640" height="480"
                        style="display: none;"></canvas> --}}
                    <br>
                    <textarea id="detected-words" readonly style="width: 100%; height: 30px; text-align: center;"></textarea>
                    {{-- <div style="margin-top: 20px;"></div>                --}}
            </div>
        </div>
    </div>
</div>
@endsection

