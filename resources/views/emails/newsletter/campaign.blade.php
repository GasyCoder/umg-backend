<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $campaign->subject }}</title>
  <meta name="x-apple-disable-message-reformatting">
  <style>
    /* Content safety (avoid overflow) */
    .content, .content * { box-sizing: border-box; }
    .content { overflow-wrap: anywhere; word-break: break-word; }
    .content img { max-width: 100% !important; height: auto !important; }
    .content table { width: 100% !important; max-width: 100% !important; }
    .content pre { white-space: pre-wrap !important; word-break: break-word !important; max-width: 100% !important; overflow: auto !important; }
    .content a { color: #2563eb; text-decoration: none; word-break: break-word; }
    .ql-align-center { text-align: center; }
    .ql-align-right { text-align: right; }
    .ql-align-justify { text-align: justify; }

    @media screen and (max-width: 620px) {
      .container { width: 100% !important; }
      .px { padding-left: 16px !important; padding-right: 16px !important; }
      .hero-title { font-size: 20px !important; line-height: 1.25 !important; }
    }
  </style>
</head>
<body style="margin:0;padding:0;background:#f3f6fb;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,Helvetica,sans-serif;color:#111827;">
  {{-- Preheader (hidden) --}}
  <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">
    {{ $campaign->subject }}
  </div>

  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#f3f6fb;">
    <tr>
      <td align="center" style="padding:28px 12px;">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" class="container" style="width:600px;max-width:600px;">
          {{-- Top bar --}}
          <tr>
            <td class="px" style="padding:0 24px 14px 24px;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                  <td style="font-size:14px;color:#0b1b3a;font-weight:800;letter-spacing:.2px;">
                    Université de Mahajanga
                  </td>
                  <td align="right" style="font-size:12px;color:#6b7280;">
                    @if(!empty($readMoreUrl))
                      <a href="{{ $readMoreUrl }}" style="color:#2563eb;text-decoration:none;">Voir en ligne</a>
                    @endif
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- Card --}}
          <tr>
            <td style="background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e6eaf2;box-shadow:0 10px 30px rgba(17,24,39,.06);">
              {{-- Hero --}}
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                @if(!empty($coverImageUrl))
                  <tr>
                    <td>
                      <img src="{{ $coverImageUrl }}"
                           alt="{{ $coverImageAlt ?: 'Image' }}"
                           width="600"
                           style="display:block;width:100%;max-width:600px;height:auto;border:0;outline:none;text-decoration:none;">
                    </td>
                  </tr>
                @endif
                <tr>
                  <td class="px" style="padding:22px 24px 8px 24px;">
                    <div class="hero-title" style="font-size:22px;line-height:1.3;font-weight:900;color:#0b1b3a;">
                      {{ $campaign->subject }}
                    </div>
                    @if(!empty($postExcerpt))
                      <div style="margin-top:10px;font-size:14px;line-height:1.6;color:#374151;">
                        {{ $postExcerpt }}
                      </div>
                    @endif
                  </td>
                </tr>
              </table>

              {{-- Divider --}}
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                  <td style="padding:0 24px;">
                    <div style="height:1px;background:#eef2f7;"></div>
                  </td>
                </tr>
              </table>

              {{-- Content --}}
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                  <td class="px content" style="padding:18px 24px 10px 24px;font-size:15px;line-height:1.75;color:#111827;overflow-wrap:anywhere;word-break:break-word;">
                    {!! $contentHtmlEmail !!}
                  </td>
                </tr>
              </table>

              {{-- CTA --}}
              @if(!empty($readMoreUrl))
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                  <tr>
                    <td class="px" style="padding:8px 24px 22px 24px;">
                      <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                          <td bgcolor="#2563eb" style="border-radius:12px;">
                            <a href="{{ $readMoreUrl }}"
                               style="display:inline-block;padding:12px 18px;font-size:14px;font-weight:800;color:#ffffff;text-decoration:none;border-radius:12px;">
                              En savoir plus
                            </a>
                          </td>
                          <td style="padding-left:12px;font-size:12px;color:#6b7280;">
                            <a href="{{ $frontendBase }}" style="color:#6b7280;text-decoration:none;">mahajanga-univ.mg</a>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              @endif

              {{-- Footer --}}
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                  <td class="px" style="padding:14px 24px;background:#fbfcfe;border-top:1px solid #eef2f7;font-size:12px;line-height:1.6;color:#6b7280;">
                    <div>Vous recevez cet email car vous êtes abonné(e) à la newsletter.</div>
                    <div style="margin-top:6px;">
                      Se désabonner :
                      <a href="{{ $unsubscribeUrl }}" style="color:#2563eb;text-decoration:none;">{{ $unsubscribeUrl }}</a>
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- Bottom --}}
          <tr>
            <td align="center" style="padding:14px 0 0 0;font-size:12px;color:#9ca3af;">
              © {{ date('Y') }} Université de Mahajanga
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
