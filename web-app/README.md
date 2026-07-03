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

## Demo temporal

La web no mantiene una demo estatica separada. Los enlaces de demo envian al CRM real y abren una sesion temporal con datos de prueba:

```text
https://app.web.josehurtado.dev/demo.html
```

`demo.html` actua como puente de entrada. Envia al login demo del CRM, inicia una sesion de 20 minutos, muestra un contador dentro de la aplicacion y al finalizar cierra sesion y devuelve al usuario a la web publica.

## Conexion con el CRM

El formulario envia leads al webhook del CRM:

```text
https://app.crm.josehurtado.dev/webhook/lead
```

No hay que configurar tokens en esta web. El CRM acepta envios desde el dominio definido en `WEB_APP_URL` y crea las solicitudes en `Admin CRM > Leads`, donde el administrador puede gestionarlas o convertirlas en clientes.
El correo de confirmacion al visitante no se configura en esta web, sino en el `.env` del CRM mediante `MAIL_MAILER`, `MAIL_FROM_EMAIL`, `MAIL_FROM_NAME`, `MAIL_REPLY_TO` y los datos `SMTP_*`.

En produccion revisa que el `.env` del CRM tenga:

```env
WEB_APP_URL="https://app.web.josehurtado.dev"
```
