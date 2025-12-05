# 💼 Portfolio - José Justicia Vico


Portfolio personal de José Justicia, Web Developer apasionado por el desarrollo web y la tecnología. Un sitio moderno y minimalista que muestra mis habilidades, experiencia y proyectos.

## ✨ Características

- 🎨 **Diseño Minimalista** - Interfaz limpia inspirada en el diseño de Apple
- 📱 **Completamente Responsive** - Adaptado a todos los tamaños de pantalla
- 🌓 **Tema Claro/Oscuro** - Selector de tema con transición suave
- 🍔 **Menú Hamburguesa** - Navegación móvil fluida con animaciones
- 🎯 **Scroll Suave** - Navegación entre secciones optimizada
- ⚡ **Optimizado** - Fuentes locales, código semántico y SEO optimizado
- 🎮 **Proyectos Integrados** - Calculadora funcional y reto de mecanografía
- ♿ **Accesible** - Cumple con estándares de accesibilidad web

## 🛠️ Tecnologías y Herramientas

### 🎨 Frontend
- HTML5
- CSS3
- JavaScript
- React
- TypeScript
- Figma

### ⚙️ Backend
- Java
- PHP
- C++
- Python
- MySQL

### 🔧 Herramientas
- Git & GitHub
- Linux/Bash
- VS Code
- PhpStorm

## 📂 Estructura del Proyecto

```
Pepe-JV.github.io/
├── index.html                    # Página principal del portfolio
├── about.html                    # Página Sobre Mí
├── contact.html                  # Página de contacto
├── projects.html                 # Galería de proyectos
├── README.md                     # Documentación del proyecto
└── src/
    ├── assets/
    │   ├── fonts/               # Fuentes locales (Inter)
    │   │   ├── Inter-Regular.woff2
    │   │   ├── Inter-Medium.woff2
    │   │   ├── Inter-SemiBold.woff2
    │   │   ├── Inter-Bold.woff2
    │   │   └── inter.css
    │   ├── icons/               # Favicons
    │   │   ├── favicon.svg
    │   │   └── favicon.ico
    │   └── images/              # Imágenes y recursos visuales
    │       ├── yo.jpeg          # Foto de perfil
    │       ├── img_tecnologia/  # Iconos de tecnologías
    │       └── project-*.svg    # Imágenes de proyectos
    ├── css/
    │   └── styles.css           # Estilos principales
    ├── js/
    │   └── main.js              # JavaScript principal
    └── projects/                # Proyectos integrados
        ├── todo-react/          # TODO App React (repositorio externo)
        │   └── [Ver repositorio: TODO-React]
        ├── mecanografia/        # Reto de mecanografía
        │   ├── index.html
        │   ├── style.css
        │   └── script.js
        └── calculadora/         # Calculadora JavaScript
            ├── index.html
            ├── styles.css
            └── script.js
```

## 🎯 Secciones del Portfolio

- **🏠 Inicio** - Presentación personal con foto y descripción
- **💼 Experiencia** - Trayectoria profesional (Grupo Winecta)
- **💻 Tecnologías** - Stack tecnológico organizado por categorías
- **🚀 Proyectos** - Proyectos destacados con enlaces y demos
- **📧 Contacto** - Información de contacto y redes sociales

## 🌐 Deploy & Instalación

### Ver en línea
👉 **[https://pepe-jv.github.io](https://pepe-jv.github.io)**

### Instalación local

```bash
# 1. Clonar el repositorio
git clone https://github.com/Pepe-JV/Pepe-JV.github.io.git

# 2. Navegar al directorio
cd Pepe-JV.github.io

# 3. Abrir con un servidor local
# Opción A: Usar Live Server en VS Code
# Opción B: Usar Python
python -m http.server 8000

# Opción C: Simplemente abrir index.html en tu navegador
```

### Configuración de GitHub Pages

Este proyecto está configurado para desplegarse automáticamente en GitHub Pages:

1. El repositorio debe llamarse `username.github.io` (en este caso `Pepe-JV.github.io`)
2. En Settings > Pages > Source, seleccionar "Deploy from a branch"
3. Branch: `main`, Folder: `/ (root)`
4. Cada push a `main` actualiza automáticamente el sitio

## 🚀 Proyectos Destacados

### 1. TODO App - React
Aplicación de gestión de tareas desarrollada con React y Vite, con diseño moderno y funcionalidades completas.

**Características:**
- ✅ Añadir, editar y eliminar tareas
- 📝 Marcar tareas como completadas
- 🔍 Filtrar tareas (Todas, Activas, Completadas)
- 💾 Persistencia de datos en localStorage
- 🎨 Interfaz moderna y responsive
- ⚡ Desarrollado con Vite para mejor rendimiento

**Tech Stack:** React, JavaScript, CSS, Vite

**Repositorio:** [TODO-React](https://github.com/Pepe-JV/TODO-React)

### 2. Reto de Mecanografía
Aplicación interactiva para practicar mecanografía con contador de palabras por minuto (WPM), precisión y tiempo.

**Características:**
- ⌨️ Sistema de escritura en tiempo real
- 📊 Estadísticas de rendimiento (WPM, precisión, tiempo)
- 🎯 Detección de errores y aciertos
- 🔄 Función de reinicio

**Tech Stack:** HTML, CSS, JavaScript

### 3. Calculadora JavaScript
Calculadora funcional con interfaz moderna y operaciones básicas.

**Características:**
- ➕ Operaciones básicas (suma, resta, multiplicación, división)
- 🔢 Display de operaciones y resultados
- 🎨 Diseño minimalista
- ⚡ Funcionalidad completa con teclado

**Tech Stack:** HTML, CSS, JavaScript

## 📈 Optimizaciones Implementadas

- ⚡ **Fuentes Locales** - Fuente Inter autoalojada para mejor rendimiento
- 🎨 **Iconos SVG** - Uso de archivos SVG externos en lugar de inline
- 📱 **Mobile First** - Diseño responsive desde dispositivos móviles
- ♿ **Accesibilidad** - Etiquetas ARIA y navegación por teclado
- 🔍 **SEO** - Meta tags optimizados (Open Graph, Twitter Cards)
- 🌐 **PWA Ready** - Preparado para Progressive Web App

## 📝 Características Técnicas

### CSS
- Variables CSS para temas
- Flexbox y Grid Layout
- Animaciones y transiciones suaves
- Media queries para responsive
- Fuentes web optimizadas (woff2)

### JavaScript
- Navegación suave entre secciones
- Toggle de tema claro/oscuro con persistencia
- Menú hamburguesa interactivo
- Event listeners optimizados
- Almacenamiento local del tema

### HTML
- Estructura semántica
- Meta tags SEO
- Open Graph y Twitter Cards
- Accesibilidad (ARIA labels)
- Favicon SVG y fallback

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Si encuentras algún error o tienes sugerencias:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📧 Contacto

**José Justicia Vico** - Web Developer

[![GitHub](https://img.shields.io/badge/GitHub-Pepe--JV-181717?style=for-the-badge&logo=github)](https://github.com/Pepe-JV)
[![Email](https://img.shields.io/badge/Email-jjusticiavico%40gmail.com-EA4335?style=for-the-badge&logo=gmail&logoColor=white)](mailto:jjusticiavico@gmail.com)
[![Portfolio](https://img.shields.io/badge/Portfolio-pepe--jv.github.io-4285F4?style=for-the-badge&logo=googlechrome&logoColor=white)](https://pepe-jv.github.io)

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Consulta el archivo `LICENSE` para más detalles.

---

<div align="center">



© 2025 José Justicia Vico - Todos los derechos reservados



</div>
