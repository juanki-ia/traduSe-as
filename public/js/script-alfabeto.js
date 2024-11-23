import {
    updateSentenceDisplay,
    textToSpeech,
    formatSentences,
    thereHand,
    extractKeypoints,
    drawGuidelines,
} from "./helpers.js";

import { GestureRecognizer, FilesetResolver, DrawingUtils } from "https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.3";


console.log("Cargando modelo alfabeto");

document.addEventListener("DOMContentLoaded", () => {
    
    const canvasElement = document.getElementById("output_canvas");
    const canvasColumn = document.getElementById("column_canvas");
    const canvasCtx = canvasElement.getContext("2d");
    const detectedWordsElement = document.getElementById("message");
    const sendMessageButton = document.querySelector(".btn.btn-primary");
    const toggleCameraButton = document.getElementById("toggle-camera");
    const chatForm = document.getElementById("chat-form");

    let camera = null;
    let alphabetRecognizer;
    let alphabetFrameCount = 0;
    const ALPHABET_THRESHOLD = 15;

    const loadingElement = document.getElementById("loading");
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

    async function loadAlphabetModel() {
        const vision = await FilesetResolver.forVisionTasks("https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.3/wasm");
        alphabetRecognizer = await GestureRecognizer.createFromOptions(vision, {
            baseOptions: {
                modelAssetPath: "/models/alfabeto.task",
                delegate: "GPU"
            },
            runningMode: "VIDEO"
        });
    }

    async function predictAlphabet(results) {
        if (!alphabetRecognizer) {
            console.error("Alphabet model not loaded yet");
            return;
        }
        const nowInMs = Date.now();
        const alphabetResults = alphabetRecognizer.recognizeForVideo(results.image, nowInMs);
        if (alphabetResults.gestures.length > 0) {
            const detectedLetter = alphabetResults.gestures[0][0].categoryName;
            alphabetFrameCount++;
            if (alphabetFrameCount >= ALPHABET_THRESHOLD) {
                detectedWordsElement.value += detectedLetter + " ";
                alphabetFrameCount = 0;
            }
        } else {
            alphabetFrameCount = 0;
        }
    }

    loadAlphabetModel();

    function onResults(results) {
        canvasCtx.save();
        canvasCtx.clearRect(0, 0, canvasElement.width, canvasElement.height);
        canvasCtx.drawImage(results.image, 0, 0, canvasElement.width, canvasElement.height);

        predictAlphabet(results);

        drawConnectors(canvasCtx, results.poseLandmarks, POSE_CONNECTIONS, { color: "#00FF00", lineWidth: 4 });
        drawLandmarks(canvasCtx, results.poseLandmarks, { color: "#FF0000", lineWidth: 2 });
        drawConnectors(canvasCtx, results.faceLandmarks, FACEMESH_TESSELATION, { color: "#C0C0C070", lineWidth: 1 });
        drawConnectors(canvasCtx, results.leftHandLandmarks, HAND_CONNECTIONS, { color: "#CC0000", lineWidth: 5 });
        drawLandmarks(canvasCtx, results.leftHandLandmarks, { color: "#00FF00", lineWidth: 2 });
        drawConnectors(canvasCtx, results.rightHandLandmarks, HAND_CONNECTIONS, { color: "#00CC00", lineWidth: 5 });
        drawLandmarks(canvasCtx, results.rightHandLandmarks, { color: "#FF0000", lineWidth: 2 });
        drawGuidelines(canvasCtx, canvasElement.width, canvasElement.height);

        canvasCtx.restore();
    }

    const holistic = new Holistic({
        locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/holistic/${file}`,
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
            cameraIcon.className = "fas fa-camera";
            toggleCameraButton.classList.remove("btn-camera-active");
            toggleCameraButton.classList.add("btn-camera-inactive");
        } else {
            startCamera();
            cameraIcon.className = "fas fa-video-slash";
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
});
