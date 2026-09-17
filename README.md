# Impulso

Plataforma web para gestionar emprendimientos locales con foco en ventas, reservas, entregas, panel administrativo y presencia pública digital.

## Descripción general

Impulso es una aplicación construida con Laravel 12 que permite a los dueños de emprendimientos administrar:

- catálogo de productos y stock,
- reservas o turnos,
- pedidos con retiro o entrega a domicilio,
- consultas desde el perfil público,
- publicaciones y contenido comercial,
- finanzas básicas (gastos, ingresos y movimiento de caja),
- membresía de equipo y personalización visual.

La solución combina un sitio público para clientes con un panel privado para el equipo del negocio.

## Objetivos del sistema

- Centralizar la gestión operativa de un emprendimiento en una sola herramienta.
- Permitir a los clientes explorar negocios, consultar disponibilidad, reservar y comprar.
- Brindar visibilidad digital personalizable a cada negocio.
- Reducir la carga administrativa con procesos como control de stock, pedidos y turnos.

## Stack tecnológico

- PHP 8.2
- Laravel 12
- SQLite por defecto (configuración inicial en .env)
- Blade + Vite
- Tailwind CSS
- Eloquent ORM
- PHPUnit para pruebas funcionales

## Arquitectura funcional

- Frontend: vistas Blade con estilos y assets Vite.
- API de negocio: controladores HTTP para reservas, pedidos, productos, finanzas y paneles.
- Modelo de dominio: Business, User, Product, Appointment, Order, Inquiry, Post, Review, etc.
- Persistencia: base de datos relacional con migraciones y Eloquent.

## Estructura principal

```text
impulso/
├── app/
│   ├── Http/Controllers/
│   ├── Mail/
│   └── Models/
├── config/
├── database/
├── docs/
│   ├── arquitectura.md
│   ├── funcionalidades.md
│   ├── instalacion.md
│   ├── seguridad.md
│   ├── pruebas.md
│   └── uml/
├── public/
├── resources/
├── routes/
├── tests/
├── .env.example
├── composer.json
├── package.json
├── phpunit.xml
├── vite.config.js
└── README.md
```

## Documentación

- [Resumen ejecutivo](docs/resumen-ejecutivo.md)
- [Plan de trabajo](docs/plan-de-trabajo.md)
- [Documentación completa](docs/documentacion-completa.md)
- [Documento final](docs/documento-final.md)
- [Arquitectura](docs/arquitectura.md)
- [Funcionalidades](docs/funcionalidades.md)
- [Instalación](docs/instalacion.md)
- [Seguridad](docs/seguridad.md)
- [Pruebas](docs/pruebas.md)
- [Casos de uso UML](docs/uml/casos-de-uso.md)
- [Diagrama de clases UML](docs/uml/diagrama-clases.md)
- [Diagrama de secuencia UML](docs/uml/diagrama-secuencia.md)

## Casos de uso principales

- Cliente navega emprendimientos y revisa contenido público.
- Cliente reserva un turno y confirma por correo cuando corresponde.
- Cliente realiza un pedido con retiro o entrega.
- Dueño gestiona el panel del negocio y actualiza estado de órdenes y consultas.
- Dueño controla inventario, gastos, ingresos y publicaciones.

## Estado del proyecto

La aplicación implementa la base de un sistema de gestión para negocios locales con flujo de reservas, ventas, equipo y personalización visual. Su estructura está preparada para crecer con más módulos de reportes, integración de pagos y automatizaciones.

## Licencia

Proyecto orientado a uso interno y desarrollo local. Se recomienda revisar la licencia final antes de despliegue productivo o difusión pública.
