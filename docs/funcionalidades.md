# Funcionalidades del proyecto

## 1. Funcionalidad principal

Impulso permite a un comercio local gestionar su presencia digital y su operación comercial desde un único sistema. El proyecto integra dos perspectivas:

- experiencia pública para clientes,
- panel interno para admins y miembros del negocio.

## 2. Usuarios y perfiles

### Cliente / visitante

Puede navegar por emprendimientos, ver información pública, consultar disponibilidades, enviar mensajes y comprar productos.

### Propietario del negocio

Puede crear emprendimientos, gestionarlos y acceder al panel principal del negocio.

### Miembro del equipo

Puede ser agregado al negocio con roles específicos y acceder a funciones administrativas autorizadas.

### Superadministrador

El sistema contempla un rol de superadmin en la lógica de permisos del negocio.

## 3. Módulos funcionales

### 3.1 Publicación del negocio

- Alta de emprendimiento con nombre, categoría, ubicación y descripción.
- Configuración de imagen de perfil y portada.
- Personalización visual pública con colores, fondos, fuentes y estilo.
- Activación o desactivación de reservas y entregas.

### 3.2 Descubrimiento de emprendimientos

- Listado de negocios públicos.
- Perfil detallado con información, fotos, comentarios y publicaciones.
- Acceso a reseñas y experiencia del cliente.

### 3.3 Gestión de productos

- Registro de productos con nombre, precio, categoría, unidad y stock.
- Asignación de número de catálogo.
- Activación de productos para venta.
- Ajuste de stock por ingreso de mercadería.

### 3.4 Gestión de turnos

- Habilitación de horarios de atención.
- Cálculo de intervalos disponibles.
- Validación de solapamiento entre reservas.
- Confirmación por email para clientes invitados.
- Reprogramación y cancelación de turnos.

### 3.5 Gestión de pedidos

- Pedido con retiro o entrega.
- Validación del radio de entrega.
- Cálculo automático de subtotal, costo de envío y total.
- Estados: pendiente, confirmado, entregado, cancelado.
- Notificación por email al propietario.

### 3.6 Consultas y atención al cliente

- Formulario de consulta pública desde el perfil del emprendimiento.
- Estado de la consulta: pendiente, respondida, cerrada.
- Respuesta gestionada desde el panel del negocio.

### 3.7 Finanzas

- Registro de gastos con categorías.
- Registro de ingresos y fuentes.
- Seguimiento de movimientos de caja.

### 3.8 Publicaciones y marketing

- Publicación de posts del negocio.
- Selección de tipo de contenido.
- Control de fechas de vigencia.
- Opción de compartir en redes sociales.

### 3.9 Reseñas

- Evaluación con puntaje.
- Comentarios visibles en la vista pública del negocio.
- Filtro de reseñas activas.

### 3.10 Colaboradores y permisos

- Agrupación de miembros por negocio.
- Roles con distintos niveles de acceso.
- Control de permisos a nivel de operación.

## 4. Casos de uso más relevantes

### Caso de uso 1: un cliente reserva un turno

1. El cliente entra al negocio público.
2. Selecciona fecha y horario disponible.
3. Envía la solicitud.
4. Si es invitado, recibe email de confirmación.
5. El negocio revisa la solicitud desde el panel.

### Caso de uso 2: un cliente compra por delivery

1. El cliente añade productos al carrito.
2. Elige envío a domicilio.
3. El sistema valida la distancia respecto al punto de entrega.
4. Se genera la orden con total calculado.
5. El dueño recibe la notificación y cambia el estado.

### Caso de uso 3: un dueño gestiona el negocio

1. Inicia sesión.
2. Ingresa a su panel.
3. Administra productos, turnos, órdenes, consultas y publicaciones.
4. Ajusta la apariencia del perfil público.

## 5. Beneficios del sistema

- Centraliza la gestión del negocio.
- Reduce fricción en ventas y atención al cliente.
- Mejora la actualización del catálogo.
- Facilita la toma de decisiones con información financiera y operativa.
- Da un canal de digitalización para comercios locales.

## 6. Limitaciones actuales identificadas

- La lógica de negocio está orientada a comercios locales, no a cadenas complejas.
- El sistema centraliza tareas operativas, pero aún requiere escalamiento para reportes avanzados.
- El enfoque actual es más transaccional y operativo que analítico.

## 7. Conclusión

La aplicación ofrece una base sólida para operar un emprendimiento digitalmente, cubriendo la mayor parte de las necesidades de gestión comercial diaria. Su mayor valor es la integración entre la presencia pública, la venta y la administración interna.
