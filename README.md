# Membora CRM

**Membora CRM** es una plataforma web SaaS responsive para gimnasios, centros fitness y estudios deportivos pequeños o medianos. El proyecto se desarrolla como Trabajo de Fin de Máster y tiene como objetivo crear un CRM vertical capaz de centralizar la captación de leads, la gestión de socios, membresías, reservas, check-ins, pagos registrados y acciones básicas de retención.

## 1. Descripción general

Membora CRM nace como respuesta a una necesidad habitual en gimnasios independientes: la gestión dispersa de leads, socios, pagos, clases y asistencia mediante hojas de cálculo, agendas manuales, WhatsApp o herramientas no especializadas.

La aplicación propone una solución más específica que un CRM generalista, pero más simple y viable que una suite fitness completa. El foco del MVP está en cubrir el ciclo principal del cliente dentro de un centro fitness:

**lead -> prueba -> alta -> socio -> membresía -> reserva -> check-in -> pago -> retención**

## 2. Objetivos del proyecto

Los objetivos principales son:

- Diseñar y desarrollar una aplicación web real y funcional.
- Construir un CRM SaaS adaptado al contexto de gimnasios y centros fitness.
- Gestionar leads, socios, membresías, clases, reservas, pagos y retención desde un único sistema.
- Aplicar una arquitectura frontend-backend-base de datos separada.
- Implementar autenticación, roles y separación de datos por gimnasio.
- Crear una interfaz responsive usable desde ordenador, tablet y teléfono.
- Preparar un proyecto documentado, ejecutable y defendible como TFM.

## 3. Funcionalidades principales del MVP

El MVP incluirá las siguientes funcionalidades:

### Autenticación y roles

- Login.
- Logout.
- Control de sesión.
- Roles básicos.
- Rutas protegidas.
- Permisos según tipo de usuario.

Roles previstos:

- Superadmin SaaS.
- Administrador del gimnasio.
- Recepción / comercial.
- Entrenador.

### Modelo SaaS multiempresa

- Gestión de varios gimnasios mediante `tenant_id`.
- Usuarios asociados a un gimnasio.
- Separación lógica de datos por tenant.
- Restricción de acceso a datos de otros centros.

### Gestión de leads

- Alta de leads.
- Edición de leads.
- Listado y búsqueda.
- Origen del lead.
- Estado comercial.
- Notas internas.
- Responsable asignado.

### Pipeline comercial

Etapas propuestas:

1. Nuevo lead.
2. Contactado.
3. Visita o prueba agendada.
4. Prueba realizada.
5. Alta propuesta.
6. Convertido a socio.
7. Perdido.

Funcionalidades:

- Visualización por etapas.
- Cambio de etapa.
- Seguimiento de oportunidades.
- Conversión de lead a socio.

### Gestión de socios

- Alta y edición de socios.
- Listado de socios.
- Ficha 360º del socio.
- Estado del socio.
- Membresía activa.
- Historial de pagos.
- Historial de reservas y asistencia.
- Tareas y alertas asociadas.

### Membresías

- Creación de planes.
- Edición de planes.
- Asignación de plan a socio.
- Estado de suscripción.
- Fecha de inicio y vencimiento.
- Consulta de membresías activas o vencidas.

### Pagos manuales

- Registro manual de pagos.
- Asociación de pago a socio.
- Asociación de pago a membresía.
- Importe.
- Fecha.
- Método de pago.
- Estado del pago.

Estados previstos:

- Pagado.
- Pendiente.
- Vencido.

### Clases y reservas

- Creación de tipos de clase.
- Creación de sesiones con fecha y hora.
- Asignación de entrenador.
- Definición de aforo máximo.
- Gestión de reservas.
- Cancelaciones.
- Control de ocupación.
- Registro de no-shows.

### Check-in

- Check-in manual desde recepción.
- Check-in mediante QR simple.
- Registro de fecha y hora de entrada.
- Asociación con socio.
- Asociación con clase si corresponde.
- Historial de asistencia.

### Tareas y alertas

- Tareas comerciales.
- Tareas de seguimiento.
- Tareas de retención.
- Alertas por pago pendiente.
- Alertas por membresía vencida.
- Alertas por inactividad.
- Alertas por tareas vencidas.

### Dashboard

KPIs previstos:

- Socios activos.
- Altas del mes.
- Bajas del mes.
- Leads abiertos.
- Conversión lead-socio.
- Conversión prueba-alta.
- MRR estimado.
- ARPU estimado.
- Pagos pendientes.
- Asistencia semanal.
- Ocupación media de clases.
- No-shows.
- Socios inactivos.
- Socios en riesgo.
- Tareas vencidas.

## 4. Funcionalidades fuera del MVP

Para mantener un alcance viable, el MVP no incluirá:

- App móvil nativa.
- Rutinas de entrenamiento.
- Nutrición.
- Wearables.
- Seguimiento deportivo avanzado.
- Pasarela de pagos real.
- Integración bancaria.
- SEPA completo.
- Facturación legal avanzada.
- Verifactu o TicketBAI completos.
- Control de acceso con hardware.
- RFID, Bluetooth o tornos.
- Inteligencia artificial predictiva real.
- POS.
- Inventario.
- Nóminas.
- Multi-sede avanzada.
- Marketplace de profesionales.

## 5. Stack tecnológico

### Frontend

- React.
- Next.js.
- TypeScript.
- Diseño responsive.
- Tailwind CSS o alternativa equivalente.

### Backend

- Node.js.
- NestJS.
- TypeScript.
- API REST.
- Validación de datos.
- Control de errores.
- Autenticación y autorización.

### Base de datos

- MariaDB.
- Prisma ORM.
- Modelo relacional.
- Separación lógica por `tenant_id`.

### Autenticación

Opción prevista:

- JWT.
- bcrypt o argon2 para cifrado de contraseñas.
- Guards o middlewares de autorización.
- Control de acceso basado en roles.

## 6. Estructura prevista del proyecto

```bash
membora-crm/
+-- backend/
|   +-- prisma/
|   +-- src/
|   |   +-- auth/
|   |   +-- users/
|   |   +-- tenants/
|   |   +-- leads/
|   |   +-- members/
|   |   +-- memberships/
|   |   +-- payments/
|   |   +-- classes/
|   |   +-- reservations/
|   |   +-- checkins/
|   |   +-- tasks/
|   |   +-- alerts/
|   |   +-- dashboard/
|   +-- .env.example
|   +-- package.json
|
+-- frontend/
|   +-- src/
|   |   +-- app/
|   |   +-- components/
|   |   +-- features/
|   |   +-- hooks/
|   |   +-- services/
|   |   +-- styles/
|   |   +-- types/
|   +-- .env.example
|   +-- package.json
|
+-- docs/
|   +-- 01-alcance-mvp.md
|   +-- 02-requisitos.md
|   +-- 03-historias-usuario.md
|   +-- 04-modelo-datos.md
|   +-- 05-pruebas.md
|
+-- README.md
+-- .gitignore
```

## 7. Instalación y ejecución

> Nota: estos pasos se ajustarán durante el desarrollo según la configuración final del repositorio.

### 7.1 Requisitos previos

Se recomienda tener instalado:

- Node.js 20 o superior.
- npm o pnpm.
- MariaDB en Plesk o una instancia MariaDB/MySQL compatible.
- Git.

### 7.2 Clonar el repositorio

```bash
git clone <URL_DEL_REPOSITORIO>
cd membora-crm
```

### 7.3 Configurar backend

```bash
cd backend
npm install
cp .env.example .env
```

Configurar las variables de entorno:

```env
DATABASE_URL="mysql://usuario:password@host-plesk:3306/membora_crm"
JWT_SECRET="cambiar_este_valor"
JWT_EXPIRES_IN="1d"
```

Ejecutar migraciones de Prisma:

```bash
npx prisma migrate dev
```

Ejecutar seed de datos demo:

```bash
npx prisma db seed
```

Arrancar backend:

```bash
npm run start:dev
```

URL prevista del backend:

```text
http://localhost:3001
```

### 7.4 Configurar frontend

En otra terminal:

```bash
cd frontend
npm install
cp .env.example .env.local
```

Configurar variables:

```env
NEXT_PUBLIC_API_URL="http://localhost:3001"
```

Arrancar frontend:

```bash
npm run dev
```

URL prevista del frontend:

```text
http://localhost:3000
```

## 8. Datos demo

Para la demostración del TFM se utilizará un gimnasio ficticio:

**NexoFit Studio**

El seed de la base de datos incluirá datos de ejemplo como:

- Usuarios internos.
- Leads en distintas fases.
- Socios activos e inactivos.
- Planes de membresía.
- Pagos pagados, pendientes y vencidos.
- Clases y reservas.
- Check-ins.
- Tareas y alertas.

## 9. Credenciales de prueba

> Las credenciales definitivas se configurarán cuando esté implementado el sistema de autenticación.

Credenciales propuestas para la demo:

```text
Administrador
Email: admin@nexofit.demo
Password: MemboraDemo2026!

Recepción / Comercial
Email: recepcion@nexofit.demo
Password: MemboraDemo2026!

Entrenador
Email: entrenador@nexofit.demo
Password: MemboraDemo2026!
```

## 10. Variables de entorno previstas

### Backend

```env
DATABASE_URL=""
JWT_SECRET=""
JWT_EXPIRES_IN=""
PORT=3001
```

### Frontend

```env
NEXT_PUBLIC_API_URL=""
```

## 11. Scripts previstos

### Backend

```bash
npm run start:dev
npm run build
npm run test
npx prisma studio
npx prisma migrate dev
npx prisma db seed
```

### Frontend

```bash
npm run dev
npm run build
npm run start
npm run lint
```

## 12. Entidades principales

Entidades previstas en el modelo de datos:

- Tenant.
- User.
- Role.
- Lead.
- PipelineStage.
- Member.
- MembershipPlan.
- Subscription.
- Payment.
- ClassType.
- ClassSession.
- Reservation.
- CheckIn.
- Task.
- CommunicationLog.
- RiskAlert.
- AuditLog.

## 13. Pantallas principales

Pantallas previstas:

- Login.
- Dashboard.
- Leads.
- Pipeline comercial.
- Ficha de lead.
- Socios.
- Ficha 360º de socio.
- Membresías.
- Pagos.
- Calendario de clases.
- Reservas.
- Check-in.
- Tareas.
- Alertas.
- Configuración.

## 14. Seguridad básica

Medidas previstas:

- Contraseñas cifradas.
- Autenticación con JWT.
- Control de acceso por roles.
- Separación de datos por `tenant_id`.
- Validación de entradas.
- Control de errores.
- Logs de acciones críticas.
- Variables de entorno para credenciales.
- Principios básicos de minimización de datos.
- Preparación para exportación o eliminación de datos personales.

## 15. Estado del proyecto

Estado actual:

```text
Fase inicial: definición de alcance, requisitos y arquitectura.
```

Próximas fases:

1. Definición de requisitos.
2. Historias de usuario.
3. Modelo de datos.
4. Diseño de pantallas.
5. Implementación del backend.
6. Implementación del frontend.
7. Integración.
8. Pruebas.
9. Despliegue.
10. Slides y vídeo final.

## 16. Despliegue

URL de despliegue:

```text
Pendiente de definir.
```

Opciones previstas:

- Frontend: Vercel.
- Backend: Render o Railway.
- Base de datos: MariaDB en Plesk o servicio compatible con MySQL/MariaDB.

## 17. Presentación

URL de slides:

```text
Pendiente de definir.
```

Las slides incluirán:

- Problema detectado.
- Objetivo del proyecto.
- Análisis de oportunidad.
- Funcionalidades del MVP.
- Arquitectura técnica.
- Demo de pantallas.
- Pruebas.
- Conclusiones.
- Líneas futuras.

## 18. Vídeo explicativo

URL del vídeo:

```text
Pendiente de definir.
```

El vídeo deberá incluir:

- Explicación general del proyecto.
- Captura de pantalla obligatoria.
- Recorrido por las funcionalidades principales.
- Explicación técnica resumida.
- Demostración con datos demo.
- Cámara opcional.

## 19. Autor

Proyecto desarrollado como Trabajo de Fin de Máster.

```text
Nombre: <NOMBRE_COMPLETO>
Email: <EMAIL_DEL_MASTER>
Portfolio: https://josehurtado.dev/
```

## 20. Licencia

Este proyecto se desarrolla con finalidad académica.

La licencia definitiva se definirá antes de publicar el repositorio.
