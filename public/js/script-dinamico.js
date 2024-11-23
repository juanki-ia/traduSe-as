import {
    updateSentenceDisplay,
    textToSpeech,
    formatSentences,
    thereHand,
    extractKeypoints,
    drawGuidelines,
} from "./helpers.js";

console.log("Cargando modelo dinámico");

document.addEventListener("DOMContentLoaded", () => {
    const canvasElement = document.getElementById("output_canvas");
    const canvasColumn = document.getElementById("column_canvas");
    const canvasCtx = canvasElement.getContext("2d");
    const detectedWordsElement = document.getElementById("message");
    const sendMessageButton = document.querySelector(".btn.btn-primary");
    const toggleCameraButton = document.getElementById("toggle-camera");
    const chatForm = document.getElementById("chat-form");

    let camera = null;
    let kpSequence = [];
    let countFrame = 0;
    let actions = ["A", "gracias", "L", "mi", "nombre", "por favor"];
    let repeSent = 1;
    let sentence = [];
    const threshold = 0.7;
    const MAX_LENGTH_FRAMES = 15;
    const MIN_LENGTH_FRAMES = 5;

    const loadingElement = document.getElementById("loading");
    // Get CSRF token from meta tag
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

    tf.setBackend("wasm").then(async () => {
        try {
            const model = await tf.loadLayersModel("/models/model.json");
            loadingElement.style.display = "none";
            canvasElement.style.display = "none";
            canvasColumn.style.display = "none";

            function onResults(results) {
                canvasCtx.save();
                canvasCtx.clearRect(0,0,canvasElement.width,canvasElement.height);
                canvasCtx.drawImage(results.image,0,0,canvasElement.width,canvasElement.height);

                const newKeypoints = extractKeypoints(results);
                kpSequence.push(newKeypoints);

                if (kpSequence.length > MAX_LENGTH_FRAMES && thereHand(results)) {
                    countFrame += 1;
                    console.log("la cantidad de frames es:", countFrame);
                } else {
                    if (countFrame >= MIN_LENGTH_FRAMES) {
                        const lastKeypoints = kpSequence.slice(-MAX_LENGTH_FRAMES);
                        const inputTensor = tf.tensor([lastKeypoints]);
                        const prediction = model.predict(inputTensor);
                        const res = prediction.dataSync();
                        console.log("Resultados de la predicción:", res);

                        if (Math.max(...res) > threshold) {
                            const maxIndex = res.indexOf(Math.max(...res));
                            const sent = actions[maxIndex];
                            textToSpeech(sent);

                            if (sent === "mi") {
                                sendMessage(true); // Llama a la función de envío de mensaje con revisión
                            } else if (sent === "L") {
                                deleteLastWord();
                            } else {
                                sentence.unshift(sent);
                                [sentence, repeSent] = formatSentences(
                                    sent,
                                    sentence,
                                    repeSent
                                );
                                detectedWordsElement.value += sent + " ";
                            }
                        }

                        countFrame = 0;
                        kpSequence = [];
                    }
                }

                drawConnectors(
                    canvasCtx,
                    results.poseLandmarks,
                    POSE_CONNECTIONS,
                    { color: "#00FF00", lineWidth: 4 }
                );
                drawLandmarks(canvasCtx, results.poseLandmarks, {
                    color: "#FF0000",
                    lineWidth: 2,
                });
                drawConnectors(
                    canvasCtx,
                    results.faceLandmarks,
                    FACEMESH_TESSELATION,
                    { color: "#C0C0C070", lineWidth: 1 }
                );
                drawConnectors(
                    canvasCtx,
                    results.leftHandLandmarks,
                    HAND_CONNECTIONS,
                    { color: "#CC0000", lineWidth: 5 }
                );
                drawLandmarks(canvasCtx, results.leftHandLandmarks, {
                    color: "#00FF00",
                    lineWidth: 2,
                });
                drawConnectors(
                    canvasCtx,
                    results.rightHandLandmarks,
                    HAND_CONNECTIONS,
                    { color: "#00CC00", lineWidth: 5 }
                );
                drawLandmarks(canvasCtx, results.rightHandLandmarks, {
                    color: "#FF0000",
                    lineWidth: 2,
                });
                drawGuidelines(
                    canvasCtx,
                    canvasElement.width,
                    canvasElement.height
                );

                canvasCtx.restore();
            }

            const holistic = new Holistic({
                locateFile: (file) =>
                    `https://cdn.jsdelivr.net/npm/@mediapipe/holistic/${file}`,
            });

            holistic.setOptions({
                modelComplexity: 1,
                smoothLandmarks: true,
                enableSegmentation: false,
                smoothSegmentation: true,
                refineFaceLandmarks: false,
                minDetectionConfidence: 0.5,
                minTrackingConfidence: 0.5,
            });

            holistic.onResults(onResults);

            function startCamera() {
                canvasElement.style.display = "block";
                canvasColumn.style.display = "block";

                camera = new Camera(videoElement, {
                    onFrame: async () => {
                        await holistic.send({ image: videoElement });
                    },
                    width: 640,
                    height: 480,
                });
                camera.start();
            }

            function stopCamera() {
                canvasElement.style.display = "none";
                canvasColumn.style.display = "none";
                if (camera) {
                    camera.stop();
                }
            }

            //const toggleCameraButton = document.getElementById("toggle-camera");
            const cameraIcon = document.getElementById("camera-icon");

            toggleCameraButton.addEventListener("click", () => {
                if (camera) {
                    stopCamera();
                    camera = null;
                    cameraIcon.className = "fas fa-camera"; // Icono para activar
                    toggleCameraButton.classList.remove("btn-camera-active");
                    toggleCameraButton.classList.add("btn-camera-inactive");
                } else {
                    startCamera();
                    cameraIcon.className = "fas fa-video-slash"; // Icono para desactivar
                    toggleCameraButton.classList.remove("btn-camera-inactive");
                    toggleCameraButton.classList.add("btn-camera-active");
                }
            });

            const videoElement = document.createElement("video");
            videoElement.width = 640;
            videoElement.height = 480;
            videoElement.autoplay = true;
            videoElement.muted = true;
            videoElement.playsInline = true;
            videoElement.style.display = "none";
            document.body.appendChild(videoElement);
        } catch (error) {
            console.error("Error al cargar el modelo:", error);
        }
    });

    async function reviewMessage(message) {
        const response = await fetch('/review-message', {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
            },
            body: JSON.stringify({ message: message }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        return data.message;
    }

    async function sendMessage(withReview = false) {
        const message = detectedWordsElement.value.trim();
        if (message) {
            try {
                let finalMessage = message;
                if (withReview) {
                    finalMessage = await reviewMessage(message);
                }
                detectedWordsElement.value = finalMessage;
                chatForm.requestSubmit(); // Envía el formulario programáticamente con el mensaje revisado
            } catch (error) {
                console.error("Error reviewing the message:", error);
                chatForm.requestSubmit(); // Envía el mensaje original si hay un error en la revisión
            }
        }
    }

    function deleteLastWord() {
        let words = detectedWordsElement.value.trim().split(" ");
        words.pop();
        detectedWordsElement.value = words.join(" ") + " ";
    }

    sendMessageButton.addEventListener("click", () => sendMessage(false));
});
