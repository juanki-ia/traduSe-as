@extends('layouts.app')

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/control_utils/control_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/holistic/holistic.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-backend-wasm/dist/tf-backend-wasm.js"></script>
    <script type="module" src="{{ asset('js/script6.js') }}"></script>
    
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <!-- Demo para reconocimiento de gestos en imágenes -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Categoría: {{ $category->name }}</span>
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

        <!-- Demo para detección continua de gestos con la cámara web -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Detection</span>
                    <button id="webcamButton" class="btn btn-primary">
                        <span class="mdc-button__ripple"></span>
                        <span class="mdc-button__label">Toggle Camera</span>
                    </button>
                </div>
                <br>
                <div class="card-body p-0">
                    <div id="liveView" class="videoView">
                        <div style="position: relative;">
                            <video id="webcam" autoplay playsinline></video>
                            <canvas class="output_canvas" id="output_canvas" style="position: absolute; left: 0px; top: 0px;"></canvas>
                            {{-- <p id='gesture_output' class="output"></p> --}}
                        
                            <textarea id='gesture_output'  readonly style="width: 100%; height: 30px; text-align: center;"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
