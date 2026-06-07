# Documentación Funcional — Smart Clinic

## 1. Planteamiento del Problema

En la actualidad, muchas clínicas pequeñas y medianas en Colombia siguen gestionando sus citas médicas de forma manual: agendas en papel, hojas de cálculo o sistemas desconectados que no garantizan integridad de datos ni trazabilidad. Esto genera múltiples inconvenientes operativos:

- **Conflictos de horarios:** Al no existir un mecanismo automatizado de validación, es frecuente que se asignen dos pacientes al mismo doctor en la misma franja horaria, generando retrasos, reprogramaciones y malestar tanto en pacientes como en profesionales de la salud.

- **Pérdida de información:** Los registros en papel o archivos locales son susceptibles a deterioro, extravío o errores de transcripción. No existe un respaldo centralizado que permita recuperar datos ante una falla.

- **Falta de estandarización:** Sin un sistema unificado, cada persona de la recepción maneja los datos a su criterio, lo que genera inconsistencias en formatos de nombres, documentos de identidad y datos de contacto.

- **Dificultad para escalar:** A medida que la clínica crece en número de doctores y pacientes, un sistema manual se vuelve insostenible, incrementando los tiempos de espera y la probabilidad de errores humanos.

- **Ausencia de prácticas de desarrollo modernas:** Desde la perspectiva de ingeniería de software, no se aplican metodologías de integración continua ni despliegue automatizado, lo que dificulta la evolución y el mantenimiento del sistema a largo plazo.

Este proyecto surge como respuesta a la necesidad de digitalizar y automatizar la gestión de citas médicas mediante una aplicación web que, además de resolver el problema funcional, sirva como caso de estudio para la implementación de prácticas de Integración Continua (CI) en un entorno académico.

---

## 2. Formulación del Problema y Preguntas de Investigación

### 2.1 Formulación del Problema

¿Cómo diseñar e implementar un sistema web de gestión de citas médicas que permita el registro de pacientes y doctores, la programación de citas con validación de conflictos de horario, y la autenticación segura de usuarios, utilizando una arquitectura MVC en PHP contenerizada con Docker Compose y preparada para la adopción de prácticas de Integración Continua?

### 2.2 Preguntas de Investigación

1. ¿Cuáles son los requisitos funcionales y no funcionales mínimos para un sistema de gestión de citas médicas que resuelva los problemas de conflicto de horarios y duplicación de datos?

2. ¿Cómo se puede estructurar una aplicación web monolítica siguiendo el patrón MVC en PHP de manera que sea mantenible, extensible y preparada para pruebas automatizadas?

3. ¿De qué manera la contenerización con Docker y Docker Compose facilita la estandarización del entorno de desarrollo y el despliegue reproducible de una aplicación web con base de datos?

4. ¿Qué beneficios aporta la adopción de prácticas de Integración Continua al ciclo de vida de desarrollo de un sistema de información en un contexto académico?

5. ¿Cómo se puede garantizar la seguridad de la autenticación de usuarios mediante mecanismos de hashing de contraseñas, bloqueo por intentos fallidos y gestión de sesiones con timeout?

---

## 3. Objetivos

### 3.1 Objetivo General

Desarrollar un sistema web de gestión de citas médicas (Smart Clinic) que permita el registro de pacientes y doctores, la programación y cancelación de citas con detección de conflictos de horario, y la autenticación segura de usuarios, implementado como un monolito PHP con arquitectura MVC, contenerizado mediante Docker Compose y preparado para la integración de pipelines de CI/CD.

### 3.2 Objetivos Específicos

1. **Diseñar la arquitectura del sistema** siguiendo el patrón Modelo-Vista-Controlador (MVC) en PHP, definiendo la estructura de directorios, las interfaces de los componentes y el esquema de base de datos relacional.

2. **Implementar el módulo de autenticación** con inicio de sesión basado en credenciales, hashing de contraseñas con bcrypt, bloqueo automático tras 5 intentos fallidos consecutivos (15 minutos), y expiración de sesión por inactividad (30 minutos).

3. **Implementar el módulo de gestión de pacientes** con operaciones CRUD completas (crear, leer, actualizar), validación de datos de entrada con reglas específicas por campo, y detección de duplicados por correo electrónico y documento de identidad.

4. **Implementar el módulo de gestión de doctores** con operaciones CRUD, validación de datos, ordenamiento alfabético y detección de duplicados por correo electrónico y número de licencia profesional.

5. **Implementar el módulo de programación de citas médicas** con validación de fecha futura, rango horario permitido (08:00–18:00), detección de conflictos de horario tanto para el doctor como para el paciente en franjas de 30 minutos, y funcionalidad de cancelación con control de estados.

6. **Contenerizar la aplicación** mediante Docker Compose con dos servicios (Apache+PHP y MySQL), persistencia de datos con volúmenes nombrados, inicialización automática del esquema de base de datos, y verificación de salud del servicio (health check).

7. **Documentar el proyecto** de forma completa incluyendo requisitos funcionales, diseño técnico, plan de implementación y esta documentación funcional, sentando las bases para la futura incorporación de pipelines de Integración Continua.

---

## 4. Justificación

### 4.1 Justificación Técnica

La elección de PHP como lenguaje de desarrollo responde a su amplia adopción en el ecosistema web y su facilidad para implementar el patrón MVC sin dependencia de frameworks pesados. Esto permite comprender los fundamentos de la arquitectura sin la abstracción que introducen herramientas como Laravel o Symfony, lo cual es valioso en un contexto académico.

La contenerización con Docker y Docker Compose aporta:

- **Reproducibilidad del entorno:** Cualquier miembro del equipo o evaluador puede levantar el sistema idéntico con un solo comando (`docker-compose up`), eliminando el clásico problema de "en mi máquina funciona".
- **Aislamiento de dependencias:** La versión de PHP, Apache y MySQL queda fijada en el Dockerfile y docker-compose.yml, evitando conflictos con otros proyectos en la misma máquina.
- **Base para CI/CD:** La infraestructura como código (IaC) que proporciona Docker es el primer paso para implementar pipelines de integración y despliegue continuo con herramientas como Jenkins, GitHub Actions o GitLab CI.

### 4.2 Justificación Académica

Este proyecto se desarrolla en el marco de la asignatura de **Integración Continua**, donde el objetivo pedagógico es que los estudiantes experimenten de primera mano el ciclo completo de desarrollo de software moderno:

1. **Especificación de requisitos** formales con criterios de aceptación verificables.
2. **Diseño arquitectónico** documentado con diagramas y definición de interfaces.
3. **Implementación incremental** siguiendo un plan de tareas con dependencias.
4. **Contenerización** como práctica estándar de la industria.
5. **Preparación para automatización** de pruebas y despliegues.

El sistema Smart Clinic es lo suficientemente complejo para demostrar estas prácticas (autenticación, CRUD múltiple, validaciones, detección de conflictos) pero lo suficientemente acotado para ser abordable como proyecto académico en un semestre.

### 4.3 Justificación Funcional

Desde la perspectiva del usuario final (personal administrativo de una clínica), el sistema resuelve problemas reales:

- **Elimina la doble asignación de horarios** mediante validación automática de conflictos en franjas de 30 minutos.
- **Previene la duplicación de registros** validando unicidad de documentos de identidad, correos electrónicos y números de licencia.
- **Protege el acceso al sistema** con autenticación segura, bloqueo por fuerza bruta y expiración automática de sesiones inactivas.
- **Centraliza la información** en una base de datos relacional con integridad referencial y persistencia garantizada.

---

## 5. Alcance del Proyecto

### 5.1 Incluido en el MVP

- Autenticación de usuarios (administrador único)
- Registro, consulta y edición de pacientes
- Registro, consulta y edición de doctores
- Programación y cancelación de citas médicas
- Validación de conflictos de horario (doctor y paciente)
- Contenerización con Docker Compose (app + base de datos)
- Endpoint de verificación de salud del sistema
- Documentación técnica y funcional

### 5.2 Fuera del Alcance (trabajo futuro)

- Sistema de roles (paciente, doctor, admin)
- Notificaciones por correo electrónico o SMS
- Calendario visual interactivo
- Reportes y estadísticas
- Pipeline de CI/CD con Jenkins (siguiente fase del curso)
- Pruebas automatizadas unitarias y de integración
- Despliegue en la nube (AWS, Azure, GCP)

---

## 6. Tecnologías Utilizadas

| Componente | Tecnología | Versión |
|---|---|---|
| Lenguaje backend | PHP | 8.2 |
| Servidor web | Apache | 2.4 (incluido en imagen Docker) |
| Base de datos | MySQL | 8.0 |
| Frontend (CSS) | Bootstrap | 5.3 |
| Contenerización | Docker + Docker Compose | 29.x |
| Control de versiones | Git + GitHub | - |
| Patrón arquitectónico | MVC (sin framework) | - |
| Autenticación | Sesiones PHP + bcrypt | - |

---

## 7. Estructura del Proyecto

```
SmartClinic/
├── docker-compose.yml          # Orquestación de contenedores
├── Dockerfile                  # Imagen de la aplicación (PHP + Apache)
├── .env.example                # Variables de entorno de ejemplo
├── .gitignore                  # Exclusiones de Git
├── init.sql                    # Script de inicialización de BD
├── docs/                       # Documentación del proyecto
├── app/
│   ├── index.php               # Front Controller
│   ├── .htaccess               # Reescritura de URLs
│   ├── config/
│   │   ├── database.php        # Conexión a BD (lee env vars)
│   │   └── routes.php          # Definición de rutas
│   ├── controllers/            # Controladores MVC
│   ├── models/                 # Modelos (acceso a datos)
│   ├── views/                  # Vistas (HTML + Bootstrap)
│   ├── helpers/                # Clases auxiliares (Router, Session, Validator)
│   └── middleware/             # Middleware de autenticación
```
