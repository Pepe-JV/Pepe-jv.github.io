let displayValue = '0';
let history = '';

const display = document.getElementById('display');
const historyDisplay = document.getElementById('history');

function updateDisplay() {
    display.textContent = displayValue || '0';
    historyDisplay.textContent = history;
}

function appendToDisplay(value) {
    if (displayValue === '0' && value !== '.') {
        displayValue = value;
    } else {
        displayValue += value;
    }
    updateDisplay();
}

function clearDisplay() {
    displayValue = '0';
    history = '';
    updateDisplay();
}

function deleteLast() {
    if (displayValue.length > 1) {
        displayValue = displayValue.slice(0, -1);
    } else {
        displayValue = '0';
    }
    updateDisplay();
}

async function calculateResult() {
    try {
        history = displayValue;
        
        const response = await fetch('/calculate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ expression: displayValue })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Formatear el resultado
            let result = data.result;
            if (Number.isFinite(result)) {
                // Redondear a 10 decimales para evitar errores de punto flotante
                result = Math.round(result * 10000000000) / 10000000000;
            }
            displayValue = result.toString();
        } else {
            displayValue = 'Error';
            setTimeout(() => {
                displayValue = '0';
                history = '';
                updateDisplay();
            }, 2000);
        }
        
        updateDisplay();
    } catch (error) {
        console.error('Error:', error);
        displayValue = 'Error de conexión';
        updateDisplay();
        setTimeout(() => {
            displayValue = '0';
            history = '';
            updateDisplay();
        }, 2000);
    }
}

// Soporte para teclado
document.addEventListener('keydown', function(event) {
    const key = event.key;
    
    if (key >= '0' && key <= '9') {
        appendToDisplay(key);
    } else if (key === '.') {
        appendToDisplay('.');
    } else if (key === '+' || key === '-') {
        appendToDisplay(key);
    } else if (key === '*') {
        appendToDisplay('×');
    } else if (key === '/') {
        event.preventDefault();
        appendToDisplay('÷');
    } else if (key === 'Enter' || key === '=') {
        event.preventDefault();
        calculateResult();
    } else if (key === 'Escape') {
        clearDisplay();
    } else if (key === 'Backspace') {
        event.preventDefault();
        deleteLast();
    } else if (key === '(' || key === ')') {
        appendToDisplay(key);
    }
});

// Toggle tema
function toggleTheme() {
    document.body.classList.toggle('light-theme');
    
    // Guardar preferencia
    const isLight = document.body.classList.contains('light-theme');
    localStorage.setItem('theme', isLight ? 'light' : 'dark');
}

// Cargar tema guardado
window.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'light') {
        document.body.classList.add('light-theme');
    }
});

// Inicializar display
updateDisplay();
