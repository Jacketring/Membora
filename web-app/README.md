# Web publica de Membora CRM

Web comercial estatica para desplegar en un subdominio separado del CRM, por ejemplo:

```text
app.web.josehurtado.dev
```

## Despliegue en Plesk

Configura el subdominio como hosting estatico/PHP normal y apunta la raiz del documento a:

```text
web-app/public
```

No necesita Node.js, npm ni build.

## Demo de solo lectura

La web incluye una demo navegable del CRM en:

```text
https://app.web.josehurtado.dev/demo.html
```

Esta demo es estatica, no conecta con la base de datos y no crea ni modifica informacion. Permite revisar panel, leads, socios, membresias, clases, tareas y modales simulados antes de pedir informacion.

## Conexion con el CRM

El formulario envia leads al webhook del CRM:

```text
https://app.crm.josehurtado.dev/webhook/lead
```

Antes de publicarla, edita:

```text
web-app/public/assets/site.js
```

y cambia:

```js
const MEMBORA_LEAD_TOKEN = 'PEGA_AQUI_EL_TOKEN_DE_CAPTACION_WEB';
```

por el token real de la seccion `Captacion Web` del CRM de la empresa que debe recibir los leads.
