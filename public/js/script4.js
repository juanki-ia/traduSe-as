import { updateSentenceDisplay, textToSpeech, formatSentences, thereHand, extractKeypoints, drawGuidelines } from './helpers.js';

const canvasElement = document.getElementById('output_canvas');
const canvasCtx = canvasElement.getContext('2d');

// Selecciona el elemento HTML donde deseas mostrar las palabras de la oración
const sentenceContainer = document.getElementById('sentence-container');
let sentenceList = document.createElement('div'); // Creamos un contenedor para las palabras
sentenceContainer.appendChild(sentenceList);

const detectedWordsElement = document.getElementById('detected-words');
const sendMessageButton = document.getElementById('send-message');
const toggleCameraButton = document.getElementById('toggle-camera');

let camera = null;
let kpSequence = []; // Variable para almacenar la secuencia de keypoints
let countFrame = 0;
let actions = ["buenos dias", "como estas", "enviar mensaje", "estoy bien", "hola", "mio", "no", "nombre", "por favor"];
let repeSent = 1;
let sentence = [];
const threshold = 0.7;
const MAX_LENGTH_FRAMES = 15;
const MIN_LENGTH_FRAMES = 5;

const loadingElement = document.getElementById('loading');

tf.setBackend('wasm').then(async () => {
    // Carga del modelo TensorFlow.js
    const model = await tf.loadLayersModel('/models/model.json');

    // Ocultar el indicador de carga y mostrar los elementos de la cámara
    loadingElement.style.display = 'none';
    canvasElement.style.display = 'block';

    // Función para cargar los keypoints desde un archivo JSON
    /*
    async function loadKeypoints(filename) {
        const response = await fetch(filename);
        const keypoints = await response.json();
        return keypoints;
    }
    */

    // Cargar los keypoints desde el archivo exportado
    ///const keypointsSequence = await loadKeypoints('keypoints_sequence.json');
    
    // Realizar la predicción con los keypoints cargados
    /*
    const inputTensor = tf.tensor([keypointsSequence]);
    const prediction = model.predict(inputTensor);
    const res = prediction.dataSync();
    console.log('Resultados de la predicción:', res);
    */


    // Función que se llama cada vez que se obtienen resultados del modelo Holistic.
    function onResults(results) {
        canvasCtx.save(); // Guarda el estado actual del contexto del lienzo.
        canvasCtx.clearRect(0, 0, canvasElement.width, canvasElement.height); // Borra el contenido del lienzo.

        // Dibujar la imagen original en el canvas
        canvasCtx.drawImage(results.image, 0, 0, canvasElement.width, canvasElement.height);

        // Actualizar la secuencia de keypoints
        const newKeypoints = extractKeypoints(results);
        kpSequence.push(newKeypoints);

        // Realizar la evaluación del modelo
        if (kpSequence.length > MAX_LENGTH_FRAMES && thereHand(results)) {
            countFrame += 1;
            console.log("la cantidad de frames es:", countFrame);
        } else {
            if (countFrame >= MIN_LENGTH_FRAMES) {
                const lastKeypoints = kpSequence.slice(-MAX_LENGTH_FRAMES); // Obtener los últimos MAX_LENGTH_FRAMES keypoints
                const inputTensor = tf.tensor([lastKeypoints]); // Crear un tensor con los keypoints
                const prediction = model.predict(inputTensor); // Realizar la predicción con el modelo TensorFlow.js
                const res = prediction.dataSync(); // Obtener los resultados de la predicción
                console.log('Resultados de la predicción:', res);

                if (Math.max(...res) > threshold) {
                    const maxIndex = res.indexOf(Math.max(...res));
                    const sent = actions[maxIndex];
                    textToSpeech(sent);

                        sentence.unshift(sent);
                        [sentence, repeSent] = formatSentences(sent, sentence, repeSent);
                        clearWord();
                        // Agregar la palabra detectada al área de texto
                        detectedWordsElement.value += sent + ' ';
                    
                    // Enviar mensaje si la palabra es "mi"
                    // if (sent === 'mi') {
                    //     sendMessage();
                    // } else if (sent === 'L') {
                    //     // Borrar palabra si la palabra es "L"
                    //     deleteLastWord();
                    // } else {
                    //     sentence.unshift(sent);
                    //     [sentence, repeSent] = formatSentences(sent, sentence, repeSent);
                    //     // Agregar la palabra detectada al área de texto
                    //     detectedWordsElement.value += sent + ' ';
                    // }
                }

                countFrame = 0;
                kpSequence = [];
            }
        }

        // Dibujar los landmarks de la pose
        drawConnectors(canvasCtx, results.poseLandmarks, POSE_CONNECTIONS, { color: '#00FF00', lineWidth: 4 });
        drawLandmarks(canvasCtx, results.poseLandmarks, { color: '#FF0000', lineWidth: 2 });

        // Dibujar los landmarks del rostro
        drawConnectors(canvasCtx, results.faceLandmarks, FACEMESH_TESSELATION, { color: '#C0C0C070', lineWidth: 1 });

        // Dibujar los landmarks de la mano izquierda
        drawConnectors(canvasCtx, results.leftHandLandmarks, HAND_CONNECTIONS, { color: '#CC0000', lineWidth: 5 });
        drawLandmarks(canvasCtx, results.leftHandLandmarks, { color: '#00FF00', lineWidth: 2 });

        // Dibujar los landmarks de la mano derecha
        drawConnectors(canvasCtx, results.rightHandLandmarks, HAND_CONNECTIONS, { color: '#00CC00', lineWidth: 5 });
        drawLandmarks(canvasCtx, results.rightHandLandmarks, { color: '#FF0000', lineWidth: 2 });

        // Llamar a drawGuidelines para dibujar las guías
        drawGuidelines(canvasCtx, canvasElement.width, canvasElement.height);

        canvasCtx.restore(); // Restaurar el estado del contexto
    }

    // Creación e inicialización del modelo Holistic:
    const holistic = new Holistic({
        locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/holistic/${file}`,
    });

    // Se configuran las opciones del modelo, como la complejidad del modelo, la suavidad de los landmarks, etc.
    holistic.setOptions({
        modelComplexity: 1,
        smoothLandmarks: true,
        enableSegmentation: false,
        smoothSegmentation: true,
        refineFaceLandmarks: false,  // Si está en true detecta 478 puntos, si está en false detecta 468 y eso necesito
        minDetectionConfidence: 0.5,
        minTrackingConfidence: 0.5
    });

    // Se establece la función onResults como el controlador de eventos para los resultados del modelo.
    holistic.onResults(onResults);

    // Función para iniciar la cámara
    function startCamera() {
        camera = new Camera(videoElement, {
            onFrame: async () => {
                await holistic.send({ image: videoElement });
                //requestAnimationFrame(startCamera);
            },
            width: 640,
            height: 480
        });
        camera.start();
    }

    // Función para detener la cámara
    function stopCamera() {
        if (camera) {
            camera.stop();
        }
    }

    // Manejo del botón de encendido/apagado de la cámara
    toggleCameraButton.addEventListener('click', () => {
        if (camera) {
            stopCamera();
            camera = null;
        } else {
            startCamera();
        }
    });

    // Crear un elemento de video solo para capturar la entrada de la cámara
    const videoElement = document.createElement('video');
    videoElement.width = 640;
    videoElement.height = 480;
    videoElement.autoplay = true;
    videoElement.muted = true;
    videoElement.playsInline = true;
    videoElement.style.display = 'none';
    document.body.appendChild(videoElement);

    // Iniciar la cámara al cargar el modelo
    startCamera();
});

function sendMessage() {
    const message = detectedWordsElement.value.trim();
    if (message) {
        console.log('Mensaje enviado:', message);
        detectedWordsElement.value = '';
    }
}

function deleteLastWord() {
    let words = detectedWordsElement.value.trim().split(' ');
    words.pop();
    detectedWordsElement.value = words.join(' ') + ' ';
}

function clearWord() {
    detectedWordsElement.value = '';
}

// Manejo del botón de envío
sendMessageButton.addEventListener('click', sendMessage);