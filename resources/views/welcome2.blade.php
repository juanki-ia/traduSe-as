<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->

    <!-- Todo esto es necesario para la detección de las señas -->
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/control_utils/control_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/holistic/holistic.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="css/styles2.css">
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-backend-wasm/dist/tf-backend-wasm.js"></script>
    <!-- Todo esto es necesario para la detección de las señas -->

</head>

<body class="antialiased">
    <div class="container">
        <video class="input_video" width="640" height="480" autoplay muted playsinline></video>
        <canvas class="output_canvas" width="640" height="480"></canvas>
    
        <div id="sentence-container"></div>
        <textarea id="detected-words" rows="4" cols="50" readonly></textarea>
        <button id="send-message">Send</button>
    
      </div>

      <!-- Este es el script.js principal para lenguaje de señas -->
      <script type="module" src="js/script2.js"></script>
</body>

</html>