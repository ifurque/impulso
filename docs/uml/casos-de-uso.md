# Casos de uso UML

## Diagrama de casos de uso

```mermaid
flowchart LR
    actorCliente[Cliente]
    actorDueno[Dueño del negocio]
    actorMiembro[Miembro del negocio]

    subgraph Sistema[Impulso]
        CU1[Explorar emprendimientos]
        CU2[Ver perfil público]
        CU3[Reservar turno]
        CU4[Confirmar turno]
        CU5[Realizar pedido]
        CU6[Dejar reseña]
        CU7[Consultar negocio]
        CU8[Gestionar panel]
        CU9[Administrar productos]
        CU10[Actualizar estados]
        CU11[Configurar publicación]
        CU12[Gestionar finanzas]
    end

    actorCliente --> CU1
    actorCliente --> CU2
    actorCliente --> CU3
    actorCliente --> CU4
    actorCliente --> CU5
    actorCliente --> CU6
    actorCliente --> CU7

    actorDueno --> CU8
    actorDueno --> CU9
    actorDueno --> CU10
    actorDueno --> CU11
    actorDueno --> CU12

    actorMiembro --> CU8
    actorMiembro --> CU9
    actorMiembro --> CU10
```

## Descripción de los casos principales

### Cliente

- Explora emprendimientos según categoría o ubicación.
- Revisa perfil público con información, fotos y publicaciones.
- Reserva turno si el negocio tiene disponibilidad.
- Confirma la reserva por correo cuando corresponde.
- Hace pedidos con retiro o envío.
- Deja reseñas y consulta al negocio.

### Dueño / administrador

- Crea y personaliza el negocio.
- Gestiona productos, precios y stock.
- Revisa turnos y pedidos pendientes.
- Actualiza estados de órdenes, consultas y reservas.
- Configura publicaciones y apariencia pública.
- Gestiona gastos, ingresos y movimiento de caja.

### Miembro del equipo

- Accede a funciones del negocio según permisos.
- Colabora con gestión operativa del emprendimiento.

## Conclusión

El sistema centraliza dos tipos de actores principales: consumidores y administradores del negocio. El diagrama refleja un modelo orientado a operaciones cotidianas y relaciones directas entre cliente y emprendimiento.
