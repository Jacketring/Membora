# Membora CRM PHP

Aplicacion PHP monolitica para ejecutar Membora CRM en un unico subdominio, sin Next.js, NestJS ni procesos Node en produccion.

## Requisitos

- PHP 8.2 o superior.
- Extension PDO MySQL activada.
- MariaDB/MySQL existente.
- Apache con `mod_rewrite` activado.

## Configuracion

Crear `php-app/.env` a partir de `.env.example`:

```env
APP_NAME="Membora CRM"
APP_ENV="production"
DB_HOST="localhost"
DB_PORT="3306"
DB_DATABASE="membora_crm"
DB_USERNAME="usuario"
DB_PASSWORD="password"
```

## Despliegue en Plesk

1. Subir el repositorio desde GitHub.
2. Configurar el subdominio para que el document root apunte a:

```text
php-app/public
```

3. Crear `php-app/.env` en el servidor con la conexion real a MariaDB.
4. Verificar que PHP usa una version 8.2 o superior.
5. Abrir el subdominio.

No hace falta ejecutar `npm install`, `npm run build`, `prisma generate` ni reiniciar una app Node para esta version PHP.

## Pantallas incluidas

- Login con usuarios existentes.
- Panel de control.
- Leads.
- Tareas.

La aplicacion reutiliza la base de datos actual. El backend y frontend Node antiguos siguen en el repositorio durante la migracion y se eliminaran cuando la version PHP quede validada.
