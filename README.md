<div align="center">

# Impulso

**Un espacio digital para descubrir, operar y hacer crecer emprendimientos locales.**

[Instalación](#puesta-en-marcha) · [Funcionalidades](#funcionalidades) · [Arquitectura y diagramas](docs/arquitectura.md) · [Errores conocidos](ERRORES.md) · [Pruebas](#pruebas)

</div>

Impulso conecta a clientes con emprendimientos y ofrece a cada negocio un escaparate público y herramientas privadas para administrar su operación. Desde una sola plataforma se pueden publicar productos y servicios, recibir pedidos y turnos, responder consultas y registrar movimientos financieros. El código de la aplicación está en `impulso/`; esta documentación se encuentra en la raíz del repositorio.

## Contenido

- [Funcionalidades](#funcionalidades)
- [Tecnologías](#tecnologías)
- [Puesta en marcha](#puesta-en-marcha)
- [Comandos de desarrollo](#comandos-de-desarrollo)
- [Pruebas](#pruebas)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Documentación técnica](#documentación-técnica)
- [Licencia](#licencia)

## Funcionalidades

- Exploración de emprendimientos y páginas públicas personalizables.
- Catálogo de productos y servicios, inventario y publicaciones promocionales.
- Pedidos para retiro o entrega, con métodos de pago configurados por negocio.
- Turnos con disponibilidad horaria, confirmación por correo y gestión mediante enlace seguro.
- Consultas y reseñas de clientes.
- Panel privado para gestionar pedidos, turnos, consultas, miembros, ingresos y gastos.
- Perfil personal con verificación de correo y teléfono, además de administración de usuarios y emprendimientos.
- Vista previa de +Impulso; la contratación y los pagos aún no están habilitados.
- Acceso por propietario, miembros con roles de negocio y superadministración.

## Tecnologías

| Área | Tecnología |
| --- | --- |
| Aplicación web | PHP 8.2+, Laravel 12 |
| Interfaz | Blade, Tailwind CSS 4, Vite 7 |
| Persistencia | Eloquent ORM, SQLite local por defecto o PostgreSQL compartido opcional |
| Pruebas | PHPUnit 11 y Laravel Test |

## Puesta en marcha

### Requisitos

- PHP 8.2 o superior con las extensiones requeridas por Laravel.
- Composer 2.
- Node.js y npm.
- Un motor de base de datos compatible con Laravel; SQLite es útil para desarrollo local.

### Instalación

Desde la raíz del repositorio, entra al directorio de la aplicación e instala:

```bash
cd impulso
composer run setup
```

El script instala dependencias PHP y JavaScript, crea `.env` desde `.env.example` si falta, genera `APP_KEY`, ejecuta migraciones y compila los assets. La configuración inicial usa SQLite local. Para datos compartidos entre equipos, configura PostgreSQL/Supabase según la [guía de datos compartidos](impulso/docs/shared-data-setup.md) antes de importar datos.

Inicia la aplicación:

```bash
php artisan serve
```

Abre <http://127.0.0.1:8000>.

Las imágenes se guardan en `impulso/public/uploads` con el disco local. Para producción en Vercel o almacenamiento compartido, configura Supabase Storage/S3 según la guía de datos compartidos. Configura `MAIL_*` en `impulso/.env` para enviar correos; con `MAIL_MAILER=log` los mensajes solo se escriben en los logs.

## Comandos de desarrollo

Ejecuta estos comandos desde `impulso/`:

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

La suite reside en `impulso/tests/Feature` y `impulso/tests/Unit`. La configuración de PHPUnit está en `impulso/phpunit.xml`; para desarrollo local, utiliza una base de datos aislada de los datos reales.

## Estructura del proyecto

```text
impulso/app/                 Modelos, controladores y correo
impulso/database/migrations/ Evolución del esquema de datos
impulso/resources/views/     Vistas Blade
impulso/resources/css/       Estilos de la interfaz
impulso/resources/js/        JavaScript de la interfaz
impulso/routes/web.php       Rutas web y agrupación por autenticación
impulso/tests/               Pruebas de integración y unitarias
docs/                         Documentación de arquitectura y UML
```

## Documentación técnica

| Documento | Contenido |
| --- | --- |
| [Arquitectura y diagramas](docs/arquitectura.md) | UML, DFD, EDT, modelo de dominio y diccionario de datos. |
| [Errores conocidos](ERRORES.md) | Incidencias registradas, limitaciones actuales y soluciones de configuración. |
| [Despliegue en Vercel](docs/despliegue-vercel.md) | Directorio raíz, build, variables de entorno y comprobaciones de despliegue. |
| [Seguridad y privacidad](docs/seguridad.md) | Protección de secretos, cuentas, datos personales, medios y operación. |
| [Configuración de datos compartidos](impulso/docs/shared-data-setup.md) | PostgreSQL/Supabase, medios compartidos e importación inicial. |
| [Manual de usuario](impulso/docs/manual-usuario.docx) | Guía de uso de la aplicación. |
| [Flujo de datos (documento)](impulso/docs/flujo-de-datos.docx) | Material adicional del flujo de datos. |

## Licencia

El manifiesto de Composer declara licencia MIT. El texto completo está en [LICENSE](LICENSE); la titularidad de los derechos corresponde a sus autores y contribuyentes según sus aportes.