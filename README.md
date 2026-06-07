# 🏥 Smart Clinic — Sistema de Gestión de Citas Médicas

Sistema web de gestión de citas médicas desarrollado como proyecto académico para la asignatura de **Integración Continua**. Permite el registro de pacientes y doctores, la programación de citas con detección de conflictos de horario, y la autenticación segura de usuarios.

## 📋 Descripción

Smart Clinic es una aplicación monolítica PHP que sigue el patrón **MVC (Modelo-Vista-Controlador)**, contenerizada con **Docker Compose** e integrada con **Jenkins** para Integración Continua (CI).

### Funcionalidades principales

- **Autenticación segura** — Login con bcrypt, bloqueo por intentos fallidos (5 intentos → 15 min de bloqueo), sesiones con expiración por inactividad (30 min)
- **Gestión de pacientes** — CRUD completo con validación de datos y detección de duplicados
- **Gestión de doctores** — CRUD completo con ordenamiento alfabético y verificación de unicidad (email, licencia)
- **Programación de citas** — Agendamiento con validación de conflictos en franjas de 30 minutos (tanto para doctor como para paciente)
- **Cancelación de citas** — Control de estados (programada → cancelada)
- **Health check** — Endpoint `/health` para verificar conectividad con la base de datos
- **Pipeline CI con Jenkins** — Build, test y deploy automatizados

---

## 🛠️ Tecnologías

| Componente | Tecnología | Versión |
|---|---|---|
| Backend | PHP | 8.2 |
| Servidor web | Apache | 2.4 |
| Base de datos | MySQL | 8.0 |
| Frontend | Bootstrap (CDN) | 5.3 |
| Contenerización | Docker + Docker Compose | 29.x |
| CI/CD | Jenkins | LTS |
| Arquitectura | MVC (sin framework) | — |

---

## 🚀 Cómo arrancar el proyecto

### Prerrequisitos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) instalado y corriendo
- Git (para clonar el repositorio)

### Paso 1: Clonar el repositorio

```bash
git clone https://github.com/salgado-2022/SmartClinic.git
cd SmartClinic
```

### Paso 2: Levantar los contenedores

```bash
docker-compose up --build -d
```

Este comando levanta 3 servicios:

| Servicio | Puerto | Descripción |
|---|---|---|
| `app` | `http://localhost:8080` | Aplicación PHP + Apache |
| `db` | `localhost:3306` | Base de datos MySQL |
| `jenkins` | `http://localhost:9090` | Servidor de Integración Continua |

**Espera ~30 segundos** a que MySQL termine de inicializar. Verifica con:

```bash
docker-compose ps
```

### Paso 3: Acceder a la aplicación

```
http://localhost:8080
```

### Paso 4: Iniciar sesión

| Campo | Valor |
|---|---|
| Email | `admin@smartclinic.com` |
| Contraseña | `Admin123!` |

---

## 🔄 Integración Continua con Jenkins

### Acceder a Jenkins

```
http://localhost:9090
```

### Primera vez — obtener contraseña inicial

```bash
docker exec smartclinic_jenkins cat /var/jenkins_home/secrets/initialAdminPassword
```

### Configurar el Pipeline

1. Ingresa a Jenkins con la contraseña inicial
2. Instala los plugins sugeridos
3. Crea tu usuario administrador
4. Crea un nuevo job:
   - Click en **"Nueva Tarea"**
   - Nombre: `SmartClinic`
   - Tipo: **Pipeline**
   - Click en "OK"
5. En la configuración del job:
   - Sección **"Pipeline"**
   - Definition: **"Pipeline script from SCM"**
   - SCM: **Git**
   - Repository URL: `https://github.com/salgado-2022/SmartClinic.git`
   - Branch: `*/main`
   - Script Path: `Jenkinsfile`
   - Click en **"Guardar"**
6. Click en **"Construir ahora"**

### Etapas del Pipeline

El `Jenkinsfile` define 5 etapas:

```
1. Checkout    → Descarga el código del repositorio
2. Build       → Construye la imagen Docker de la app
3. Test        → Levanta los contenedores y verifica que estén corriendo
4. Health Check → Verifica que el endpoint /health responda HTTP 200
5. Deploy      → Confirma el despliegue exitoso
```

---

## 🔧 Comandos útiles

### Ver logs de los contenedores

```bash
docker-compose logs -f
```

### Ver logs solo de la aplicación

```bash
docker-compose logs -f app
```

### Detener los contenedores (conserva datos)

```bash
docker-compose down
```

### Detener y eliminar todo (contenedores, volúmenes, imágenes)

```bash
docker-compose down --volumes --rmi all
```

### Reconstruir después de cambios en el código

```bash
docker-compose up --build -d
```

### Verificar el health check

```bash
curl http://localhost:8080/health
```

Respuesta esperada: `{"status":"ok"}`

---

## 📁 Estructura del proyecto

```
SmartClinic/
├── docker-compose.yml          # Orquestación: app + db + jenkins
├── Dockerfile                  # Imagen PHP 8.2 + Apache + PDO
├── Dockerfile.jenkins          # Imagen Jenkins con Docker y Compose
├── Jenkinsfile                 # Pipeline CI (5 etapas)
├── .env.example                # Plantilla de variables de entorno
├── .gitignore                  # Archivos excluidos de Git
├── init.sql                    # Esquema de BD + usuario admin seed
├── app/
│   ├── index.php               # Front Controller (punto de entrada)
│   ├── .htaccess               # Reescritura de URLs → index.php
│   ├── config/
│   │   ├── database.php        # Conexión PDO (lee variables de entorno)
│   │   └── routes.php          # Registro de todas las rutas
│   ├── controllers/
│   │   ├── AuthController.php        # Login / Logout
│   │   ├── DashboardController.php   # Redirección raíz
│   │   ├── PatientController.php     # CRUD Pacientes
│   │   ├── DoctorController.php      # CRUD Doctores
│   │   ├── AppointmentController.php # Citas médicas
│   │   └── HealthController.php      # Endpoint /health
│   ├── models/
│   │   ├── User.php            # Autenticación y bloqueo
│   │   ├── Patient.php         # Operaciones de pacientes
│   │   ├── Doctor.php          # Operaciones de doctores
│   │   └── Appointment.php     # Citas + detección de conflictos
│   ├── views/
│   │   ├── layouts/main.php    # Layout base con Bootstrap 5
│   │   ├── auth/login.php      # Formulario de login
│   │   ├── patients/           # Vistas CRUD pacientes
│   │   ├── doctors/            # Vistas CRUD doctores
│   │   ├── appointments/       # Vistas de citas
│   │   └── errors/             # Páginas 404 y 503
│   ├── helpers/
│   │   ├── Router.php          # Enrutador con soporte de parámetros
│   │   ├── Session.php         # Gestión de sesiones + timeout
│   │   └── Validator.php       # Validación centralizada
│   └── middleware/
│       └── AuthMiddleware.php  # Protección de rutas
```

---

## 🗄️ Base de datos

### Esquema

El sistema usa 4 tablas:

- **users** — Credenciales de administrador, intentos fallidos, bloqueo temporal
- **patients** — Datos de pacientes (nombre, fecha nacimiento, email, teléfono, documento)
- **doctors** — Datos de doctores (nombre, email, teléfono, especialidad, licencia)
- **appointments** — Citas médicas (paciente, doctor, fecha, hora, estado)

### Datos iniciales (seed)

Al primer arranque se crea automáticamente:
- Un usuario administrador: `admin@smartclinic.com` / `Admin123!`

---

## 🔒 Seguridad

- Contraseñas almacenadas con **bcrypt** (PHP `password_hash`)
- Mensajes de error genéricos en login (no revelan si el email o la contraseña es incorrecta)
- Bloqueo automático tras **5 intentos fallidos** consecutivos (15 minutos)
- Sesiones con **timeout de 30 minutos** por inactividad
- Páginas de error **no exponen** credenciales, rutas internas ni stack traces
- Consultas SQL con **prepared statements** (prevención de SQL injection)
- Salida HTML con **htmlspecialchars** (prevención de XSS)

---

## 📝 Flujo de uso

1. El administrador inicia sesión
2. Registra **pacientes** (nombre, fecha nacimiento, email, teléfono, documento)
3. Registra **doctores** (nombre, email, teléfono, especialidad, licencia)
4. Programa **citas** seleccionando paciente + doctor + fecha + hora
5. El sistema valida automáticamente que no haya conflictos de horario
6. El administrador puede **cancelar** citas programadas

---

## 👨‍💻 Autor

Proyecto desarrollado para la asignatura de **Integración Continua** — Politécnico Grancolombiano.

---

## 📄 Licencia

Este proyecto es de uso académico.
