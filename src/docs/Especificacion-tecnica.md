# Custodya - Especificación Técnica

## 1. Resumen Ejecutivo

**Custodya** es una plataforma SaaS (Software as a Service) de gestión integral diseñada para **asociaciones, ya sea religiosa, vecinal u otras** y cualquier organización que requiera una gestión moderna, centralizada y eficiente en la nube.

### 1.1 Objetivo del Sistema

Proporcionar una solución completa de administración que centralice la gestión de miembros, finanzas, eventos, patrimonio y comunicaciones mediante una arquitectura web moderna, escalable y segura.

### 1.2 Alcance

Sistema web multi-tenant con arquitectura cliente-servidor, accesible desde navegadores modernos, con gestión de roles diferenciados (administrador, miembro, invitado).

---

## 2. Funcionalidades Core

### 2.1 Módulos Principales

Custodya facilita la administración integral del grupo mediante:

-   **Gestión de Integrantes**: CRUD completo con perfiles personalizados, historial de actividad y roles diferenciados
-   **Sistema de Cuotas y Cobros**: Gestión automatizada de pagos recurrentes, conciliación bancaria y generación de recibos
-   **Calendario y Eventos**: Planificador con notificaciones automáticas, inscripciones y control de asistencia
-   **Administración de Patrimonio**: Inventario digital con trazabilidad, valoración económica y gestión documental
-   **Control de Acceso (RBAC)**: Sistema de roles y permisos granulares 
-   **Repositorio Documental**: Almacenamiento seguro con versionado, búsqueda avanzada y control de acceso
-   **Landing Page Personalizable**: Portal público con branding corporativo y SEO optimizado
-   **Portal de Usuario**: Área privada para miembros con autogestión de datos y consulta de información

---

## 3. Arquitectura del Sistema

### 3.1 Stack Tecnológico

#### Frontend
- **Motor de Plantillas**: Twig (templating engine de Symfony)
- **CSS Framework**: Tailwind CSS 4
- **JavaScript**
- **Validación**: Symfony Forms + Constraints
- **Assets**:AssetMapper

#### Backend
- **Framework**: Symfony 8 (PHP 8.5)
- **Arquitectura**: Doctrine ORM
- **API**: API Platform
- **Autenticación**: Symfony Security

#### Base de Datos
- **Principal**: PostgreSQL 15+ (datos relacionales)

#### Infraestructura
- **Contenedores**: Docker + Docker Compose
- **Hosting**: localhost


---

## 4. Características Técnicas Destacadas

### 4.1 Experiencia de Usuario (UX/UI)

**Diseño responsivo y accesible**
- Interfaz moderna siguiendo principios de Material Design / Design System propio
- Compatibilidad cross-browser (Chrome, Firefox, Safari, Edge)
- Responsive design (mobile-first approach)
- Cumplimiento WCAG 2.1 nivel AA (accesibilidad)
- Modo oscuro/claro

### 4.2 Automatización e Inteligencia Artificial

**Motor de automatización**
- Recordatorios automáticos de cuotas vía email/SMS (Symfony Messenger + Mailer)
- Notificaciones push para eventos próximos (Symfony Notifier)
- Generación automática de recibos y documentos (Twig + DomPDF/Snappy)

### 4.3 Sistema Financiero

**Gestión de cuotas:**
- Cuotas recurrentes configurables (mensual, trimestral, anual)
- Múltiples conceptos de pago simultáneos
- Descuentos y bonificaciones automáticas
- Fraccionamiento de pagos
- Exenciones y casos especiales

**Pasarelas de pago integradas:**
- Paypal
- Bizum (para España) - Integración vía API REST
- Transferencia bancaria con conciliación automática


### 4.4 Sistema de Comunicación

**Notificaciones:**
- Sistema multi-canal con Symfony Notifier (email)
- Plantillas de mensajes personalizables (Twig templates)
- Programación de envíos (Symfony Scheduler + Messenger)

**Comunicados oficiales:**
- Sistema de anuncios con priorización
- Adjuntos y rich text formatting

### 4.5 Seguridad y Cumplimiento Normativo

**Cumplimiento legal:**
- LOPD (España)
- Exportación de datos personales
- Consentimientos rastreables
- Política de privacidad y términos de uso
- Registro de tratamientos de datos

**Backups y recuperación:**
- Backups automatizados diarios
- Retención configurable

### 4.6 Gestión Documental y Patrimonio

**Repositorio de documentos:**
- Versionado de archivos 
- Previsualización de archivos 
- Control de acceso granular

**Inventario de patrimonio:**
- Ficha detallada por bien (descripción, fotos, valoración)
- Historial de adquisiciones y movimientos
- Reporting de valoración total

---

## 5. Roles y Permisos

### 5.1 Sistema RBAC (Role-Based Access Control)

**Roles predefinidos:**

| Rol | Descripción | Permisos típicos |
|-----|-------------|------------------|
| **Super Admin** | Administrador de plataforma | Acceso total al sistema, gestión de organizaciones |
| **Admin Organización** | Administrador del grupo/hermandad | CRUD completo sobre todos los módulos de su organización |
| **Moderador** | Gestión de comunicación, documental y económica | secretaría, tesoreria y comunicación |
| **Miembro/Hermano** | Usuario estándar | Consulta de información, autogestión de perfil, participación |
| **Invitado** | Acceso limitado | Solo visualización de información pública |

**Permisos granulares:**
- Permisos por módulo (activar/desactivar funcionalidades)
- Permisos por acción (crear, leer, actualizar, eliminar)
- Roles personalizados por organización

## 6. Casos de Uso Específicos

### 6.1 Hermandad con 2000 hermanos

**Funcionalidades para el hermano:**
- **Perfil personal completo**:
  - Número de hermano único
  - Estado de cuotas (al día / pendiente)
  - Derechos a voto actualizados
  - Fecha de inscripción y antigüedad
  - Historial de cargos desempeñados

- **Historial de participaciones**:
  - Estaciones de penitencia
  - Cultos y eventos especiales
  - Voluntariados
  - Estadísticas personales

- **Gestión de pagos**:
  - Cuotas pendientes y abonadas
  - Pago online con múltiples métodos
  - Descarga de recibos históricos
  - Domiciliación bancaria

- **Información corporativa**:
  - Patrimonio de la hermandad
  - Inventario de enseres (pasos, mantos, insignias)
  - Calendario de cultos
  - Noticias y comunicados

- **Calendario integrado**:
  - Eventos próximos
  - Recordatorios automáticos
  - Inscripción a eventos

**Funcionalidades para el administrador:**
- **Estación de penitencia**:
  - Planificación 
  - Asignación de funciones
  - Control de asistencia

- **Gestión de hermanos**:
  - Alta, baja y modificación de hermanos
  - Importación/exportación masiva
  - Segmentación por criterios múltiples

- **Administración de patrimonio**:
  - Inventario valorado
  - Mantenimientos programados
  - Fotografías y documentación técnica

- **Sistema de comunicación**:
  - Comunicados oficiales

---

### 6.3 Sociedad, Asociación o Grupo Cultural

**Funcionalidades principales:**

-   **Registro de socios**:
    - Censo actualizado
    - Junta directiva y cargos
    - Renovaciones anuales
    - Certificados de socio

-   **Gestión documental legal**:
    - Actas de reuniones
    - Estatutos y reglamentos
    - Documentación fiscal
    - Contratos y convenios

-   **Control de cuotas y actividades**:
    - Cuotas de socio
    - Actividades de pago
    - Eventos culturales
    - Excursiones

-   **Administración de patrimonio**:
    - Sede social (salón, mobiliario)
    - patrimonio

-   **Convocatorias y eventos**:
    - Asambleas generales
    - Reuniones de junta
    - Eventos culturales
    - Confirmación de asistencia
    - Votaciones online


---

### 12.2 Referencias

- [Symfony Documentation](https://symfony.com/doc/current/index.html)
- [Doctrine ORM Documentation](https://www.doctrine-project.org/projects/orm.html)
- [PostgreSQL Documentation](https://www.postgresql.org/docs/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Twig Documentation](https://twig.symfony.com/)
- [API Platform Documentation](https://api-platform.com/docs/)
- [OWASP Security Guidelines](https://owasp.org)
- [GDPR Official Text](https://gdpr-info.eu)
- [Web Content Accessibility Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)

---

## 13. Conclusión

**Custodya** se posiciona como la solución SaaS integral de referencia para la gestión moderna de organizaciones comunitarias en España y Latinoamérica. 

Mediante una arquitectura técnica sólida, funcionalidades específicas para cada tipo de organización y un enfoque en la automatización inteligente, Custodya permite a hermandades, asociaciones y clubes deportivos concentrarse en su misión mientras la plataforma **custodia** lo verdaderamente importante: sus datos, su patrimonio y su comunidad.

---

**Versión del documento**: 2.0  
**Fecha**: 8 de enero de 2026  
**Estado**: Especificación Técnica Completa
