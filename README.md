# Membora CRM

**Membora CRM** es una plataforma web SaaS responsive para gimnasios, centros fitness y estudios deportivos pequenos o medianos. Es una aplicacion de gestion para propietarios, recepcion, comerciales y entrenadores.

El proyecto se ha migrado a una app PHP monolitica para simplificar el despliegue en Plesk y evitar procesos Node.js en produccion.

## Estado actual

```text
Aplicacion PHP en migracion funcional.
Pantallas disponibles: login, panel, leads y tareas.
Pendiente: socios, membresias, pagos, clases, reservas, check-ins y alertas.
```

Repositorio:

```text
https://github.com/Jacketring/Membora-CRM.git
```

Subdominio previsto:

```text
https://app.crm.josehurtado.dev
```

## Stack

- PHP 8.2 o superior.
- MariaDB.
- PDO.
- HTML, CSS y JavaScript de navegador.
- Sin Node.js en produccion.
- Sin `npm install`.
- Sin `npm run build`.

## Estructura

```text
membora-crm/
|-- php-app/
|   |-- config/
|   |-- public/
|   |   |-- assets/
|   |   |-- .htaccess
|   |   |-- index.php
|   |-- src/
|   |   |-- Views/
|   |   |-- Actions.php
|   |   |-- Auth.php
|   |   |-- Database.php
|   |   |-- Repositories.php
|   |   |-- Support.php
|   |   |-- View.php
|   |   |-- bootstrap.php
|   |-- .env.example
|   |-- README.md
|-- docs/
|-- README.md
|-- .gitignore
```

## Configuracion

Crear `php-app/.env` en local o en Plesk.

Opcion recomendada en Plesk, especialmente si la contrasena tiene caracteres especiales:

```env
APP_NAME="Membora CRM"
APP_ENV="production"
DB_HOST="localhost"
DB_PORT="3306"
DB_DATABASE="nombre_base_datos"
DB_USERNAME="usuario_base_datos"
DB_PASSWORD="password_base_datos"
```

Tambien se admite `DATABASE_URL`:

```env
APP_NAME="Membora CRM"
APP_ENV="production"
DATABASE_URL="mysql://usuario:password@localhost:3306/nombre_base_datos"
```

## Despliegue en Plesk

1. Clonar el repositorio desde GitHub.
2. Configurar el subdominio como hosting PHP.
3. Usar PHP 8.2 o superior.
4. Activar `pdo_mysql`.
5. Configurar la raiz del documento apuntando a:

```text
php-app/public
```

Si Plesk ha clonado el repositorio dentro de otra carpeta, la ruta debe acabar igualmente en:

```text
.../php-app/public
```

6. Crear `php-app/.env` con los datos reales de MariaDB.
7. Abrir el subdominio.

## Credenciales demo

```text
Administrador
Email: admin@nexofit.demo
Password: MemboraDemo2026!

Recepcion / Comercial
Email: recepcion@nexofit.demo
Password: MemboraDemo2026!

Entrenador
Email: entrenador@nexofit.demo
Password: MemboraDemo2026!

Superadmin
Email: superadmin@membora.demo
Password: MemboraDemo2026!
```

## Funcionalidades actuales

- Login con usuarios existentes.
- Panel de control.
- Listado y creacion de leads.
- Cambio de etapa comercial.
- Conversion de lead a socio.
- Marcado de lead como perdido.
- Eliminacion de leads.
- Listado y creacion de tareas.
- Asignacion de responsable interno.
- Cambio de estado de tareas.
- Eliminacion de tareas.

## Notas

La aplicacion PHP reutiliza la base de datos MariaDB ya existente. No es necesario compilar assets ni reiniciar una aplicacion Node.js.
