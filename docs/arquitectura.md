# Arquitectura del proyecto

## Visión general

Este proyecto es una aplicación web en Laravel para gestionar emprendimientos locales, reservas, pedidos, publicaciones y personalización pública. La idea principal es permitir que un emprendedor pueda crear su perfil de negocio, vender productos, aceptar turnos, gestionar consultas y mostrar una presencia digital moderna.

## Stack tecnológico

- PHP 8.2
- Laravel 12
- Blade para vistas
- MySQL / SQLite (según entorno)
- Vite para assets frontend
- Composer para dependencias PHP
- NPM para assets JS/CSS

## Patrón de arquitectura

El proyecto sigue el patrón MVC estándar de Laravel:

- Models: representan entidades del dominio (Business, Product, Appointment, Order, Inquiry, User, etc.)
- Controllers: encapsulan la lógica de negocio y la gestión de formularios
- Views: templates Blade que renderizan la interfaz en español
- Routes: definen la navegación pública y privada del sistema

## Componentes principales

### 1. Autenticación y usuarios

La autenticación se maneja con Laravel por medio de middleware y rutas protegidas por `auth` y `guest`.

Funciones principales:

- registro e ingreso de usuarios
- identificación de emprendedores
- roles dentro de emprendimientos
- miembros con permisos de administración

### 2. Emprendimientos

La entidad `Business` centraliza la información general del negocio:

- nombre, slug, categoría, descripción, ubicación
- datos de contacto
- foto de perfil y portada
- personalización visual pública
- horarios, turnos y delivery
- productos, publicaciones y reseñas

### 3. Gestión operativa

El controller `ManagementController` concentra gran parte de la lógica funcional:

- gestión de turnos
- gestión de pedidos
- consultas y respuestas
- miembros del negocio
- configuración de disponibilidad
- base de datos del negocio

### 4. Productos y movimientos

El sistema permite gestionar:

- productos y stock
- ingreso de mercadería
- gastos
- ingresos
- movimientos de negocio

La relación entre negocio y producto es clave para operar el panel del emprendimiento.

### 5. Personalización pública

El negocio expone una versión pública con:

- palette de colores
- fondo de página y patrones
- tipografías
- estilo de botones y cards
- foto de perfil ajustada
- publicaciones publicadas y reseñas visibles

### 6. Frontend público y privado

El proyecto cuenta con dos tipos de interfaces:

- pública: landing, catálogo, perfil de emprendimiento, consulta, turnos y pedidos
- privada: panel del negocio, personalización, publicaciones, miembros, gestión, base de datos

## Entidades principales

### User

Representa a cada usuario. Puede ser propietario, miembro o cliente. Tiene información de perfil y un indicador de emprendimiento.

### Business

Representa el emprendimiento con su personalización, medios de pago, productos y emisión de turnos/pedidos.

### Product

Se asocia a un negocio y representa el catálogo del emprendimiento.

### Appointment

Relaciona un cliente con un emprendimiento para reservar un turno, incluyendo fechas, horarios, confirmación y token de gestión.

### Order

Permite registrar pedidos con entrega o retiro en local.

### Inquiry

Modela consultas de clientes hacia un emprendimiento.

### Post

Representa publicaciones del negocio para difundir contenido en la parte pública.

## Flujo general

1. Un usuario crea o se asocia a un emprendimiento.
2. El negocio activa servicios como turnos, delivery o publicaciones.
3. El cliente puede explorar emprendimientos y ver su perfil público.
4. El cliente puede reservar turnos, enviar consultas o hacer pedidos.
5. El propietario gestiona la operación desde el panel administrador.
6. Las decisiones se reflejan en stock, agenda, ingresos, gastos y publicaciones.

## Seguridad

La app implementa validaciones en los controladores, control de acceso por autenticación y autorización basada en pertenencia a negocio, validación de inputs y manejo de tokens para confirmación y gestión de turnos.

## Conclusión

La arquitectura del proyecto es una implementación moderna de Laravel orientada a negocios locales con fuerte presencia digital y operativa. Su principal valor es centralizar la administración de un emprendimiento en un único panel, permitiendo al dueño operar ventas, turnos, logística y presencia pública sin depender de múltiples herramientas.
