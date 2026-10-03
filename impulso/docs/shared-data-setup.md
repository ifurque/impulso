# Datos compartidos entre equipos

La aplicación separa el código de los datos: Git sincroniza el código; Supabase PostgreSQL guarda usuarios, emprendimientos, productos y publicaciones, y Supabase Storage guarda las imágenes. Los equipos conectados al mismo proyecto ven los mismos datos sin versionar snapshots de SQLite.

## Crear el servicio

1. Crea un proyecto de Supabase en una región cercana a quienes usarán Impulso. Guarda la contraseña de PostgreSQL en un gestor de contraseñas.
2. En **Storage**, crea un bucket llamado `impulso-media` y márcalo como público. Este bucket solo debe contener medios que ya sean públicos en Impulso, como fotos de emprendimientos y publicaciones.
3. En los ajustes de Storage, crea credenciales S3 dedicadas. No uses la `service_role key` como clave S3.
4. En **Connect**, copia la cadena del **Session Pooler** para obtener host, puerto, usuario y base. El pooler de sesión es la opción recomendada para equipos locales cuando la conexión directa no está disponible por IPv4.

## Configurar cada equipo

Cada equipo mantiene sus secretos en `impulso/.env`, que no se debe subir a Git. Completa los campos usando los valores del panel de Supabase:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=<host-del-session-pooler>
DB_PORT=<puerto-del-session-pooler>
DB_DATABASE=postgres
DB_USERNAME=<usuario-del-session-pooler>
DB_PASSWORD=<contraseña-de-postgres>
DB_SSLMODE=require

PUBLIC_FILESYSTEM_DRIVER=s3
AWS_ENDPOINT=https://<project-ref>.storage.supabase.co/storage/v1/s3
AWS_URL=https://<project-ref>.supabase.co/storage/v1/object/public/impulso-media
AWS_DEFAULT_REGION=<region-del-proyecto>
AWS_BUCKET=impulso-media
AWS_ACCESS_KEY_ID=<s3-access-key>
AWS_SECRET_ACCESS_KEY=<s3-secret-key>
AWS_USE_PATH_STYLE_ENDPOINT=false
```

No compartas ni publiques el `.env`, la contraseña de PostgreSQL ni las claves S3. Los valores de `.env.example` son solo una plantilla.

## Primera importación

Haz primero una copia de seguridad de `database/database.sqlite` y de `public/uploads` en una ubicación que no esté dentro del repositorio. No borres ni reemplaces los archivos locales durante la migración.

1. Habilita `pdo_pgsql` en el `php.ini` que usa PHP 8.3. En Windows, comprueba `php -m` y que aparezca `pdo_pgsql`.
2. Configura y verifica las variables anteriores en `.env`.
3. Crea el esquema vacío en Supabase: `php artisan migrate --force`.
4. Haz una prueba de conexión: `php artisan migrate:status`.
5. Importa el SQLite y los uploads: `php artisan app:import-local-data --confirm`.

El importador se detiene si el destino no es PostgreSQL, si faltan tablas o si alguna tabla de datos ya contiene filas. No copia caché, sesiones, colas ni el registro local de migraciones. Sube primero los medios sin sobrescribir objetos existentes y conserva intactos SQLite y los archivos fuente. Si falla solo la subida, reintenta con `php artisan app:import-local-data --confirm --files-only`.

No ejecutes la importación en una base compartida que ya tenga datos. Los siguientes equipos no importan copias locales: configuran el mismo `.env`, hacen `git pull`, ejecutan `composer install` y `php artisan migrate --force` para aplicar nuevas migraciones.

## Operación y respaldos

- Una vez confirmada la importación en todos los perfiles y publicaciones, deja de usar SQLite como base compartida. `database/database.sqlite` no es una estrategia de sincronización y no debe volver a resolverse mediante merges de Git.
- Configura respaldos de PostgreSQL y del bucket desde el proveedor, y prueba periódicamente la restauración. La retención y los respaldos dependen del plan de Supabase.
- Los datos privados deben permanecer en PostgreSQL o en almacenamiento privado; no guardes documentos privados en el bucket público de medios.
- Las migraciones nuevas siguen versionándose en Git. No versionamos los datos vivos de los clientes.