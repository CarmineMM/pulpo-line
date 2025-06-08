# Pulpo Line - API del Clima

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)

API desarrollada en Laravel para consultar información meteorológica y gestionar ciudades favoritas de los usuarios.

## 🚀 Características Principales

-   **Autenticación de Usuarios**

    -   Registro y autenticación con Laravel Sanctum
    -   Gestión de tokens de acceso

-   **Consulta del Clima**

    -   Obtén información meteorológica en tiempo real
    -   Incluye temperatura, humedad, velocidad del viento y más

-   **Gestión de Favoritos**

    -   Añade ciudades a tu lista de favoritos
    -   Consulta tu historial de búsquedas

-   **Documentación Completa**
    -   Documentación de la API con Swagger/OpenAPI
    -   Ejemplos de solicitudes y respuestas

## 📋 Requisitos Previos

-   PHP >= 8.1
-   Composer
-   (Cualquiera de las bases de datos soportador por Laravel)
-   Clave de API de [WeatherAPI](https://www.weatherapi.com/)

## 🛠 Instalación

1. **Clonar el repositorio**

    ```bash
    git clone https://github.com/CarmineMM/pulpo-line.git
    cd pulpo-line
    ```

2. **Instalar dependencias de PHP**

    ```bash
    composer install
    ```

3. **Configurar el entorno**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Configurar la base de datos**

    - Crear una base de datos MySQL o Postgres
    - Configurar las variables de conexión en `.env`:
        ```
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=nombre_base_datos
        DB_USERNAME=usuario
        DB_PASSWORD=contraseña
        ```

5. **Configurar la API del Clima**
   Obtén una clave de API en [WeatherAPI](https://www.weatherapi.com/) y configúrala en `.env`:

    ```
    WEATHER_API_KEY=tu_clave_api
    ```

6. **Ejecutar migraciones y seeders**

    ```bash
    php artisan migrate --seed
    ```

7. **Iniciar el servidor**

    ```bash
    php artisan serve
    ```

8. **Acceder a la documentación**
   Abre tu navegador en:
    ```
    http://localhost:8000/api/documentation
    ```

## 🔍 Uso de la API

### Autenticación

1. **Registro de Usuario**

    ```http
    POST /api/register
    Content-Type: application/json

    {
        "name": "Usuario Ejemplo",
        "email": "usuario@ejemplo.com",
        "password": "contraseña123",
        "password_confirmation": "contraseña123"
    }
    ```

2. **Inicio de Sesión**

    ```http
    POST /api/login
    Content-Type: application/json

    {
        "email": "usuario@ejemplo.com",
        "password": "contraseña123"
    }
    ```

    La respuesta incluirá un token de acceso que debes usar en las solicitudes autenticadas.

### Consultar el Clima

```http
GET /api/weather/Madrid
Authorization: Bearer [tu_token]
```

### Gestionar Favoritos

**Añadir a favoritos:**

```http
POST /api/favorites
Authorization: Bearer [tu_token]
Content-Type: application/json

{
    "city_name": "Barcelona"
}
```

**Ver favoritos:**

```http
GET /api/favorites
Authorization: Bearer [tu_token]
```

**Eliminar de favoritos:**

```http
DELETE /api/favorites/1
Authorization: Bearer [tu_token]
```

## 🧪 Ejecutar Pruebas

```bash
php artisan test
```

## 📚 Documentación de la API

La documentación completa de la API está disponible en:

```
http://localhost:8000/api/documentation
```

## 🛠️ Tecnologías Utilizadas

-   **Backend:** Laravel ^12.0
-   **Autenticación:** Laravel Sanctum
-   **Base de Datos:** MySQL, Postgres o cualquier base de datos soportada por Laravel
-   **Documentación:** Swagger/OpenAPI
-   **Pruebas:** PHPUnit

## 📄 Licencia

Este proyecto fue realizado para solventar la prueba técnica de Pulpo Line.

## ✨ Características Adicionales

-   Validación de datos en todas las solicitudes
-   Manejo de errores personalizado
-   Respuestas JSON estandarizadas
-   Sistema de caché para consultas a la API del clima
-   Internacionalización (inglés y español)

## 📧 Contacto

¿Preguntas? No dudes en contactarme en [carminemaggiom@gmail.com](mailto:carminemaggiom@gmail.com)
