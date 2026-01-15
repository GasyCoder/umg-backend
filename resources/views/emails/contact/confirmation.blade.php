<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <title>Message bien reçu</title>
  </head>
  <body style="margin:0;padding:0;background-color:#f3f4f6;font-family:Arial,sans-serif;color:#0f172a;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
      <tr>
        <td align="center" style="padding:24px;">
          <table role="presentation" cellpadding="0" cellspacing="0" width="600" style="max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;">
            <tr>
              <td style="padding:24px 28px;background:#1d4ed8;color:#ffffff;">
                <p style="margin:0;font-size:12px;letter-spacing:0.25em;text-transform:uppercase;color:#dbeafe;">Université de Mahajanga</p>
                <h1 style="margin:10px 0 0;font-size:20px;font-weight:700;">Votre message a été reçu</h1>
              </td>
            </tr>
            <tr>
              <td style="padding:24px 28px;font-size:14px;line-height:1.7;color:#1f2937;">
                <p style="margin:0 0 12px;">Bonjour {{ $name }},</p>
                <p style="margin:0 0 12px;">
                  Votre message concernant <strong>{{ $subjectLine }}</strong> a bien été reçu par l'Université de Mahajanga.
                  Notre équipe vous répondra dans les meilleurs délais.
                </p>
                <p style="margin:0;">Merci pour votre confiance.</p>
              </td>
            </tr>
            <tr>
              <td style="padding:18px 28px;background:#f8fafc;color:#64748b;font-size:12px;">
                Ceci est un message automatique. Merci de ne pas répondre directement à cet email.
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
