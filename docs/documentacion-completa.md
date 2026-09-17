# Documentación completa del proyecto Impulso

## 1. Introducción

Impulso es una plataforma web desarrollada en Laravel con el objetivo de digitalizar y centralizar la gestión operativa de emprendimientos locales. La solución combina dos perspectivas complementarias: la presencia pública del negocio para clientes y la administración interna del emprendimiento para sus dueños y colaboradores.

El proyecto está orientado a negocios que necesitan vender, reservar turnos, atender consultas, promocionar sus servicios y controlar operaciones mínimas de inventario, finanzas y organización del equipo sin depender de herramientas dispersas o complejas.

## 2. Antecedentes del proyecto

En muchos emprendimientos locales la gestión diaria se realiza de forma desordenada: WhatsApp para pedidos, cuadernos para turnos, Excel para stock, redes sociales para promociones y mensajes manuales para consultas. Esto genera pérdida de tiempo, errores operativos y una experiencia poco profesional para los clientes.

Impulso nace para resolver esa fragmentación mediante una única aplicación web que ofrece:

- presencia digital profesional,
- atención directa al cliente,
- gestión comercial básica,
- control de reservas y pedidos,
- administración del negocio y del equipo.

## 3. Objetivo general

Desarrollar una solución tecnológica para la gestión integral de emprendimientos locales, capaz de fortalecer la operación comercial, mejorar la atención al cliente y profesionalizar su presencia digital.

## 4. Objetivos específicos

- Crear un perfil público para cada emprendimiento.
- Permitir la publicación de productos y servicios.
- Habilitar la gestión de turnos y reservas.
- Soportar pedidos con retiro o entrega a domicilio.
- Gestionar consultas, reseñas y publicaciones.
- Administrar finanzas básicas y movimientos de caja.
- Organizar miembros del negocio con permisos y roles.
- Brindar una base tecnológica escalable para futuras mejoras.

## 5. Alcance del sistema

### 5.1 In-scope

- alta y edición de emprendimientos,
- autenticación de usuarios,
- panel de administración,
- gestión de productos y stock,
- reservas y disponibilidad,
- pedidos con distintos estados,
- control de gastos e ingresos,
- consultas y respuestas,
- publicaciones y contenido comercial,
- personalización del perfil público,
- panel de integrantes del negocio,
- sistema de correos y confirmaciones.

### 5.2 Out-of-scope

- pagos online integrados,
- ERP completo,
- gestión multi-sucursal avanzada,
- reportes analíticos complejos,
- integraciones con marketplaces,
- sistema de facturación fiscal.

## 6. Usuarios del sistema

### 6.1 Cliente / visitante

Es quien explora emprendimientos, revisa productos, realiza consultas y toma decisiones de compra o reserva.

### 6.2 Dueño del negocio

Es el usuario principal del sistema y tiene acceso completo a la administración del emprendimiento.

### 6.3 Miembro del negocio

Puede colaborar con la gestión del negocio según los permisos asignados por el dueño.

### 6.4 Superadministrador

Cuenta con permisos amplios para supervisar y administrar ciertos aspectos del sistema.

## 7. Requisitos funcionales

### 7.1 Gestión de emprendimientos

- Crear un emprendimiento con nombre, categoría, descripción, ubicación y datos de contacto.
- Personalizar la identidad visual del negocio.
- Habilitar o deshabilitar turnos y entregas.
- Definir el radio de entrega y costos asociados.

### 7.2 Gestión de catálogo

- Registrar productos con nombre, descripción, precio, unidad, stock y categoría.
- Actualizar el catálogo y stock disponible.
- Eliminar o desactivar productos.
- Asignar números de catálogo.

### 7.3 Reservas y turnos

- Visualizar disponibilidad por fecha y horario.
- Validar que el horario no esté tomado.
- Crear reservas para clientes registrados o invitados.
- Enviar confirmación por correo.
- Reprogramar o cancelar turnos.

### 7.4 Pedidos

- Crear pedidos con entrega o retiro.
- Validar el método de pago.
- Calcular subtotal, costo de envío y total.
- Confirmar el pedido y notificar al negocio.
- Actualizar estados del pedido.

### 7.5 Consultas y atención

- Recibir consultas desde el perfil público.
- Responder desde el panel del negocio.
- Cambiar estado según la resolución del caso.

### 7.6 Publicaciones y marketing

- Crear publicaciones con título, texto, fotos y precio.
- Publicarlas o dejarlas en borrador.
- Configurar vigencia de promoción.
- Compartir contenido en redes sociales.

### 7.7 Reseñas

- Los clientes pueden dejar valoraciones.
- El negocio puede revisar y mantener reseñas visibles.
- Se muestra el promedio de calificación en la vista pública.

### 7.8 Finanzas

- Registrar gastos por categoría.
- Registrar ingresos y fuentes de ingreso.
- Seguir movimientos sencillos de caja.

### 7.9 Miembros del negocio

- Invitar o asociar colaboradores.
- Asignar roles con distintos permisos.
- Gestionar participación interna.

## 8. Requisitos no funcionales

- Seguridad: acceso autorizado por roles y permisos.
- Validación de datos: formularios y datos de negocio validados antes del procesamiento.
- Usabilidad: uso intuitivo para negocios sin experiencia técnica.
- Mantenibilidad: estructura modular por capas y modelos bien definidos.
- Escalabilidad: base preparada para crecer con nuevos módulos.
- Rendimiento: tiempos de respuesta razonables para paneles y vistas públicas.
- Compatibilidad: funcionamiento en navegadores modernos.

## 9. Arquitectura del sistema

La solución sigue una arquitectura típica de Laravel basada en la separación por capas:

- capa de presentación: vistas Blade + assets Vite,
- capa de aplicación: controladores HTTP,
- capa de dominio: modelos y lógica de negocio,
- capa de persistencia: base de datos relacional.

### 9.1 Componentes principales

- aplicación web Laravel,
- enrutador HTTP,
- controladores de negocio,
- modelos Eloquent,
- base de datos relacional,
- vistas Blade,
- sistema de correo,
- suite de pruebas PHPUnit.

## 10. Modelo de dominio

El núcleo del sistema gira alrededor de la entidad Business, que agrega productos, pedidos, servicios, consultas, publicaciones y personalización visual.

Entidades principales:

- User
- Business
- Product
- Appointment
- Order
- OrderItem
- Inquiry
- Post
- Review
- Expense
- Income
- SocialLink
- BusinessAvailabilityHours

## 11. Casos de uso principales

### Caso de uso 1: crear un emprendimiento

Un usuario autenticado crea un negocio, completa sus datos básicos y queda como propietario del mismo.

### Caso de uso 2: reservar un turno

Un cliente visita el perfil público del negocio, selecciona fecha y horario y completa la solicitud. Si el cliente es invitado, el sistema envía una confirmación por correo.

### Caso de uso 3: realizar un pedido

El cliente agrega productos, define el método de entrega y confirma la compra. El sistema valida distancia y costos, guarda el pedido y notifica al propietario.

### Caso de uso 4: gestionar el negocio

El dueño entra al panel, revisa turnos, pedidos, consultas, productos y finanzas, y actualiza el estado de cada operación.

### Caso de uso 5: publicar contenido comercial

El propietario crea publicaciones con promociones, servicios o productos y controla su visibilidad.

## 12. Flujos de negocio más importantes

### 12.1 Flujo de reserva

1. El cliente selecciona una fecha y horario.
2. El sistema valida la disponibilidad.
3. Se crea el turno con estado pendiente o en espera de confirmación.
4. El cliente recibe un correo de confirmación.
5. El negocio valida y gestiona la solicitud desde el panel.

### 12.2 Flujo de pedido

1. El cliente agrega productos.
2. Define si desea retiro o entrega.
3. El sistema calcula total y costo de envío.
4. Valida la distancia si es delivery.
5. Guarda el pedido y envía notificación al dueño.

### 12.3 Flujo de consulta

1. El cliente completa un formulario desde la vista pública.
2. El sistema registra la consulta.
3. El negocio responde desde el panel.
4. La consulta cambia de estado a respondida o cerrada.

## 13. Seguridad

La aplicación implementa medidas básicas de seguridad de Laravel y validación de negocio:

- autenticación y sesiones,
- control de acceso por negocio,
- validación de entradas con Request::validate,
- uso de tokens para confirmación y gestión de turnos,
- permisos por roles y ownership,
- limitación de acceso a rutas y paneles internos.

## 14. Pruebas

El proyecto cuenta con pruebas funcionales para los flujos principales, especialmente para:

- reservas,
- consultas, 
- reseñas,
- pedidos,
- delivery,
- estados del negocio,
- notificaciones por email,
- acceso a paneles y gestión.

La suite principal se ejecuta con PHPUnit mediante Laravel TestCase.

## 15. Despliegue y entorno

### Entorno local

- PHP 8.2
- Composer
- Node.js y npm
- SQLite por defecto
- proyecto ejecutado con Laravel artisan serve y Vite

### Entorno de producción

Se recomienda:

- servidor con PHP y Composer,
- base de datos robusta (MySQL o PostgreSQL),
- configuración de variables de entorno seguras,
- HTTPS activo,
- almacenamiento seguro para imágenes,
- sistema de backups y monitoreo.

## 16. Riesgos y mitigaciones

### Riesgos

- alcance funcional demasiado amplio,
- permisos complejos por negocio,
- crecimiento del proyecto sin planificación,
- falta de reportes avanzados,
- necesidad de pagos online y automatizaciones futuras.

### Mitigaciones

- priorizar módulos críticos,
- mantener una estructura modular,
- aumentar pruebas y cobertura,
- definir roadmap por fases,
- reforzar seguridad y auditoría a medida que crece el sistema.

## 17. Roadmap propuesto

### Fase 1: base operativa

- elaboración del negocio,
- catálogo,
- turnos,
- pedidos,
- consultas,
- panel de administración.

### Fase 2: optimización comercial

- reportes,
- métricas,
- finanzas más completas,
- automatización de mensajes,
- mejoras de UX.

### Fase 3: escalabilidad

- pagos online,
- multi-sucursal,
- integraciones externas,
- analytics avanzados,
- exportación de reportes y gestión empresarial ampliada.

## 18. Conclusión

Impulso representa una solución funcional y bien orientada a la realidad de los emprendimientos locales. Su mayor valor es la integración de la operación interna con la presencia pública del negocio, permitiendo a los comercios digitalizar actividades esenciales con una herramienta centralizada y accesible.

El proyecto tiene una base sólida para continuar creciendo, y su arquitectura y enfoque actual lo posicionan como una plataforma enfocada en negocios reales, con clara posibilidad de evolución hacia un sistema más completo, profesional y escalable.
