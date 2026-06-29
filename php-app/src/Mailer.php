<?php

final class Mailer
{
    public static function sendWebLeadConfirmation(array $payload, string $leadId): bool
    {
        if (strtolower((string) (getenv('MAIL_ENABLED') ?: 'true')) === 'false') {
            return true;
        }

        $email = strtolower(trim((string) ($payload['email'] ?? '')));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $name = trim((string) (($payload['nombre'] ?? '') . ' ' . ($payload['apellidos'] ?? '')));
        if ($name === '') {
            $name = 'Hola';
        }

        $company = trim((string) ($payload['empresa'] ?? $payload['company'] ?? $payload['company_name'] ?? ''));
        $subject = 'Hemos recibido tu solicitud en Membora CRM';
        $html = self::webLeadTemplate($name, $company, $leadId);
        $fromEmail = self::fromEmail();
        $fromName = self::headerText((string) (getenv('MAIL_FROM_NAME') ?: 'Membora CRM'));
        $replyTo = trim((string) (getenv('MAIL_REPLY_TO') ?: $fromEmail));

        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $fromName . ' <' . $fromEmail . '>',
            'Reply-To: ' . $replyTo,
            'X-Mailer: Membora CRM',
        ];

        return @mail($email, self::encodedSubject($subject), $html, implode("\r\n", $headers), '-f' . $fromEmail);
    }

    private static function webLeadTemplate(string $name, string $company, string $leadId): string
    {
        $safeName = e($name);
        $safeCompany = $company !== '' ? e($company) : 'tu centro';
        $safeLeadId = e($leadId);
        $webUrl = e((string) (getenv('WEB_APP_URL') ?: 'https://app.web.josehurtado.dev'));
        $logoUrl = e(app_base_url() . '/assets/favicon.svg');

        return <<<HTML
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Solicitud recibida</title>
  </head>
  <body style="margin:0;background:#f4f7fb;font-family:Arial,Helvetica,sans-serif;color:#0b172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f7fb;padding:32px 14px;">
      <tr>
        <td align="center">
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border-radius:22px;overflow:hidden;border:1px solid #dce6f5;box-shadow:0 20px 50px rgba(15,23,42,.10);">
            <tr>
              <td style="background:#0754d6;padding:28px 32px;color:#ffffff;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                    <td>
                      <img src="{$logoUrl}" width="48" height="48" alt="Membora CRM" style="display:inline-block;width:48px;height:48px;border-radius:14px;background:#ffffff;vertical-align:middle;margin-right:12px;">
                      <span style="font-size:23px;font-weight:900;vertical-align:middle;">Membora CRM</span>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td style="padding:34px 32px 18px;">
                <p style="margin:0 0 10px;color:#0754d6;font-weight:800;text-transform:uppercase;font-size:12px;letter-spacing:.08em;">Solicitud recibida</p>
                <h1 style="margin:0 0 16px;font-size:30px;line-height:1.18;color:#071327;">Gracias, {$safeName}</h1>
                <p style="margin:0 0 18px;font-size:16px;line-height:1.65;color:#334155;">
                  Hemos recibido correctamente tu solicitud para <strong>{$safeCompany}</strong>. Una persona del equipo de Membora CRM revisara la informacion y contactara contigo en un plazo aproximado de <strong>24 a 48 horas</strong>.
                </p>
                <div style="margin:26px 0;padding:20px;border-radius:16px;background:#eef4ff;border:1px solid #cfe0ff;">
                  <p style="margin:0 0 8px;font-size:14px;color:#0754d6;font-weight:800;">Que ocurre ahora</p>
                  <ul style="margin:0;padding-left:20px;color:#1f3657;font-size:15px;line-height:1.7;">
                    <li>Revisaremos las necesidades de tu centro.</li>
                    <li>Te propondremos una demo o una llamada breve.</li>
                    <li>Resolveremos dudas sobre leads, socios, clases y membresias.</li>
                  </ul>
                </div>
                <p style="margin:0;color:#64748b;font-size:14px;line-height:1.6;">
                  Referencia interna de solicitud: <strong style="color:#0b172a;">{$safeLeadId}</strong>
                </p>
              </td>
            </tr>
            <tr>
              <td style="padding:0 32px 34px;">
                <a href="{$webUrl}" style="display:inline-block;background:#0754d6;color:#ffffff;text-decoration:none;font-weight:800;padding:14px 20px;border-radius:12px;">Volver a Membora CRM</a>
              </td>
            </tr>
            <tr>
              <td style="padding:22px 32px;background:#f8fafc;color:#64748b;font-size:13px;line-height:1.5;">
                Este correo confirma que el formulario se ha enviado correctamente. Si no has solicitado informacion sobre Membora CRM, puedes ignorarlo.
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
HTML;
    }

    private static function fromEmail(): string
    {
        $configured = trim((string) (getenv('MAIL_FROM_EMAIL') ?: ''));
        if ($configured !== '' && filter_var($configured, FILTER_VALIDATE_EMAIL)) {
            return $configured;
        }

        $host = parse_url(app_base_url(), PHP_URL_HOST) ?: 'josehurtado.dev';
        return 'no-reply@' . $host;
    }

    private static function encodedSubject(string $subject): string
    {
        return '=?UTF-8?B?' . base64_encode($subject) . '?=';
    }

    private static function headerText(string $text): string
    {
        return str_replace(["\r", "\n"], '', $text);
    }
}
