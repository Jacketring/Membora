# Plan de pruebas - Membora CRM

## 1. Objetivo

Este documento define las pruebas previstas y las pruebas manuales ya realizadas para validar el MVP de Membora CRM antes de la entrega del TFM.

El objetivo es comprobar que el proyecto:

- Se instala y ejecuta siguiendo el README.
- Permite acceder con credenciales demo.
- Cubre las funcionalidades principales del MVP.
- Mantiene separacion de datos por tenant.
- Tiene una API backend desplegada y funcional.
- Puede defenderse en slides y video.

## 2. Entorno de pruebas actual

Backend desplegado:

```text
https://crm.josehurtado.dev/api
```

Base de datos:

```text
MariaDB gestionada desde Plesk
Base de datos: membora_crm
Usuario: membora_user
```

Tenant demo:

```text
NexoFit Studio
Slug: nexofit-studio
```

Credenciales demo:

```text
Admin gimnasio
Email: admin@nexofit.demo
Password: MemboraDemo2026!

Recepcion / comercial
Email: recepcion@nexofit.demo
Password: MemboraDemo2026!

Entrenador
Email: entrenador@nexofit.demo
Password: MemboraDemo2026!
```

## 3. Pruebas de instalacion y despliegue

### PI-01 Backend desplegado en Plesk

Estado: OK.

Pasos realizados:

1. Crear subdominio `crm.josehurtado.dev`.
2. Conectar repositorio GitHub desde Plesk.
3. Configurar Node.js en Plesk.
4. Configurar raiz de aplicacion en `backend`.
5. Configurar archivo de inicio `dist/main.js`.
6. Configurar variables de entorno.
7. Ejecutar `npm install --include=dev`.
8. Ejecutar `npm run build`.
9. Reiniciar aplicacion.

Resultado:

- La aplicacion NestJS arranca correctamente.
- El endpoint de health responde.

### PI-02 Base de datos y Prisma

Estado: OK.

Pasos realizados:

1. Crear base de datos `membora_crm` en Plesk.
2. Crear usuario `membora_user`.
3. Crear archivo `.env` en backend con `DATABASE_URL`.
4. Ejecutar sincronizacion Prisma:

```bash
npm exec prisma db push -- --schema prisma/schema.prisma
```

5. Ejecutar seed:

```bash
npm run prisma:seed
```

Resultado:

- Tablas creadas correctamente en MariaDB.
- Prisma Client generado correctamente.
- Datos demo cargados correctamente.

### PI-03 Frontend

Estado: pendiente.

Motivo:

- El frontend se implementara despues de cerrar el backend y recibir el diseno visual.

## 4. Pruebas backend ejecutadas

Todas las pruebas siguientes se realizaron contra el backend desplegado en Plesk.

### PB-01 Health

Endpoint:

```http
GET /api/health
```

Estado: OK.

Resultado esperado:

- HTTP `200`.
- Respuesta con `status: "ok"`.

### PB-02 Login JWT

Endpoint:

```http
POST /api/auth/login
```

Estado: OK.

Body probado:

```json
{
  "email": "admin@nexofit.demo",
  "password": "MemboraDemo2026!"
}
```

Resultado esperado:

- HTTP `201`.
- Devuelve `accessToken`.
- Devuelve usuario con `tenantId`, `role` y `tenantName`.

Resultado obtenido:

- Login correcto.
- Token JWT generado correctamente.

### PB-03 Pipeline stages

Endpoint:

```http
GET /api/pipeline-stages
```

Estado: OK.

Resultado esperado:

- HTTP `200`.
- Devuelve etapas del pipeline de NexoFit Studio ordenadas por `order`.

Resultado obtenido:

- Se devuelven etapas como `NEW_LEAD`, `CONTACTED`, `TRIAL_SCHEDULED`, `CONVERTED` y `LOST`.

### PB-04 Leads

Endpoints:

```http
GET /api/leads
POST /api/leads
PATCH /api/leads/:id
POST /api/leads/:id/convert
```

Estado: OK.

Casos probados:

- Listar leads demo.
- Crear lead nuevo.
- Mover lead a otra etapa del pipeline.
- Convertir lead en socio.

Resultado esperado:

- Los leads se filtran por `tenantId`.
- La creacion asigna `tenantId` desde el token.
- El cambio de etapa valida que la etapa pertenezca al tenant.
- La conversion crea un `Member` y cambia el lead a `CONVERTED`.

Resultado obtenido:

- Listado correcto.
- Creacion correcta.
- Movimiento de etapa correcto.
- Conversion correcta con HTTP `201`.

### PB-05 Members

Endpoints:

```http
GET /api/members
GET /api/members/:id
```

Estado: OK.

Resultado esperado:

- HTTP `200`.
- Devuelve socios demo y socios creados por conversion.
- Incluye informacion relacionada en detalle.

Resultado obtenido:

- Listado de socios correcto.

### PB-06 Membership plans

Endpoint:

```http
GET /api/membership-plans
```

Estado: OK.

Resultado esperado:

- HTTP `200`.
- Devuelve planes del tenant.

Resultado obtenido:

- Listado correcto.

### PB-07 Subscriptions

Endpoints:

```http
GET /api/subscriptions
POST /api/subscriptions
```

Estado:

- `GET`: OK.
- `POST`: implementado, pendiente de prueba manual especifica.

Resultado esperado:

- Lista suscripciones del tenant.
- Permite asignar plan a socio validando tenant.
- Evita una segunda suscripcion activa para el mismo socio.

Resultado obtenido:

- Listado correcto con HTTP `200`.

### PB-08 Payments

Endpoints:

```http
GET /api/payments
POST /api/payments
```

Estado:

- `GET`: OK.
- `POST`: implementado, pendiente de prueba manual especifica.

Resultado esperado:

- Lista pagos del tenant.
- Permite registrar pagos manuales.
- Valida socio y suscripcion dentro del mismo tenant.

Resultado obtenido:

- Listado correcto con HTTP `200`.

### PB-09 Class types

Endpoint:

```http
GET /api/class-types
```

Estado: OK.

Resultado esperado:

- HTTP `200`.
- Devuelve tipos de clase del tenant.

Resultado obtenido:

- Listado correcto.

### PB-10 Class sessions

Endpoints:

```http
GET /api/class-sessions
POST /api/class-sessions
```

Estado:

- `GET`: OK.
- `POST`: implementado, pendiente de prueba manual especifica.

Resultado esperado:

- Lista sesiones del tenant.
- Permite crear sesiones validando tipo de clase, entrenador y aforo.

Resultado obtenido:

- Listado correcto con HTTP `200`.

### PB-11 Reservations

Endpoints:

```http
GET /api/reservations
POST /api/reservations
```

Estado:

- `GET`: OK.
- `POST`: implementado, pendiente de prueba manual especifica.

Resultado esperado:

- Lista reservas del tenant.
- Permite crear reservas.
- Evita superar aforo.
- Evita reservas activas duplicadas del mismo socio en la misma sesion.

Resultado obtenido:

- Listado correcto con HTTP `200`.

### PB-12 Check-ins

Endpoints:

```http
GET /api/check-ins
POST /api/check-ins
```

Estado:

- `GET`: OK.
- `POST`: implementado, pendiente de prueba manual especifica.

Resultado esperado:

- Lista check-ins del tenant.
- Permite check-in manual o QR.
- Si se asocia a reserva, marca la reserva como `ATTENDED`.

Resultado obtenido:

- Listado correcto con HTTP `200`.

### PB-13 Tasks

Endpoints:

```http
GET /api/tasks
POST /api/tasks
PATCH /api/tasks/:id
```

Estado:

- `GET`: OK.
- `POST`: implementado, pendiente de prueba manual especifica.
- `PATCH`: implementado, pendiente de prueba manual especifica.

Resultado esperado:

- Lista tareas del tenant.
- Permite crear y completar tareas.
- Valida usuario, lead y socio dentro del tenant.

Resultado obtenido:

- Listado correcto con HTTP `200`.

### PB-14 Risk alerts

Endpoints:

```http
GET /api/risk-alerts
PATCH /api/risk-alerts/:id
```

Estado:

- `GET`: OK.
- `PATCH`: implementado, pendiente de prueba manual especifica.

Resultado esperado:

- Lista alertas del tenant.
- Permite resolver o descartar alertas.

Resultado obtenido:

- Listado correcto con HTTP `200`.

### PB-15 Dashboard

Endpoint:

```http
GET /api/dashboard
```

Estado: OK.

Resultado esperado:

- HTTP `200`.
- Devuelve KPIs principales.
- Devuelve ultimos leads, tareas pendientes y alertas abiertas.

Resultado obtenido:

- Dashboard responde correctamente con HTTP `200`.

## 5. Pruebas de seguridad

### PS-01 Rutas privadas sin token

Estado: OK.

Caso observado:

- Acceso sin token a ruta protegida devuelve:

```json
{
  "message": "Missing bearer token",
  "error": "Unauthorized",
  "statusCode": 401
}
```

Resultado:

- Las rutas privadas requieren JWT.

### PS-02 Separacion por tenant

Estado: parcialmente validado.

Validacion aplicada en codigo:

- Todas las rutas de negocio usan `tenantId` desde el token.
- Se validan relaciones dentro del mismo tenant.
- No se permite operar rutas de negocio con usuario sin `tenantId`.

Pendiente:

- Crear un segundo tenant demo para probar intentos reales de acceso cruzado.

### PS-03 Secretos

Estado: OK.

Resultado:

- `.env` no se sube al repositorio.
- `.env.example` contiene solo variables de ejemplo.
- `DATABASE_URL` real, `JWT_SECRET` y contrasenas reales se gestionan en Plesk.

## 6. Pruebas funcionales pendientes

Pendientes antes de cerrar definitivamente el backend:

- Probar `POST /subscriptions` con un socio sin suscripcion activa.
- Probar `POST /payments`.
- Probar `POST /class-sessions`.
- Probar `POST /reservations`.
- Probar `POST /check-ins`.
- Probar `POST /tasks`.
- Probar `PATCH /tasks/:id`.
- Probar `PATCH /risk-alerts/:id`.
- Probar errores esperados:
  - reserva duplicada
  - aforo completo
  - lead ya convertido
  - socio de otro tenant
  - token ausente o invalido

## 7. Pruebas frontend

Estado: pendiente.

Se ejecutaran cuando exista frontend.

Viewports recomendados:

- Desktop: 1440 x 900.
- Tablet: 768 x 1024.
- Movil: 390 x 844.

Pantallas minimas a revisar:

- Login.
- Dashboard.
- Leads.
- Pipeline.
- Socios.
- Ficha de socio.
- Calendario de clases.
- Check-in.

Resultado esperado:

- No hay solapamientos.
- Los textos caben en sus contenedores.
- Las acciones principales son accesibles.
- La experiencia es responsive.

## 8. Pruebas de entrega

Antes de entregar:

- [ ] El README contiene descripcion, stack, instalacion, ejecucion, estructura, funcionalidades y credenciales.
- [ ] El README contiene URL de GitHub.
- [ ] El README contiene URL de despliegue.
- [ ] El README contiene URL de slides.
- [ ] El README contiene URL de video.
- [ ] El video muestra captura de pantalla obligatoria.
- [ ] Las slides tienen acceso publico.
- [ ] El repositorio esta publico o el acceso privado esta justificado.
- [ ] La fecha de entrega objetivo, 20/07/2026, esta controlada.

## 9. Incidencias detectadas y resueltas

| Fecha | Area | Descripcion | Estado |
| --- | --- | --- | --- |
| 2026-06-22 | Prisma / MariaDB | `DATABASE_URL` no estaba disponible para Prisma CLI en Plesk. Se creo `.env` en backend. | Resuelta |
| 2026-06-22 | Prisma schema | `Payment.status` y `Task.status` quedaron cruzados inicialmente. Se corrigieron enums y se sincronizo la base. | Resuelta |
| 2026-06-22 | Plesk Node.js | Plesk seguia sirviendo builds anteriores hasta reiniciar la app. Se establecio proceso: pull, build, reiniciar. | Resuelta |
| 2026-06-22 | Auth | Seed inicial usaba hash no compatible con bcrypt. Se actualizo seed para usar bcryptjs. | Resuelta |

## 10. Estado general

Estado actual:

```text
Backend MVP desplegado y probado manualmente en sus rutas principales.
Frontend pendiente de diseno e implementacion.
Pruebas automaticas pendientes.
```
