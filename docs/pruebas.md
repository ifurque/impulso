# Pruebas

## 1. Objetivo

Validar que el comportamiento principal del sistema funciona correctamente: reservas, revisión de negocios, pedidos, disponibilidad, administración del negocio y flujo de notificaciones.

## 2. Framework de pruebas

El proyecto usa PHPUnit y Laravel TestCase.

Comando principal:

```bash
php artisan test
```

## 3. Cobertura actual

En la suite actual se validan escenarios clave en `tests/Feature/BusinessInteractionsTest.php`, entre otros:

- invitado puede reservar un turno,
- usuario puede confirmar turno por email,
- cualquier persona puede dejar una reseña,
- propietario puede acceder al panel del negocio,
- negocio puede configurar delivery,
- cliente puede hacer pedido con entrega,
- pedido fuera del radio es rechazado,
- propietario puede cambiar estado de pedido,
- correo de notificación se envía al dueño,
- publicaciones requieren precio y persisten el valor de redes sociales.

## 4. Ejemplos de ejecución

### Ejecutar toda la suite

```bash
php artisan test
```

### Ejecutar una prueba específica

```bash
php artisan test --filter=BusinessInteractionsTest
```

### Ejecutar un caso concreto

```bash
php artisan test --filter="test_client_can_place_a_delivery_order_with_cash_payment"
```

## 5. Estrategia recomendada

- Mantener pruebas funcionales para rutas principales.
- Cubrir validaciones críticas de negocio antes de refactors.
- Añadir pruebas para nuevos módulos (reportes, pagos, exportación, auditoría).
- Usar `RefreshDatabase` para garantizar entorno limpio.

## 6. Buenas prácticas para este proyecto

- Probar flujos completos de negocio, no solo validaciones unitarias.
- Verificar que las vistas y mensajes de sesión reflejen resultados reales.
- Confirmar eventos de correo en flujos de compra y confirmación.
- Incluir pruebas para roles y permisos en panel administrativo.

## 7. Conclusión

La suite existente da una base sólida para asegurar la continuidad del proyecto en sus procesos críticos. La recomendación es seguir ampliando la cobertura conforme crezcan los módulos del sistema.
