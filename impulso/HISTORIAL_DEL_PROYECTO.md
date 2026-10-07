# Historial completo de Impulso

Este documento resume, en orden cronológico, los commits de `main` desde el primer commit hasta `2125244` (2026-10-05). Incluye cambios de código, documentación, reorganización del repositorio y guardados de la base SQLite e imágenes.

Los commits marcados como **Datos** actualizan `database/database.sqlite` y/o imágenes. Como SQLite y las imágenes son binarios, Git registra el archivo completo, no un diff legible de cada registro o pixel; la descripción se limita a los archivos efectivamente modificados y al mensaje del commit.

## Commits

1. **`3162333` · 2026-08-21 · Proyecto Impulso casi terminado**
   - Primer snapshot amplio de la aplicación Laravel: autenticación, usuarios, emprendimientos, productos, publicaciones, reseñas, turnos, gastos/ingresos, paneles, descubrimiento, migraciones, estilos y pruebas.

2. **`7f188e2` · 2026-08-26 · Implement appointment management and business customization**
   - Añadió gestión de turnos y disponibilidad, administración de publicaciones/productos y personalización del perfil público del emprendimiento, con controladores, modelos, vistas y rutas.

3. **`1dfbd93` · 2026-08-31 · Add orders and delivery management**
   - Incorporó pedidos, métodos de pago y datos de entrega; agregó el modelo y las migraciones de pedidos/campos comerciales, además de notificaciones por correo y cambios en el panel.

4. **`f676ef1` · 2026-08-31 · Improve business dashboard workflows**
   - Mejoró flujos del panel, estados de pedidos, publicaciones y operaciones; añadió campos de entrega/publicación y actualizó vistas y pruebas.

5. **`e8549bc` · 2026-08-31 · feat: mejorar formularios condicionales, bandeja e inbox de consultas**
   - Mejoró formularios condicionales y la bandeja de consultas; añadió respuestas a consultas y ajustes de disponibilidad, modelos y vistas.

6. **`a965e51` · 2026-08-31 · feat: catalogo inicial, filtros y burbuja global**
   - Añadió categorías/unidades al catálogo, filtros de productos y la burbuja global de bandeja; actualizó vistas del emprendimiento y panel.

7. **`d920c3b` · 2026-08-31 · fix: horarios editables sin turnos y mejoras mobile**
   - Corrigió la edición de horarios incluso cuando no había turnos existentes y ajustó para móvil las pantallas de disponibilidad, creación y gestión.

8. **`4705aa7` · 2026-09-01 · feat: separar base de datos y gestion operativa**
   - Separó la vista de base de datos de la gestión operativa y reorganizó rutas, controlador y accesos desde el dashboard.

9. **`e565b33` · 2026-09-01 · feat: base de datos configurable y flujo de ingreso de mercaderia**
   - Hizo configurable la información mostrada en la base de datos; agregó cantidades, marca/modelo e ingreso de mercadería con sus migraciones y cambios de gestión.

10. **`8a2adc4` · 2026-09-07 · Agregar validacion de cobertura de envios**
    - Añadió coordenadas y validación de cobertura para entregas, con cambios en emprendimientos, pedidos, disponibilidad y pruebas.

11. **`fc7d4e4` · 2026-09-07 · feat: personalizacion de usuario y emprendimiento con presets**
    - Añadió preferencias visuales de usuario, presets y colores/temas públicos del emprendimiento; incorporó almacenamiento y controles de personalización.

12. **`83784c5` · 2026-09-09 · Personalizacion del usuario/emprendimiento**
    - Amplió la personalización general y comercial, agregó elementos de pedidos y artículos, y extendió la configuración visual pública con nuevas migraciones y vistas.

13. **`a2a3ad9` · 2026-09-09 · Agregar emprendimientos y datos locales**
    - **Datos:** actualizó la base local de emprendimientos y agregó imágenes existentes de negocios y publicaciones a `storage/app/public`.

14. **`58038d7` · 2026-09-14 · Personalizacion avanzada del perfil publico**
    - Añadió opciones de borde, botones y forma de tarjetas al perfil público, con sus campos/migraciones, estilos y vista de personalización.

15. **`7c2ffb4` · 2026-09-14 · Cambios en Personalizacion y ceacion de emprendimientos**
    - Añadió fondo personalizado e imagen de patrón con tamaño configurable; actualizó creación, personalización y visualización pública.

16. **`beffd7a` · 2026-09-14 · Add profile photo customization**
    - Añadió estilo y encuadre personalizable para la foto del emprendimiento (forma, posición y zoom), con migración, controles y vista previa.

17. **`71cfcb4` · 2026-09-14 · Numerar productos por emprendimiento**
    - Incorporó numeración de catálogo por emprendimiento en el modelo, controlador, migración y pantalla de base de datos.

18. **`c4a9dfa` · 2026-09-14 · Ajustar personalizacion y fondo propio**
    - Completó capas de patrón y ajustes del fondo propio, modificando modelo, estilos, vista pública y base local.

19. **`b02b2db` · 2026-09-17 · Documentación del proyecto finalizada**
    - Añadió documentación final, diagramas y archivos de instalación/seguridad/pruebas, junto con un snapshot amplio del proyecto en la raíz del repositorio. Esa copia duplicaba temporalmente la aplicación que vivía en `impulso/`.

20. **`5fc2ed9` · 2026-09-17 · Integrar historia existente de GitHub**
    - Unió la línea de historial existente de GitHub con el snapshot documentado y consolidó la ubicación de la aplicación dentro de `impulso/`.

21. **`dab04c7` · 2026-09-23 · Asegurar persistencia de base de datos e imagenes de emprendimientos en el repositorio**
    - Ajustó reglas de `.gitignore` para que la base SQLite y las imágenes agregadas pudieran quedar versionadas.

22. **`0699442` · 2026-09-23 · Limpiar duplicados: dejar unicamente el proyecto impulso en el repositorio**
    - Quitó copias duplicadas del Laravel en la raíz y la referencia al repositorio anidado `mi-laravel`; dejó `impulso/` como aplicación fuente.

23. **`e7d5990` · 2026-09-23 · Actualizar base de datos local de emprendimientos**
    - **Datos:** guardó una nueva versión de `database/database.sqlite` con el estado local de emprendimientos en ese momento.

24. **`359a4e4` · 2026-09-23 · Agregar documentacion del proyecto**
    - Añadió documentación de arquitectura, funcionalidades, instalación y un diagrama de secuencia.

25. **`3b4c326` · 2026-09-24 · Delete docs directory**
    - Eliminó la carpeta de documentación que se había agregado en el commit anterior.

26. **`3a1fe6d` · 2026-09-24 · Storage funcionando**
    - Ajustó documentación y dependencias de Composer relacionadas con el almacenamiento de archivos.

27. **`600c419` · 2026-09-24 · Nuevo emprendimiento**
    - **Datos:** actualizó el snapshot de SQLite tras crear un emprendimiento.

28. **`30095a6` · 2026-09-24 · Unificar publicaciones y productos**
    - Ajustó la presentación pública para unificar cómo se muestran publicaciones y propuestas/productos del emprendimiento.

29. **`6a4932e` · 2026-09-27 · docs: documentar arquitectura y diagramas UML**
    - Reorganizó el README y añadió documentación de arquitectura con diagramas UML.

30. **`9cca590` · 2026-09-27 · docs: mover documentación a la raíz del repositorio**
    - Movió README y arquitectura desde `impulso/` a la raíz del repositorio y actualizó sus referencias.

31. **`91938ef` · 2026-10-02 · Versionar imagenes subidas (storage/app/public) y agregar las faltantes**
    - Ajustó el ignore del almacenamiento y agregó imágenes existentes de emprendimientos, fondos y publicaciones que faltaban en Git.

32. **`d920cc3` · 2026-10-02 · Persistencia definitiva: uploads en public/uploads (sin symlink), sesiones/cache en archivo, script guardar.ps1**
    - Movió las imágenes existentes a `public/uploads`, configuró el disco público sin enlace simbólico, marcó SQLite/imágenes como binarios y pasó sesiones/caché a archivos. Añadió `guardar.ps1` para sincronizar cambios.

33. **`dd359fb` · 2026-10-02 · Autoguardado: vigilar.ps1 y instalar-autoguardado.ps1**
    - Añadió el observador de cambios y el instalador de una tarea de Windows para sincronizar datos al iniciar sesión.

34. **`66e218a` · 2026-10-02 · Add Impulso Plus membership preview**
    - Añadió la página informativa de +Impulso, sus beneficios/estado y rutas/controlador de acceso para emprendimientos.

35. **`aaaadf7` · 2026-10-02 · Prepare shared data storage and user profile**
    - Preparó configuración para almacenamiento compartido e importación explícita de datos; añadió perfil personal, avatar y resolución común de URLs de imágenes.

36. **`c69a58d` · 2026-10-03 · Auto: guardar datos e imagenes 2026-10-03 15:37**
    - **Autoguardado:** agrupó el panel administrativo inicial para usuarios/emprendimientos, el estado +Impulso, una migración, pruebas y el snapshot de datos.

37. **`89edee0` · 2026-10-03 · Auto: guardar datos e imagenes 2026-10-03 15:49**
    - **Autoguardado:** persistió ajustes acumulados en controladores de perfiles y medios, configuración de archivos, estilos/vistas y SQLite.

38. **`455f5ea` · 2026-10-03 · Auto: guardar datos e imagenes 2026-10-03 15:50**
    - **Datos:** actualizó únicamente el snapshot de SQLite.

39. **`6f83884` · 2026-10-03 · Auto: guardar datos e imagenes 2026-10-03 15:52**
    - **Datos:** guardó SQLite y dos nuevas imágenes de emprendimientos en `public/uploads/businesses`.

40. **`7772c43` · 2026-10-03 · Auto: guardar datos e imagenes 2026-10-03 15:54**
    - **Datos:** actualizó únicamente SQLite.

41. **`fe75624` · 2026-10-04 · Auto: guardar datos e imagenes 2026-10-04 19:46**
    - **Datos:** actualizó únicamente SQLite.

42. **`b9b2be9` · 2026-10-04 · Auto: guardar datos e imagenes 2026-10-04 19:48**
    - **Datos:** actualizó SQLite y agregó una imagen de fondo en `public/uploads/businesses/backgrounds`.

43. **`5ae04e3` · 2026-10-04 · Auto: guardar datos e imagenes 2026-10-04 19:51**
    - **Datos:** actualizó únicamente SQLite.

44. **`377b198` · 2026-10-04 · Auto: guardar datos e imagenes 2026-10-04 19:52**
    - **Datos:** actualizó únicamente SQLite.

45. **`0120777` · 2026-10-04 · Auto: guardar datos e imagenes 2026-10-04 19:53**
    - **Datos:** actualizó SQLite y añadió un avatar de usuario a `public/uploads/users/avatars`.

46. **`b41b705` · 2026-10-04 · Auto: guardar datos e imagenes 2026-10-04 19:56**
    - **Datos:** actualizó únicamente SQLite.

47. **`f9adc8a` · 2026-10-05 · Add Impulso Plus payment method mockup**
    - Añadió una maqueta del método de pago en la página de membresía +Impulso y cubrió el flujo con una prueba de interacción.

48. **`2125244` · 2026-10-05 · cambios en el interfaz de la membresia del proyecto**
    - **Datos:** actualizó SQLite después de los cambios de interfaz de membresía; el commit contiene la base de datos binaria.

49. **`03e2497` · 2026-10-05 · Align owner business list with admin view**
    - Alineó el listado de emprendimientos del propietario con la presentación del panel de administración y actualizó las pruebas de navegación correspondientes.

50. **`9b9fa68` · 2026-10-05 · Add Visual Studio workspace state**
    - Añadió estado local del workspace de Visual Studio (`.vs/VSWorkspaceState.json`, `.vs/impulso/v16/.suo` y `.vs/slnx.sqlite`). No modifica la base SQLite de la aplicación ni las fotos.

## Estado al generar este informe

- Rama: `main`.
- Último commit registrado: `9b9fa68`.
- Total de commits incluidos: 50.
- El historial describe commits de Git; los cambios posteriores a este commit aparecerán al actualizar este documento.