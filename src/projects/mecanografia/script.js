// Textos de ejemplo para el reto de mecanografía
const texts = [
    "La práctica constante es la clave del éxito. Cada día que dedicas a mejorar tus habilidades te acerca más a la maestría. No importa cuán lento vayas, siempre y cuando no te detengas.",
    "El tiempo es el recurso más valioso que tenemos. Aprender a escribir rápido y con precisión nos permite comunicarnos de manera más eficiente en el mundo digital actual.",
    "La tecnología avanza a pasos agigantados. Dominar el teclado es una habilidad fundamental en la era de la información. Cada tecla presionada es un paso hacia el futuro.",
    "La perseverancia vence lo que la dicha no alcanza. Con dedicación y esfuerzo, incluso las tareas más difíciles se vuelven simples. La mecanografía es un arte que se perfecciona con la práctica.",
    "El conocimiento es poder, y la velocidad de escritura es la llave que abre las puertas de la productividad. Escribir sin mirar el teclado es una sensación liberadora.",
    "En un mundo conectado, la capacidad de expresar ideas rápidamente marca la diferencia. La mecanografía no es solo velocidad, es precisión y eficiencia combinadas.",
    "Cada error es una oportunidad de aprendizaje. No temas equivocarte, porque es en esos momentos cuando más crecemos. La práctica hace al maestro, y la paciencia es tu mejor aliada.",
    "Las grandes hazañas comienzan con pequeños pasos. Mejorar tu velocidad de escritura es un viaje, no un destino. Disfruta del proceso y celebra cada pequeño logro.",
];

// Variables del juego
let currentText = '';
let currentIndex = 0;
let startTime = null;
let timerInterval = null;
let errors = 0;
let totalKeystrokes = 0;
let correctKeystrokes = 0;

// Referencias a elementos del DOM
const textDisplay = document.getElementById('textDisplay');
const userInput = document.getElementById('userInput');
const wpmDisplay = document.getElementById('wpm');
const accuracyDisplay = document.getElementById('accuracy');
const timerDisplay = document.getElementById('timer');
const errorsDisplay = document.getElementById('errors');
const restartBtn = document.getElementById('restartBtn');
const newTextBtn = document.getElementById('newTextBtn');
const results = document.getElementById('results');
const tryAgainBtn = document.getElementById('tryAgainBtn');

// Inicializar el juego
function init() {
    loadRandomText();
    userInput.value = '';
    currentIndex = 0;
    startTime = null;
    errors = 0;
    totalKeystrokes = 0;
    correctKeystrokes = 0;
    updateStats();
    userInput.focus();
    
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
}

// Cargar un texto aleatorio
function loadRandomText() {
    currentText = texts[Math.floor(Math.random() * texts.length)];
    displayText();
}

// Mostrar el texto con formato
function displayText() {
    textDisplay.innerHTML = '';
    
    for (let i = 0; i < currentText.length; i++) {
        const span = document.createElement('span');
        span.textContent = currentText[i];
        
        if (i < currentIndex) {
            span.classList.add('correct');
        } else if (i === currentIndex) {
            span.classList.add('current');
        }
        
        textDisplay.appendChild(span);
    }
}

// Iniciar el temporizador
function startTimer() {
    if (!startTime) {
        startTime = Date.now();
        timerInterval = setInterval(updateTimer, 100);
    }
}

// Actualizar el temporizador
function updateTimer() {
    const elapsed = Math.floor((Date.now() - startTime) / 1000);
    timerDisplay.textContent = elapsed + 's';
}

// Calcular palabras por minuto
function calculateWPM() {
    if (!startTime) return 0;
    
    const elapsed = (Date.now() - startTime) / 1000 / 60; // minutos
    const words = correctKeystrokes / 5; // promedio de 5 caracteres por palabra
    return Math.round(words / elapsed);
}

// Calcular precisión
function calculateAccuracy() {
    if (totalKeystrokes === 0) return 100;
    return Math.round((correctKeystrokes / totalKeystrokes) * 100);
}

// Actualizar estadísticas
function updateStats() {
    wpmDisplay.textContent = calculateWPM();
    accuracyDisplay.textContent = calculateAccuracy() + '%';
    errorsDisplay.textContent = errors;
}

// Manejar la entrada del usuario
userInput.addEventListener('input', (e) => {
    startTimer();
    
    const typed = e.target.value;
    const expected = currentText.substring(0, typed.length);
    
    // Verificar si el texto escrito coincide
    if (typed === expected) {
        currentIndex = typed.length;
        totalKeystrokes++;
        correctKeystrokes++;
        displayText();
        updateStats();
        
        // Verificar si se completó el texto
        if (typed === currentText) {
            finishTest();
        }
    } else {
        // Error detectado
        if (typed.length > currentIndex) {
            errors++;
            totalKeystrokes++;
            userInput.value = currentText.substring(0, currentIndex);
            updateStats();
            
            // Efecto visual de error
            userInput.style.borderColor = '#ff4444';
            setTimeout(() => {
                userInput.style.borderColor = '';
            }, 200);
        }
    }
});

// Prevenir pegar texto
userInput.addEventListener('paste', (e) => {
    e.preventDefault();
});

// Finalizar la prueba
function finishTest() {
    clearInterval(timerInterval);
    
    const finalWPM = calculateWPM();
    const finalAccuracy = calculateAccuracy();
    const finalTime = Math.floor((Date.now() - startTime) / 1000);
    
    document.getElementById('finalWpm').textContent = finalWPM;
    document.getElementById('finalAccuracy').textContent = finalAccuracy + '%';
    document.getElementById('finalTime').textContent = finalTime + 's';
    
    results.style.display = 'flex';
    userInput.disabled = true;
}

// Botón reiniciar
restartBtn.addEventListener('click', () => {
    results.style.display = 'none';
    userInput.disabled = false;
    displayText();
    userInput.value = '';
    currentIndex = 0;
    startTime = null;
    errors = 0;
    totalKeystrokes = 0;
    correctKeystrokes = 0;
    updateStats();
    userInput.focus();
    
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
});

// Botón nuevo texto
newTextBtn.addEventListener('click', () => {
    results.style.display = 'none';
    userInput.disabled = false;
    init();
});

// Botón intentar de nuevo
tryAgainBtn.addEventListener('click', () => {
    results.style.display = 'none';
    userInput.disabled = false;
    init();
});

// Prevenir que el input pierda el foco
document.addEventListener('click', (e) => {
    if (e.target !== userInput && !results.style.display.includes('flex')) {
        userInput.focus();
    }
});

// Inicializar al cargar la página
init();
