<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <title>Nouveau message de contact</title>
  </head>
  <body style="margin:0;padding:0;background-color:#f3f4f6;font-family:Arial,sans-serif;color:#0f172a;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
      <tr>
        <td align="center" style="padding:24px;">
          <table role="presentation" cellpadding="0" cellspacing="0" width="600" style="max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;">
            <tr>
              <td style="padding:24px 28px;background:#0f172a;color:#ffffff;">
                <p style="margin:0;font-size:12px;letter-spacing:0.25em;text-transform:uppercase;color:#cbd5f5;">Université de Mahajanga</p>
                <h1 style="margin:10px 0 0;font-size:20px;font-weight:700;">Nouveau message de contact</h1>
              </td>
            </tr>
            <tr>
              <td style="padding:24px 28px;">
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;">
                  <tr>
                    <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;font-size:14px;"><strong>Nom</strong></td>
                    <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;font-size:14px;">{{ $name }}</td>
                  </tr>
                  <tr>
                    <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;font-size:14px;"><strong>Email</strong></td>
                    <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;font-size:14px;">{{ $email }}</td>
                  </tr>
                  <tr>
                    <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;font-size:14px;"><strong>Sujet</strong></td>
                    <td style="padding:8px 0;border-bottom:1px solid #e2e8f0;font-size:14px;">{{ $subjectLine }}</td>
                  </tr>
                </table>
                <div style="margin-top:18px;font-size:14px;line-height:1.6;color:#1f2937;white-space:pre-line;">
                  {{ $messageBody }}
                </div>
              </td>
            </tr>
            <tr>
              <td style="padding:18px 28px;background:#f8fafc;color:#64748b;font-size:12px;">
                Message reçu depuis le formulaire public du site UMG.
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
