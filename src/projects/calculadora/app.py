from flask import Flask, render_template, request, jsonify
import math

app = Flask(__name__)

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/calculate', methods=['POST'])
def calculate():
    try:
        data = request.get_json()
        expression = data.get('expression', '')
        
        # Reemplazar operadores visuales por operadores de Python
        expression = expression.replace('×', '*').replace('÷', '/')
        
        # Evaluar la expresión de forma segura
        # Permitir funciones matemáticas básicas
        allowed_names = {
            'sqrt': math.sqrt,
            'sin': math.sin,
            'cos': math.cos,
            'tan': math.tan,
            'log': math.log,
            'pi': math.pi,
            'e': math.e
        }
        
        result = eval(expression, {"__builtins__": {}}, allowed_names)
        
        return jsonify({'result': result, 'success': True})
    except Exception as e:
        return jsonify({'error': str(e), 'success': False})

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)
