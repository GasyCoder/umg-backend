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
      .btn-td { width: 100% !important; }
      .btn { display: block !important; width: 100% !important; text-align: center !important; }
    }
  </style>
</head>
<body style="margin:0;padding:0;background:#f3f6fb;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,Helvetica,sans-serif;color:#111827;">
  {{-- Preheader (hidden) --}}
  <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">
    {{ !empty($postSnippet) ? $postSnippet : $campaign->subject }}
  </div>

  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#f3f6fb;">
    <tr>
      <td align="center" style="padding:28px 12px;">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="680" class="container" style="width:680px;max-width:680px;">
          {{-- Utility bar --}}
          <tr>
            <td class="px" style="padding:0 24px 14px 24px;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#f9fafb;border:1px solid #e6eaf2;border-radius:12px;">
                <tr>
                  <td style="padding:10px 12px;font-size:12px;color:#6b7280;">
                    {{ $campaign->created_at?->format('d/m/Y') ?? now()->format('d/m/Y') }}
                  </td>
                  <td align="right" style="padding:10px 12px;font-size:12px;color:#6b7280;">
                    @if(!empty($readMoreUrl))
                      <a href="{{ $readMoreUrl }}" style="color:#2563eb;text-decoration:none;font-weight:600;">Voir en ligne</a>
                    @endif
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- Card --}}
          <tr>
            <td style="background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e6eaf2;box-shadow:0 10px 30px rgba(17,24,39,.06);">
              {{-- Brand header --}}
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                  <td class="px" align="center" style="padding:22px 24px 14px 24px;border-bottom:1px solid #eef2f7;">
                    @if(!empty($logoUrl))
                      <a href="{{ $frontendBase }}" style="text-decoration:none;display:inline-block;">
                        <img src="{{ $logoUrl }}" alt="Université de Mahajanga" height="36" style="display:block;height:36px;width:auto;border:0;outline:none;text-decoration:none;">
                      </a>
                    @endif
                    <div style="margin-top:10px;font-size:18px;font-weight:900;letter-spacing:.2px;color:#0b1b3a;">
                      <a href="{{ $frontendBase }}" style="color:#0b1b3a;text-decoration:none;">Université de Mahajanga</a>
                    </div>
                    <div style="margin-top:4px;font-size:13px;color:#6b7280;">
                      Newsletter &amp; actualités
                    </div>
                  </td>
                </tr>
              </table>

              {{-- Hero --}}
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                @if(!empty($coverImageUrl))
                  <tr>
                    <td>
                      <img src="{{ $coverImageUrl }}"
                           alt="{{ $coverImageAlt ?: 'Image' }}"
                           width="680"
                           style="display:block;width:100%;max-width:680px;height:auto;border:0;outline:none;text-decoration:none;">
                    </td>
                  </tr>
                @endif
                <tr>
                  <td class="px" style="padding:18px 24px 8px 24px;">
                    <div style="margin-bottom:10px;">
                      <span style="display:inline-block;padding:3px 10px;border-radius:999px;background:#eaf2ff;color:#2563eb;font-size:12px;font-weight:800;letter-spacing:.2px;">
                        {{ !empty($readMoreUrl) ? 'À la une' : 'Newsletter' }}
                      </span>
                    </div>
                    <div class="hero-title" style="font-size:24px;line-height:1.25;font-weight:900;color:#0b1b3a;">
                      @if(!empty($readMoreUrl))
                        <a href="{{ $readMoreUrl }}" style="color:#0b1b3a;text-decoration:none;display:inline-block;">
                          {{ $campaign->subject }}
                        </a>
                      @else
                        {{ $campaign->subject }}
                      @endif
                    </div>
                    @if(empty($readMoreUrl) && !empty($postExcerpt))
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
                    @if(!empty($readMoreUrl))
                      @if(!empty($postSnippet))
                        <div style="background:#f8fafc;border:1px solid #eef2f7;border-radius:14px;padding:14px 14px;margin:0 0 12px 0;">
                          <div style="font-size:13px;color:#6b7280;font-weight:700;margin-bottom:6px;">Résumé</div>
                          <div style="font-size:15px;line-height:1.7;color:#111827;">
                            {{ $postSnippet }}
                          </div>
                        </div>
                      @endif
                    @else
                      {!! $contentHtmlEmail !!}
                    @endif
                  </td>
                </tr>
              </table>

              {{-- CTA --}}
              @if(!empty($readMoreUrl))
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                  <tr>
                    <td class="px" align="center" style="padding:8px 24px 22px 24px;">
                      <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:280px;">
                        <tr>
                          <td class="btn-td" bgcolor="#2563eb" style="border-radius:12px;">
                            <a href="{{ $readMoreUrl }}"
                               class="btn"
                               style="display:inline-block;padding:12px 18px;font-size:14px;font-weight:800;color:#ffffff;text-decoration:none;border-radius:12px;mso-padding-alt:12px 18px;text-align:center;">
                              En savoir plus
                            </a>
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
                  <td class="px" style="padding:18px 24px;background:#111827;border-top:1px solid #0b1220;font-size:12px;line-height:1.7;color:#cbd5e1;">
                    <div style="color:#cbd5e1;">Vous recevez cet email car vous êtes abonné(e) à la newsletter.</div>
                    <div style="margin-top:10px;">
                      <a href="{{ $unsubscribeUrl }}" style="color:#ffffff;text-decoration:underline;font-weight:800;">Se désabonner</a>
                      <span style="color:#64748b;">&nbsp;•&nbsp;</span>
                      <a href="{{ $frontendBase }}" style="color:#cbd5e1;text-decoration:none;">Visiter le site</a>
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
