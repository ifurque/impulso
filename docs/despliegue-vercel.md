# Despliegue en Vercel

Esta guía describe la configuración del repositorio actual. La configuración de Vercel está dentro de `impulso/` y usa el runtime PHP `vercel-php@0.9.0`; no implica que exista un proyecto de Vercel conectado ni que el despliegue esté activo.

## 1. Requisitos previos

- Una cuenta de Vercel con acceso al repositorio GitHub.
- Un servicio de PostgreSQL persistente, recomendado Supabase. No uses el SQLite local como base de producción para funciones serverless.
- Un bucket S3 compatible para medios persistentes, por ejemplo Supabase Storage.
- Un proveedor de correo configurado para los correos de la aplicación.
- Las credenciales guardadas como variables de entorno en Vercel, nunca en Git.

La configuración de base de datos y archivos compartidos se detalla en [Datos compartidos entre equipos](../impulso/docs/shared-data-setup.md).

## 2. Crear el proyecto

1. Importa `ifurque/impulso` desde Vercel.
2. En **Project Settings → General → Root Directory**, configura `impulso` y habilita la inclusión de archivos fuera del directorio raíz solo si la plataforma lo solicita para el repositorio conectado.
3. Deja que Vercel lea `vercel.json` del directorio raíz del proyecto configurado. La configuración define:
   - Instalación: `npm ci`.
   - Build: `npm run build && node scripts/prepare-vercel-output.mjs`.
   - Salida estática: `vercel-output`.
   - Runtime PHP para `api/index.php` y reescritura de solicitudes hacia Laravel.
4. Conecta la rama `main` como rama de producción y usa ramas de vista previa para probar cambios antes de publicarlos.

Los comandos de build se ejecutan desde `impulso/`, porque ese es el directorio raíz de la aplicación.

## 3. Variables de entorno

Define las variables en **Project Settings → Environment Variables** para Production y, por separado, Preview cuando corresponda. No copies secretos a `vercel.json`, al README ni a archivos versionados.

### Laravel

- `APP_NAME=Impulso`
- `APP_ENV=production`
- `APP_KEY`: clave generada de forma segura para este entorno; mantenla estable entre despliegues.
- `APP_DEBUG=false`
- `APP_URL`: URL HTTPS canónica del despliegue.
- `LOG_LEVEL`: nivel apropiado para producción, sin registrar información sensible.
- `SESSION_DRIVER=database`: comparte sesiones entre invocaciones y evita depender del disco local efímero. La migración base crea la tabla `sessions`.
- `SESSION_SECURE_COOKIE=true`: limita el envío de la cookie de sesión a HTTPS.
- `SESSION_HTTP_ONLY=true` y `SESSION_SAME_SITE=lax`: valores seguros compatibles con la configuración actual; revisa el flujo si integras navegación cross-site.

### Base de datos

Configura PostgreSQL siguiendo la guía de datos compartidos: `DB_CONNECTION=pgsql`, host, puerto, base, usuario, contraseña y `DB_SSLMODE=require` cuando el proveedor lo requiera. Usa credenciales de mínimo privilegio y una conexión compatible con funciones serverless (pooler del proveedor cuando aplique).

No uses el valor SQLite de `.env.example` como persistencia de producción. El archivo local no es una base compartida ni adecuada para almacenamiento durable entre ejecuciones serverless.

### Archivos y servicios externos

- Para medios persistentes, configura `PUBLIC_FILESYSTEM_DRIVER=s3` y las variables `AWS_ENDPOINT`, `AWS_URL`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY` y `AWS_USE_PATH_STYLE_ENDPOINT` según el proveedor.
- Configura un mailer de producción con sus variables `MAIL_*`. `MAIL_MAILER=log` solo escribe mensajes en logs.
- El adaptador SMS predeterminado registra el mensaje en logs; integrar un proveedor real antes de depender de la verificación telefónica.
- Las variables `VITE_SUPABASE_URL` y `VITE_SUPABASE_PUBLISHABLE_KEY` se usan al compilar la interfaz si se habilita esa integración. Son valores públicos por diseño; nunca pongas una contraseña de base de datos o una clave `service_role` en variables `VITE_*`.

Añade variables de build y runtime en los entornos de Vercel que las necesiten. Si se modifican variables `VITE_*`, genera un nuevo despliegue para recompilar los recursos del navegador.

## 4. Migraciones y datos

Las migraciones están en `impulso/database/migrations` y deben estar versionadas junto con el código. Antes del primer despliegue:

1. Crea la base PostgreSQL persistente y configura sus credenciales en Vercel.
2. Desde un entorno controlado con acceso a la misma base, aplica migraciones con `php artisan migrate --force`.
3. Si se importan datos existentes, sigue el procedimiento protegido de [Datos compartidos entre equipos](../impulso/docs/shared-data-setup.md); no ejecutes una importación inicial contra una base que ya contiene datos.
4. Comprueba la conexión y el estado con `php artisan migrate:status` desde el entorno administrativo adecuado.

No incluyas un dump SQLite de producción en el artefacto de despliegue como mecanismo de persistencia.

La tabla de sesiones se prepara con las migraciones de Laravel; aplica esas migraciones antes de habilitar `SESSION_DRIVER=database`.

## 5. Verificación del despliegue

Después del build, comprueba:

- La URL HTTPS carga inicio, exploración y un perfil público.
- Registro, login, cierre de sesión y perfil funcionan; valida también acceso como cliente, propietario y `superadmin`.
- Pedidos, turnos, respuestas de consulta y notificaciones funcionan con servicios externos reales de prueba.
- Los medios subidos siguen disponibles después de un nuevo despliegue y desde más de una instancia.
- Los logs no contienen credenciales, contraseñas, enlaces de verificación ni datos personales innecesarios.
- Los flujos de administración y pagos permanecen limitados a lo realmente habilitado; la vista de +Impulso actual es una demostración y no procesa cobros.

Ejecuta localmente `composer run test` desde `impulso/` antes de promover cambios. Prueba el build del frontend con `npm run build` desde el mismo directorio.

## 6. Diagnóstico

| Síntoma | Comprobaciones |
| --- | --- |
| El build no encuentra `package.json` o `vercel.json` | Verifica que **Root Directory** sea `impulso`. |
| Error de conexión a PostgreSQL | Revisa variables `DB_*`, SSL, extensión `pdo_pgsql`, permisos y host/puerto del pooler. No imprimas la contraseña en logs. |
| Las imágenes desaparecen después de un despliegue | Comprueba `PUBLIC_FILESYSTEM_DRIVER=s3`, bucket, URL, endpoint y credenciales; el disco local de una función no es persistente. |
| Los correos no llegan | Sustituye el mailer `log` por un proveedor real y revisa su dominio, credenciales y logs del proveedor. |
| Los SMS no llegan | El binding actual es `LogSmsSender`; integrar un proveedor SMS. |
| Error 500 en rutas Laravel | Revisa logs de Vercel y Laravel, `APP_KEY`, configuración de producción y conexión de base. Mantén `APP_DEBUG=false` y elimina secretos antes de compartir logs. |

## 7. Operación

- Configura respaldos de PostgreSQL y del bucket; prueba la restauración periódicamente.
- Revisa límites de conexiones, almacenamiento, ejecución y costos del plan de Vercel y del proveedor de datos.
- Mantén dependencias actualizadas y revisa incidencias de seguridad antes de desplegar.
- Para registrar un defecto funcional, sigue el formato de [Errores conocidos](../ERRORES.md).
