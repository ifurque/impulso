# Instalación y configuración

## 1. Requisitos del sistema

Antes de instalar el proyecto, asegúrate de contar con:

- PHP 8.2 o superior
- Composer
- Node.js 18 o superior
- npm
- SQLite o una base de datos compatible con Laravel

## 2. Clonar el proyecto

```bash
git clone <url-del-repositorio>
cd impulso
```

## 3. Instalar dependencias de PHP

```bash
composer install
```

## 4. Configurar variables de entorno

Copia el archivo de ejemplo:

```bash
cp .env.example .env
```

Luego genera la clave de la aplicación:

```bash
php artisan key:generate
```

La configuración por defecto del proyecto usa SQLite:

```env
DB_CONNECTION=sqlite
```

## 5. Preparar la base de datos

```bash
php artisan migrate
```

Si no existe la base de datos SQLite, puedes crearla antes de migrar.

## 6. Instalar dependencias frontend

```bash
npm install
```

## 7. Compilar assets

Para entorno de desarrollo:

```bash
npm run dev
```

Para producción:

```bash
npm run build
```

## 8. Ejecutar la aplicación

```bash
php artisan serve
```

La aplicación quedará disponible normalmente en:

```text
http://localhost:8000
```

## 9. Script de configuración recomendado

El proyecto incluye un script útil para levantar el entorno:

```bash
composer run setup
```

Este comando intenta realizar la instalación común de dependencias, preparar el archivo .env, generar la clave, ejecutar migraciones y compilar assets.

## 10. Recomendaciones de entorno

- Para desarrollo local, usa SQLite.
- Para producción, considera MySQL o Postgres con configuración adecuada.
- Define APP_URL y parámetros de correo según el entorno.
- Usa variables de entorno para claves y configuraciones sensibles.

## 11. Troubleshooting rápido

### Error de clave de aplicación

```bash
php artisan key:generate
```

### Error de migraciones

```bash
php artisan migrate:fresh --seed
```

### Error de assets frontend

```bash
rm -rf node_modules
npm install
npm run build
```

## 12. Conclusión

El proceso de instalación es directo y estándar para Laravel. La base del proyecto queda lista con la configuración mínima para comenzar a operar en entorno local y prepararse para despliegue posterior.
