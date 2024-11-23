import { updateSentenceDisplay, textToSpeech, formatSentences, thereHand, extractKeypoints, drawGuidelines  } from './helpers.js';

const videoElement = document.getElementsByClassName('input_video')[0];
const canvasElement = document.getElementsByClassName('output_canvas')[0];
const canvasCtx = canvasElement.getContext('2d');

// Selecciona el elemento HTML donde deseas mostrar las palabras de la oración
const sentenceContainer = document.getElementById('sentence-container');
let sentenceList = document.createElement('div'); // Creamos un contenedor para las palabras
// Añadir el contenedor de palabras al contenedor principal
sentenceContainer.appendChild(sentenceList);

const detectedWordsElement = document.getElementById('detected-words');
const sendMessageButton = document.getElementById('send-message');

let kpSequence = []; // Variable para almacenar la secuencia de keypoints
let countFrame = 0;
let actions = ['A','gracias','L','mi','nombre','por favor'];
let repeSent = 1;
let sentence = [];
const threshold = 0.7;
const MAX_LENGTH_FRAMES = 15
const MIN_LENGTH_FRAMES = 5

tf.setBackend('wasm').then(async () => {
    // Carga del modelo TensorFlow.js
    const model = await tf.loadLayersModel('./models/model.json');


    // Función que se llama cada vez que se obtienen resultados del modelo Holistic.
    function onResults(results) {
        canvasCtx.save(); // Guarda el estado actual del contexto del lienzo.
        canvasCtx.clearRect(0, 0, canvasElement.width, canvasElement.height); // Borra el contenido del lienzo.
        //canvasCtx.drawImage(results.segmentationMask, 0, 0,canvasElement.width, canvasElement.height);

        // Actualizar la secuencia de keypoints
        const newKeypoints = extractKeypoints(results);
        kpSequence.push(newKeypoints);
        
        // Realizar la evaluación del modelo
        if (kpSequence.length > MAX_LENGTH_FRAMES && thereHand(results)) {
            countFrame += 1;
        } else {
            if (countFrame >= MIN_LENGTH_FRAMES) {
                const lastKeypoints = kpSequence.slice(-MAX_LENGTH_FRAMES); // Obtener los últimos MAX_LENGTH_FRAMES keypoints
                const inputTensor = tf.tensor([lastKeypoints]); // Crear un tensor con los keypoints
                const prediction = model.predict(inputTensor); // Realizar la predicción con el modelo TensorFlow.js
                const res = prediction.dataSync(); // Obtener los resultados de la predicción
                //console.log('Resultados de la predicción:', res);
                
                if (Math.max(...res) > threshold) {
                    const maxIndex = res.indexOf(Math.max(...res));
                    const sent = actions[maxIndex];
                    sentence.unshift(sent);
                    textToSpeech(sent);
                    [sentence, repeSent] = formatSentences(sent, sentence, repeSent);
                    // Llama a la función updateSentenceDisplay con la variable sentence
                    //updateSentenceDisplay(sentence, sentenceContainer);

                    // Agregar la palabra detectada al área de texto
                    detectedWordsElement.value += sent + ' ';
                }
                
                countFrame = 0;
                kpSequence = [];
            }
        }
        
        // Solo sobrescribe los píxeles existentes en el lienzo.
        canvasCtx.globalCompositeOperation = 'source-in';
        canvasCtx.fillStyle = '#00FF00';
        canvasCtx.fillRect(0, 0, canvasElement.width, canvasElement.height);

        // Sobrescribe solo los píxeles faltantes en el lienzo.
        canvasCtx.globalCompositeOperation = 'destination-atop';
        canvasCtx.drawImage(results.image, 0, 0, canvasElement.width, canvasElement.height);

        canvasCtx.globalCompositeOperation = 'source-over';

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
        /*
        locateFile: (file) => {
            return `https://cdn.jsdelivr.net/npm/@mediapipe/holistic/${file}`;
        }
        */
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


    // Configuración y inicio de la cámara:
    const camera = new Camera(videoElement, {
        onFrame: async () => {
            // Obtener resultados de MediaPipe
            await holistic.send({ image: videoElement });
            
        },
        width: 640,
        height: 480
    });
    camera.start();
});

// Manejo del botón de envío
sendMessageButton.addEventListener('click', () => {
    const message = detectedWordsElement.value.trim();
    if (message) {
        // Aquí puedes agregar la lógica para enviar el mensaje al Usuario B
        console.log('Mensaje enviado:', message);

        // Limpiar el área de texto después de enviar el mensaje
        detectedWordsElement.value = '';
    }
});