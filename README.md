# Concursos - Plataforma de Trivia y Concursos

Plataforma web para la gestión de concursos y trivias, con roles diferenciados para administradores, empresas y moderadores. Permite crear concursos, preguntas de trivia, gestionar participaciones, premios y notificaciones.

## Tecnologías utilizadas

- **Backend:** PHP / Laravel
- **Base de datos:** MySQL
- **Frontend:** Blade, Tailwind CSS, Vite
- **Gestor de dependencias:** Composer / NPM

## Funcionalidades principales

- Gestión de concursos y trivias con tiempo límite
- Roles de Administrador, Empresa y Moderador
- Sistema de preguntas con importación
- Gestión de premios y participaciones
- Notificaciones automáticas (concursos por finalizar, intentos agotados)
- Reportes y mensajes de contacto
- Autenticación y recuperación de contraseña

##  Requisitos previos

- PHP (versión 8.2 o superior recomendado)
- Composer
- MySQL o un gestor como XAMPP/Laragon
- Node.js y NPM
- Git

## Instalación paso a paso

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/nombre-del-repo.git
cd nombre-del-repo
```

### 2. Instalar las dependencias de PHP

```bash
composer install
```

### 3. Instalar las dependencias de JavaScript

```bash
npm install
```

### 4. Configurar el archivo de entorno

```bash
cp .env.example .env
```

> **Nota:** El archivo `.env` real no está incluido en el repositorio por seguridad. Debes completarlo con tus propios datos locales.

### 5. Generar la clave de la aplicación

```bash
php artisan key:generate
```

### 6. Configurar la base de datos

Edita el archivo `.env` con tus datos de MySQL local:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_tu_base_de_datos
DB_USERNAME=root
DB_PASSWORD=tu_contraseña
```

Crea la base de datos vacía:

```sql
CREATE DATABASE nombre_de_tu_base_de_datos;
```

### 7. Ejecutar las migraciones

```bash
php artisan migrate
```

### 8. Compilar los assets (Tailwind CSS + Vite)

```bash
npm run dev
```

o para producción:

```bash
npm run build
```

### 9. Iniciar el servidor local

```bash
php artisan serve
```

El proyecto quedará disponible en `http://127.0.0.1:8000`



##  Autora

**Daisy Chileno**
Estudiante de Desarrollo de Software
[LinkedIn](https://www.linkedin.com/in/daisy-chileno-tuapanta-20a508232/)
