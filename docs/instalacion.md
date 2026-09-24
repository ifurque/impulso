# Instalación

## Requisitos

- PHP 8.2+
- Composer
- Node.js y npm
- Base de datos compatible con Laravel (MySQL, SQLite u otra soportada)
- Acceso a terminal

## 1. Clonar el proyecto

```bash
git clone https://github.com/ifurque/impulso.git
cd impulso
```

## 2. Instalar dependencias PHP

```bash
composer install
```

## 3. Configurar variables de entorno

Copia el archivo de ejemplo:

```bash
cp .env.example .env
```

Luego configura la base de datos y la app key:

```bash
php artisan key:generate
```

## 4. Base de datos

Si usás SQLite:

```bash
touch database/database.sqlite
```

Y en `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/ruta/al/proyecto/database/database.sqlite
```

O con MySQL, completar `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

## 5. Ejecutar migraciones

```bash
php artisan migrate
```

## 6. Instalar dependencias frontend

```bash
npm install
```

## 7. Compilar assets

```bash
npm run build
```

O modo desarrollo:

```bash
npm run dev
```

## 8. Ejecutar la aplicación

```bash
php artisan serve
```

La aplicación quedará disponible normalmente en:

```text
http://127.0.0.1:8000
```

## 9. Setup rápido

El proyecto incluye un script de instalación:

```bash
composer run setup
```

Este comando intenta preparar los archivos necesarios, generar la clave, migrar la base y preparar assets.

## 10. Verificación

Para comprobar que el sistema funciona:

```bash
php artisan test
```

Si todo está correcto, la aplicación queda lista para usarse localmente.
