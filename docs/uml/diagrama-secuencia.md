# Diagrama de secuencia UML

## Flujo de reserva de turno

```mermaid
sequenceDiagram
    actor Cliente as Cliente / visitante
    participant Front as Vista pública
    participant Ctrl as ManagementController
    participant Model as Business
    participant App as Appointment
    participant Mail as Email
    participant DB as Base de datos

    Cliente->>Front: Selecciona fecha y horario
    Front->>Ctrl: POST /turnos
    Ctrl->>Model: Validar negocio y disponibilidad
    Model->>Ctrl: Retorna horarios disponibles
    Ctrl->>App: Crear reserva
    App->>DB: Insertar appointment
    Ctrl->>Mail: Enviar confirmación
    Mail-->>Cliente: Correo de confirmación
    Ctrl-->>Front: Respuesta con éxito / error
    Front-->>Cliente: Mensaje del sistema
```

## Descripción del flujo

1. El cliente accede a la vista pública del negocio.
2. El sistema valida el horario solicitado y la disponibilidad del negocio.
3. Si el horario está libre, se crea un registro de Appointment.
4. Si el cliente es invitado, se genera confirmación por email.
5. El negocio puede luego gestionar la reserva desde el panel administrativo.

## Flujo alternativo para pedido con delivery

```mermaid
sequenceDiagram
    actor Cliente as Cliente
    participant Front as Formulario de pedido
    participant Ctrl as OrderController
    participant B as Business
    participant DB as Base de datos
    participant Mail as Email

    Cliente->>Front: Completa datos y productos
    Front->>Ctrl: POST /pedidos
    Ctrl->>B: Validar negocio / radio de entrega
    B-->>Ctrl: Confirmación de configuración
    Ctrl->>DB: Guardar Order y OrderItem
    Ctrl->>Mail: Notificar al dueño
    Mail-->>Cliente: Confirmación operativa
    Ctrl-->>Front: Mensaje de éxito
```

## Conclusión

Los diagramas de secuencia muestran cómo se gestionan los flujos más críticos del sistema: reservas y pedidos. Ambos caminos validan negocio, persisten datos y entregan retroalimentación al cliente o al dueño del negocio.
