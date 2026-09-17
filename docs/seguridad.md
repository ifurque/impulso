# Seguridad

## 1. Objetivo

Garantizar la integridad del negocio, la privacidad de la información y la protección de las sesiones de usuario dentro de la aplicación.

## 2. Autenticación

La aplicación usa el sistema nativo de autenticación de Laravel basado en sesiones y usuarios persistidos en la base de datos.

Aspectos clave:

- usuarios registrados con email y password,
- hashing de contraseña con Laravel,
- uso de middleware de autenticación para rutas privadas,
- sesión almacenada en base de datos según la configuración.

## 3. Autorización

La autorización se implementa a nivel de negocio y validación manual de permisos. En los controladores se usan comprobaciones como:

- abort_unless,
- abort_if,
- validación de ownership mediante Business::canBeManagedBy(...).

Ejemplo de lógica relevante:

- un dueño puede acceder al panel del negocio,
- un miembro con rol de administrador puede acceder si corresponde,
- un superadmin también tiene acceso,
- usuarios no autorizados reciben 403.

## 4. Validación de datos

Todos los formularios principales se validan usando `Request::validate`. Esto permite:

- evitar datos vacíos o inconsistentes,
- restringir valores inválidos para fechas, horas, precios, números y enums,
- reducir la posibilidad de inyección o manipulación de entrada.

## 5. Protección CSRF

Dado que la aplicación es de Laravel, los formularios dentro del sistema están protegidos por token CSRF de forma nativa.

## 6. Protección contra errores de lógica operativa

Se establecen validaciones de negocio para escenarios críticos:

- no permitir turnos fuera de horario,
- no aceptar productos inexistentes en un pedido,
- no permitir entrega si el negocio no la habilita,
- rechazar pedidos fuera del radio de entrega configurado,
- evitar órdenes con cantidades inválidas.

## 7. Manejo de información sensible

La configuración de entorno debe proteger variables críticas, especialmente:

- clave de la aplicación,
- credenciales de mail,
- acceso a base de datos,
- URL de producción.

## 8. Recomendaciones para producción

- Usar HTTPS obligatorio.
- Configurar un servicio de correo real y no log-only.
- Usar almacenamiento seguro para archivos de negocio.
- Limitar acceso a rutas administrativas por IP o VPN si es necesario.
- Mejorar auditoría con logs y trazabilidad de acciones.
- Agregar rate limiting para formularios públicos sensibles.

## 9. Riesgos actuales

El proyecto tiene una base sólida, pero en producción sería recomendable reforzar:

- autenticación con verificación de email,
- límites por intento de login,
- autorización más granular por permisos por módulo,
- auditoría de cambios y eliminación de registros,
- copias de seguridad automatizadas.

## 10. Conclusión

La seguridad actual se apoya en la base sólida de Laravel, validaciones explícitas y controles de propiedad por negocio. Para despliegue real, conviene reforzar la política de permisos y la auditoría operacional.
