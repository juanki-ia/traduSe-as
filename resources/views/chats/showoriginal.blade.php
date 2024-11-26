@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('scripts2')
    <script src="https://cdn.socket.io/4.7.5/socket.io.min.js"
        integrity="sha384-2huaZvOR9iDzHqslqwpR87isEmrfxqyWOF7hr7BY6KG0+hVKLoEXMPUJw3ynWuhO" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/control_utils/control_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/holistic/holistic.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-backend-wasm/dist/tf-backend-wasm.js"></script>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6" id="column_canvas" style="display: none;">
                <div class="card">
                    <!-- Controles para la cámara -->
                    <div id="loading" class="loading" style="display: none;">Cargando...</div>
                    <canvas id="output_canvas" class="output_canvas" width="640" height="480"
                        style="display: none;"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card chat-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Este es el chat {{ $chat->id }}</span>
                        <button id="toggle-camera" class="btn btn-secondary btn-camera-inactive">
                            <i id="camera-icon" class="fas fa-camera"></i>
                        </button>
                    </div>

                    <div class="card-body chat-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <!-- Mensajes del chat -->
                        @php
                            $previousDate = null;
                        @endphp
                        @foreach ($messages as $message)
                            @php
                                $currentDate = $message->created_at->format('Y-m-d');
                            @endphp
                            @if ($previousDate !== $currentDate)
                                <div class="date-separator">
                                    {{ \Carbon\Carbon::parse($currentDate)->translatedFormat('j \d\e F \d\e\l Y') }}
                                </div>
                                @php
                                    $previousDate = $currentDate;
                                @endphp
                            @endif

                            @if ($message->user_id === auth()->user()->id)
                                <div class="chat-message right">
                                    <div class="message-content">
                                        {{ $message->content }}
                                    </div>
                                    <span class="message-time">{{ $message->created_at->format('H:i') }}</span>
                                </div>
                            @else
                                <div class="chat-message left">
                                    <div class="message-content">
                                        {{ $message->content }}
                                    </div>
                                    <span class="message-time">{{ $message->created_at->format('H:i') }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Formulario de envío -->
                    <form id="chat-form" class="chat-form">
                        @csrf
                        <div class="input-group">
                            <textarea class="form-control" name="message" id="message" placeholder="Escribir..." rows="1"></textarea>
                            <button class="btn btn-primary" type="button">Enviar</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="module" src="{{ asset('js/script3.js') }}"></script>
    <script>
        const socket = io('http://18.119.98.140:8080/');
        // const socket = io('http://localhost:8080');

        document.getElementById('chat-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const messageInput = document.getElementById('message');
            const message = messageInput.value;
            const chatId = {{ $chat->id }};
            const token = document.querySelector('input[name="_token"]').value;

            fetch(`{{ route('chats.send', $chat->id) }}`, { // Lo mismo que AJAX
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        message: message
                    })
                })
                .then(response => response.json())
                .then(data => {
                    messageInput.value = '';
                    socket.emit('message', data.message);
                });
        });

        socket.on('message', function(data) {
            const chatBody = document.querySelector('.chat-body');
            const messageElement = document.createElement('div');
            messageElement.classList.add('chat-message', data.user_id === {{ Auth::id() }} ? 'right' : 'left');
            messageElement.innerHTML = `
                <div class="message-content">
                    ${data.content}
                </div>
                <span class="message-time">${new Date(data.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false })}</span>
            `;
            chatBody.appendChild(messageElement);
            chatBody.scrollTop = chatBody.scrollHeight;
        });

        window.onload = function() {
            const chatBody = document.querySelector('.chat-body');
            chatBody.scrollTop = chatBody.scrollHeight;
        }
    </script>
@endsection
