<div align="center">

# Impulso

**Un espacio digital para descubrir, operar y hacer crecer emprendimientos locales.**

[Instalación](#puesta-en-marcha) · [Funcionalidades](#funcionalidades) · [Arquitectura y UML](docs/arquitectura.md) · [Pruebas](#pruebas)

</div>

Impulso conecta a clientes con emprendimientos y ofrece a cada negocio un escaparate público y herramientas privadas para administrar su operación. Desde una sola plataforma se pueden publicar productos y servicios, recibir pedidos y turnos, responder consultas y registrar movimientos financieros.

## Contenido

- [Funcionalidades](#funcionalidades)
- [Tecnologías](#tecnologías)
- [Puesta en marcha](#puesta-en-marcha)
- [Comandos de desarrollo](#comandos-de-desarrollo)
- [Pruebas](#pruebas)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Documentación técnica](#documentación-técnica)

## Funcionalidades

- Exploración de emprendimientos y páginas públicas personalizables.
- Catálogo de productos y servicios, inventario y publicaciones promocionales.
- Pedidos para retiro o entrega, con métodos de pago configurados por negocio.
- Turnos con disponibilidad horaria, confirmación por correo y gestión mediante enlace seguro.
- Consultas y reseñas de clientes.
- Panel privado para gestionar pedidos, turnos, consultas, miembros, ingresos y gastos.
- Acceso por propietario, miembros con roles de negocio y superadministración.

## Tecnologías

| Área | Tecnología |
| --- | --- |
| Aplicación web | PHP 8.2+, Laravel 12 |
| Interfaz | Blade, Tailwind CSS 4, Vite 7 |
| Persistencia | Eloquent ORM y base de datos configurada en Laravel |
| Pruebas | PHPUnit 11 y Laravel Test |

## Puesta en marcha

### Requisitos

- PHP 8.2 o superior con las extensiones requeridas por Laravel.
- Composer 2.
- Node.js y npm.
- Un motor de base de datos compatible con Laravel; SQLite es útil para desarrollo local.

### Instalación

Desde la raíz del repositorio:

```bash
composer run setup
```

El script instala dependencias PHP y JavaScript, crea `.env` desde `.env.example` si falta, genera `APP_KEY`, prepara el enlace `public/storage`, ejecuta migraciones y compila los assets. Antes de usar un motor distinto al configurado en `.env.example`, define sus variables `DB_*` y crea la base de datos.

Inicia la aplicación:

```bash
php artisan serve
```

Abre <http://127.0.0.1:8000>.

Las imágenes cargadas se guardan en `storage/app/public` y se sirven mediante `public/storage`. Configura `MAIL_*` en `.env` para habilitar los correos de pedidos y confirmación de turnos.

## Comandos de desarrollo

```bash
# Aplicación, Vite, cola y visor de logs
composer run dev

# Compilar assets para producción
npm run build

# Aplicar migraciones pendientes
php artisan migrate

# Ejecutar la suite de pruebas
composer run test
```

## Pruebas

La suite reside en `tests/Feature` y `tests/Unit`. Configura las variables de entorno de prueba en `phpunit.xml`; para desarrollo local, utiliza una base de datos aislada de los datos reales.

## Estructura del proyecto

```text
app/                 Modelos, controladores y correo
database/migrations/ Evolución del esquema de datos
resources/views/     Vistas Blade
resources/css/       Estilos de la interfaz
resources/js/        JavaScript de la interfaz
routes/web.php       Rutas web y agrupación por autenticación
tests/               Pruebas de integración y unitarias
docs/                Documentación de arquitectura y UML
```

## Documentación técnica

Consulta la [documentación de arquitectura](docs/arquitectura.md) para conocer los actores y módulos del sistema, el modelo de dominio, los flujos principales y los diagramas UML en Mermaid.