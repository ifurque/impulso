# Seguridad y privacidad

Esta guía reúne controles operativos para el código actual. No constituye una auditoría de seguridad ni reemplaza la revisión de los permisos y obligaciones legales aplicables a los datos personales tratados.

## Secretos y configuración

- Mantén `impulso/.env` fuera de Git. Usa Vercel Environment Variables u otro gestor de secretos para producción; restringe quién puede leerlos y rotarlos.
- Genera una `APP_KEY` estable y diferente para cada entorno. No la cambies en cada despliegue: hacerlo invalida datos cifrados y sesiones asociadas.
- En producción define `APP_DEBUG=false` y una URL HTTPS correcta. No publiques trazas completas ni exportes logs con datos personales.
- Las variables `VITE_*` se incorporan al bundle del navegador y son públicas. Solo coloca allí URLs y claves publicables. Nunca incluyas contraseñas de PostgreSQL, claves privadas S3 ni la clave Supabase `service_role`.
- Usa una credencial de base de datos con privilegios mínimos necesarios. Mantén separado desarrollo, Preview y Production.

## Cuentas, permisos y sesiones

- El panel exige autenticación; el control de acceso al negocio y a la administración global debe seguir las comprobaciones del servidor. No confíes en botones ocultos o validaciones hechas solo en el navegador.
- Las rutas de administración global requieren rol `superadmin`. Limita y revisa periódicamente las cuentas con ese rol; conserva al menos una cuenta administrativa activa.
- Los enlaces de verificación y gestión de turnos son credenciales temporales: no los copies a tickets, capturas, analítica ni logs compartidos.
- En producción usa `SESSION_SECURE_COOKIE=true`, `SESSION_HTTP_ONLY=true` y `SESSION_SAME_SITE=lax` salvo que un flujo cross-site requiera otro valor revisado.
- Configura un mecanismo de sesión compartido en producción; para Vercel, usa `SESSION_DRIVER=database` y aplica antes las migraciones. El valor de desarrollo `SESSION_DRIVER=file` depende del filesystem local y no comparte estado entre instancias serverless.
- Configura el dominio de sesión de acuerdo con el dominio real; no amplíes la cookie a dominios/subdominios que no necesiten compartirla.
- Aplica actualizaciones de dependencias y revisa avisos de seguridad de Laravel, PHP y paquetes Node/Composer antes de promover cambios.

## Datos personales

Impulso puede tratar nombres, email, teléfono, documento, domicilio, pedidos, consultas, turnos y datos de perfil. Antes de habilitar el servicio:

- Define quién puede acceder a cada categoría, por cuánto tiempo se conserva y cómo se atienden solicitudes de acceso, corrección o eliminación.
- No uses datos reales de clientes en pruebas locales ni en capturas para soporte.
- Comparte logs y dumps solo después de quitar nombres, emails, teléfonos, direcciones, tokens, contraseñas y otros identificadores.
- Revisa las reglas de retención y las obligaciones de privacidad que correspondan a la jurisdicción y operación del servicio.

## Archivos y almacenamiento

- El disco `public` se sirve como contenido público. Sube allí únicamente fotos y medios que el negocio haya decidido publicar; no es lugar para DNI, comprobantes privados ni documentos de clientes.
- En despliegues serverless configura un destino S3 compatible persistente. No supongas que `public/uploads` escrito por una función seguirá disponible después de una ejecución o nuevo despliegue.
- Usa credenciales S3 dedicadas y restringidas al bucket necesario. No uses `service_role` como credencial S3. Si un archivo debe ser privado, usa un bucket privado y URLs firmadas/controles de acceso; no lo enlaces desde la ruta pública.
- Verifica límites de tamaño y tipos permitidos en el servidor. Evita confiar en el nombre o extensión enviados por el cliente.

## Base de datos y respaldos

- SQLite es el valor predeterminado local. Para varios equipos o producción usa PostgreSQL persistente; `git pull` no replica cambios de SQLite.
- Usa TLS/SSL en conexiones remotas según el proveedor y protege las credenciales del pooler.
- Configura respaldos de la base y de los medios con retención adecuada; prueba restauraciones y documenta responsables y tiempos de recuperación.
- Ejecuta importaciones iniciales solo después de confirmar el destino y contar con un respaldo. El importador rechaza destinos que ya tengan datos como salvaguarda; no eludas esa protección borrando tablas a ciegas.

## Correo, SMS y registros

- `MAIL_MAILER=log` no entrega correo: escribe los mensajes en logs. Cambia a un proveedor real antes de producción y evita que los logs queden accesibles a usuarios no autorizados.
- `LogSmsSender` registra destinatario y contenido, incluido el enlace firmado de verificación telefónica. No uses ese adaptador en producción con datos reales; instala y configura un proveedor SMS y evita registrar cuerpos de mensajes.
- Configura nivel, acceso y retención de logs para reducir la exposición de datos personales y facilitar la investigación de errores.

## Respuesta ante incidentes

1. Contén el incidente: revoca o rota la credencial expuesta y limita accesos afectados.
2. Conserva evidencia necesaria con acceso restringido; no reenvíes secretos en chats ni issues.
3. Determina alcance, datos y usuarios afectados, y registra fechas y acciones sin copiar datos innecesarios.
4. Restaura desde respaldos solo después de comprobar integridad y alcance.
5. Evalúa las obligaciones de notificación aplicables y registra las medidas preventivas tomadas.

Para reportes de vulnerabilidad, no publiques detalles explotables en issues públicos. Contacta a los responsables del repositorio por un canal privado y coordina la divulgación. Los problemas funcionales no sensibles pueden reportarse en [GitHub Issues](https://github.com/ifurque/impulso/issues).

## Referencias

- [Despliegue en Vercel](despliegue-vercel.md)
- [Errores y limitaciones conocidas](../ERRORES.md)
- [Configuración de datos compartidos](../impulso/docs/shared-data-setup.md)
- [Configuración de ejemplo](../impulso/.env.example)
