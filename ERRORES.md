# Errores y limitaciones conocidas

Este registro separa defectos reproducibles de comportamientos que dependen de la configuración o aún no están implementados. Se revisó el repositorio y el tracker de GitHub el **19 de octubre de 2026**. En esa fecha el tracker no tenía incidencias abiertas; esto no garantiza que no existan problemas todavía no reportados.

## Incidencias y limitaciones

| Estado | Comportamiento | Impacto | Acción o solución |
| --- | --- | --- | --- |
| Configuración predeterminada | `MAIL_MAILER=log` en `impulso/.env.example` registra los correos en los logs; no los entrega a una bandeja real. | No se reciben por email los avisos de pedidos ni los enlaces de verificación/confirmación. | Configurar un mailer SMTP o un proveedor transaccional en el `.env` del entorno y probar su entrega. Restringir el acceso a los logs mientras se use el mailer `log`. |
| Integración pendiente | El contenedor enlaza `SmsSender` con `LogSmsSender`, que escribe el número y el mensaje (incluido el enlace de verificación firmado) en el log en lugar de enviar un SMS. | La pantalla puede informar que se envió la verificación telefónica, aunque no llega un mensaje al dispositivo. Los logs contienen datos personales y un enlace temporal de verificación. | Integrar un proveedor SMS y cambiar el binding en `impulso/app/Providers/AppServiceProvider.php`. No usar el adaptador de log como proveedor de producción ni exponer sus logs. |
| Persistencia local | `DB_CONNECTION=sqlite` es el valor de `.env.example`; el archivo SQLite local no sincroniza cambios en tiempo real entre equipos o instancias. | `git pull` no es un mecanismo para compartir transacciones ni mantener una base de datos de producción. | Para datos compartidos, configurar PostgreSQL/Supabase y seguir [la guía de datos compartidos](impulso/docs/shared-data-setup.md). Mantener los secretos en variables de entorno, fuera de Git. |
| Archivos en producción | El disco público local escribe en `impulso/public/uploads`. En un entorno serverless como Vercel, el filesystem de la función no debe considerarse almacenamiento persistente. | Las cargas realizadas durante la ejecución pueden no estar disponibles después de otra instancia o despliegue. | Configurar almacenamiento S3 compatible, por ejemplo Supabase Storage, mediante `PUBLIC_FILESYSTEM_DRIVER=s3` y las variables descritas en la guía de datos compartidos. |
| +Impulso | La página de membresía es una vista previa. El propio flujo indica que todavía no procesa pagos ni cambia el estado de la membresía. | Las opciones de pago mostradas no generan cargos ni activan una suscripción. | No usar la pantalla como pasarela real; integrar y probar un proveedor de pagos antes de habilitar cobros. |
| Importación SQLite a PostgreSQL | `app:import-local-data --confirm` se detiene si el destino PostgreSQL no está vacío o si faltan tablas requeridas. | La importación no continúa sobre una base que podría tener datos previos. | Es una protección intencional. Verificar el proyecto y el destino, hacer respaldos y seguir el procedimiento de primera importación de la guía; no borrar datos compartidos para forzarla. |

## Defectos confirmados

No se encontraron defectos reproducibles registrados como incidencias abiertas en el tracker del proyecto al momento de esta revisión. Las filas anteriores describen valores por defecto, integraciones pendientes o salvaguardas conocidas; no deben interpretarse automáticamente como fallos de código.

Para reportar un defecto, incluir versión/commit, pasos para reproducir, resultado esperado y obtenido, entorno (local/Vercel, motor de base de datos) y logs pertinentes sin contraseñas, tokens ni otros secretos. Crear el reporte en [GitHub Issues](https://github.com/ifurque/impulso/issues).

## Referencias

- [Configuración de datos compartidos](impulso/docs/shared-data-setup.md)
- [Arquitectura y diagramas](docs/arquitectura.md)
- [Configuración Laravel](impulso/.env.example)
