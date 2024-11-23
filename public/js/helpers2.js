// Función para actualizar el contenido del elemento HTML con las palabras de la oración
export function updateSentenceDisplay(sentence, sentenceContainer) {
    // Limpiar el contenido anterior del contenedor
    sentenceContainer.innerHTML = '';

    // Crear elementos de lista para cada palabra en la oración y agregarlos al contenedor
    sentence.forEach(word => {
        const wordElement = document.createElement('span');
        wordElement.textContent = word + ' ';
        sentenceContainer.appendChild(wordElement);
    });
}

// Convierte la palabra de texto a voz
export function textToSpeech(text) {
    try {
        // Verifica si la API de síntesis de voz está disponible
        if ('speechSynthesis' in window && typeof SpeechSynthesisUtterance !== 'undefined') {
            // Crear un nuevo objeto SpeechSynthesisUtterance con el texto proporcionado
            const utterance = new SpeechSynthesisUtterance(text);
            // Establecer el idioma del texto a sintetizar
            utterance.lang = 'es';
            // Usar la API de síntesis de voz para hablar el texto
            speechSynthesis.speak(utterance);
        } else {
            console.error('API de síntesis de voz no está disponible en este navegador.');
        }
    } catch (error) {
        console.error('Error al intentar usar la síntesis de voz:', error);
    }
}

// Formatea las oraciones para su visualización.
export function formatSentences(sent, sentence, repeSent) {
    if (sentence.length > 1) {
        if (sentence[1].includes(sent)) {
            repeSent += 1;
            sentence.shift();
            sentence[0] = `${sent} (x${repeSent})`;
        } else {
            repeSent = 1;
        }
    }
    return [sentence, repeSent];
}

// Verifica si hay alguna mano detectada en los resultados proporcionados.
export function thereHand(results) {
    return results.leftHandLandmarks || results.rightHandLandmarks;
}

// Función para procesar los keypoints de MediaPipe
export function extractKeypoints(results) {
    // Extraer keypoints de la pose
    const pose = results.poseLandmarks ? results.poseLandmarks.map(res => [res.x, res.y, res.z, res.visibility]).flat() : new Array(33 * 4).fill(0);
    // Extraer keypoints de la cara
    const face = results.faceLandmarks ? results.faceLandmarks.map(res => [res.x, res.y, res.z]).flat() : new Array(468 * 3).fill(0);
    // Extraer keypoints de la mano izquierda
    const leftHand = results.leftHandLandmarks ? results.leftHandLandmarks.map(res => [res.x, res.y, res.z]).flat() : new Array(21 * 3).fill(0);  
    // Extraer keypoints de la mano derecha
    const rightHand = results.rightHandLandmarks ? results.rightHandLandmarks.map(res => [res.x, res.y, res.z]).flat() : new Array(21 * 3).fill(0);
    // Concatenar todos los keypoints y devolverlos como un array unidimensional
    return pose.concat(face, leftHand, rightHand);
}

// Dibuja líneas guía en la imagen
export function drawGuidelines(ctx, width, height) {
    // Dibuja un rectángulo alrededor del área donde debe estar la cabeza
    const headWidth = width * 0.35; // Ancho del rectángulo ajustado
    const headHeight = height * 0.35;
    const headX = (width - headWidth) / 2; // Centrando el rectángulo
    const headY = height * 0.025;

    ctx.strokeStyle = 'red';
    ctx.lineWidth = 2;
    ctx.strokeRect(headX, headY, headWidth, headHeight);

    // Puedes agregar más líneas guía si es necesario
}
