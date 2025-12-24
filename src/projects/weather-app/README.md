# 🌤️ Weather App - Aplicación Meteorológica

Una aplicación web moderna y responsiva para consultar el pronóstico del tiempo en tiempo real utilizando React y la API de Visual Crossing Weather.

## ✨ Características

### Requisitos Implementados

✅ **Búsqueda de ubicaciones**: El usuario puede ingresar cualquier ciudad o ubicación en el campo de búsqueda.

✅ **Información meteorológica completa**:
- Temperatura actual
- Sensación térmica
- Velocidad del viento
- Humedad
- Probabilidad de lluvia
- Condiciones climáticas (soleado, lluvioso, nublado, etc.)

✅ **Pronóstico por horas**: Visualización de las últimas 12 horas y las próximas 12 horas (24 horas en total).

✅ **Actualización del pronóstico**: Botón para refrescar los datos meteorológicos.

### Funcionalidades Extra (Metas Ambiciosas)

✅ **Animaciones con Framer Motion**: 
- Transiciones suaves al cargar los datos
- Efectos hover en las tarjetas
- Animaciones de entrada escalonadas para el pronóstico por horas

✅ **Geolocalización automática**: 
- Al cargar la página, detecta automáticamente la ubicación del usuario
- Botón "Mi ubicación" para volver a usar la geolocalización

## 🚀 Cómo usar

### 1. API Key configurada

La aplicación ya viene con una API key configurada de Visual Crossing Weather API (cuenta gratuita con 1000 consultas/día).

Si deseas usar tu propia API key:

1. Visita [Visual Crossing Weather API](https://www.visualcrossing.com/weather-api)
2. Crea una cuenta gratuita
3. Obtén tu API key
4. Abre el archivo `app.js`
5. Reemplaza la API key existente con la tuya:

```javascript
const API_KEY = 'tu-api-key-aqui';
```

### 2. Ejecutar la aplicación

Simplemente abre el archivo `index.html` en tu navegador web.

**Nota**: Para que funcione la geolocalización, necesitas servir la aplicación a través de HTTPS o localhost. Puedes usar:

```bash
# Con Python 3
python -m http.server 8000

# Con Node.js (http-server)
npx http-server
```

Luego accede a `http://localhost:8000` en tu navegador.

## 🎨 Características de diseño

- **Diseño responsivo**: Se adapta perfectamente a móviles, tablets y escritorio
- **Interfaz moderna**: Uso de gradientes, sombras y animaciones suaves
- **Iconos emoji**: Representación visual intuitiva de las condiciones climáticas
- **Tarjetas interactivas**: Efectos hover y transiciones
- **Estados de carga**: Spinner animado mientras se cargan los datos
- **Manejo de errores**: Mensajes claros y amigables para el usuario

## 🛠️ Tecnologías utilizadas

- **React 18**: Framework de JavaScript para la interfaz de usuario
- **Framer Motion**: Biblioteca de animaciones
- **Visual Crossing Weather API**: Fuente de datos meteorológicos
- **CSS3**: Estilos modernos con gradientes y animaciones
- **Geolocation API**: Para obtener la ubicación del usuario

## 📱 Funcionalidades

### Búsqueda
- Ingresa el nombre de cualquier ciudad del mundo
- Presiona "Buscar" o Enter para obtener el pronóstico

### Botones de acción
- **🔍 Buscar**: Busca el clima de la ubicación ingresada
- **🔄 Actualizar**: Refresca los datos meteorológicos
- **📍 Mi ubicación**: Usa la geolocalización para obtener el clima local

### Información mostrada
- **Clima actual**: Temperatura, condiciones, ubicación
- **Detalles**: Sensación térmica, viento, humedad, probabilidad de lluvia
- **Pronóstico horario**: 24 horas de pronóstico detallado

## 🌐 Navegadores compatibles

- Chrome (recomendado)
- Firefox
- Safari
- Edge

## 📄 Licencia

Este proyecto es de código abierto y está disponible bajo la licencia MIT.

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Si encuentras un error o tienes una sugerencia, no dudes en crear un issue o pull request.

---

Desarrollado con ❤️ usando React y Visual Crossing Weather API
