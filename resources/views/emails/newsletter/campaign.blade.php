<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $campaign->subject }}</title>
  <meta name="x-apple-disable-message-reformatting">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    /* Some email clients strip external fonts; keep safe fallbacks. */
    .font { font-family: Inter, "Noto Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, Helvetica, sans-serif; }

    /* Content safety (avoid overflow) */
    .content, .content * { box-sizing: border-box; }
    .content { overflow-wrap: anywhere; word-break: break-word; }
    .content img { max-width: 100% !important; height: auto !important; }
    .content table { width: 100% !important; max-width: 100% !important; }
    .content pre { white-space: pre-wrap !important; word-break: break-word !important; max-width: 100% !important; overflow: auto !important; }
    .content a { color: #0606e0; text-decoration: none; word-break: break-word; }
    .ql-align-center { text-align: center; }
    .ql-align-right { text-align: right; }
    .ql-align-justify { text-align: justify; }

    @media screen and (max-width: 620px) {
      .container { width: 100% !important; }
      .px { padding-left: 16px !important; padding-right: 16px !important; }
      .brand-title { font-size: 18px !important; }
      .hero-title { font-size: 22px !important; line-height: 1.25 !important; }
      .btn-td { width: 100% !important; }
      .btn { display: block !important; width: 100% !important; text-align: center !important; }
      .stack { display: block !important; width: 100% !important; }
    }
  </style>
</head>
<body class="font" style="margin:0;padding:0;background:#f5f5f8;color:#111827;">
  {{-- Preheader (hidden) --}}
  <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">
    {{ !empty($postSnippet) ? $postSnippet : $campaign->subject }}
  </div>

  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#f5f5f8;">
    <tr>
      <td align="center" style="padding:32px 12px;">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="680" class="container" style="width:680px;max-width:680px;">
          <tr>
            <td style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #eef2f7;box-shadow:0 20px 40px rgba(17,24,39,.08);">
              {{-- Utility bar (inside card) --}}
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                  <td class="px" style="padding:10px 24px;background:#f9fafb;border-bottom:1px solid #eef2f7;font-size:12px;color:#6b7280;">
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                      <tr>
                        <td class="stack" style="font-size:12px;color:#6b7280;">
                          {{ $campaign->created_at?->format('d/m/Y') ?? now()->format('d/m/Y') }}
                        </td>
                        <td class="stack" align="right" style="font-size:12px;color:#6b7280;">
                          @if(!empty($readMoreUrl))
                            <a href="{{ $readMoreUrl }}" style="color:#0606e0;text-decoration:none;font-weight:600;">Voir en ligne</a>
                          @endif
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              {{-- Brand header --}}
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                  <td class="px" align="center" style="padding:28px 24px;border-bottom:1px solid #eef2f7;">
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        @if(!empty($logoUrl))
                          <td style="padding-right:12px;">
                            <a href="{{ $frontendBase }}" style="text-decoration:none;display:inline-block;">
                              <img src="{{ $logoUrl }}" alt="Université de Mahajanga" height="32" style="display:block;height:32px;width:auto;border:0;outline:none;text-decoration:none;">
                            </a>
                          </td>
                        @endif
                        <td style="text-align:left;">
                          <div class="brand-title" style="font-size:20px;line-height:1.2;font-weight:800;letter-spacing:-.2px;color:#0f172a;">
                            <a href="{{ $frontendBase }}" style="color:#0f172a;text-decoration:none;">Université de Mahajanga</a>
                          </div>
                          <div style="margin-top:4px;font-size:13px;color:#6b7280;">
                            Newsletter &amp; actualités
                          </div>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              {{-- Main content --}}
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                  <td class="px" style="padding:24px 24px 12px 24px;">
                    @if(!empty($coverImageUrl))
                      <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                        <tr>
                          <td style="border-radius:12px;overflow:hidden;">
                            <img src="{{ $coverImageUrl }}"
                                 alt="{{ $coverImageAlt ?: 'Image' }}"
                                 width="632"
                                 style="display:block;width:100%;max-width:632px;height:auto;border:0;outline:none;text-decoration:none;border-radius:12px;">
                          </td>
                        </tr>
                      </table>
                      <div style="height:18px;line-height:18px;font-size:18px;">&nbsp;</div>
                    @endif

                    <div style="margin:0 0 12px 0;">
                      <span style="display:inline-block;padding:3px 10px;border-radius:999px;background:#e8e8ff;color:#0606e0;font-size:12px;font-weight:700;border:1px solid rgba(6,6,224,.18);">
                        {{ !empty($readMoreUrl) ? 'À la une' : (!empty($campaign->post_id) ? 'Newsletter' : 'Annonce institutionnelle') }}
                      </span>
                    </div>

                    <div class="hero-title" style="font-size:28px;line-height:1.2;font-weight:800;letter-spacing:-.3px;color:#0f172a;">
                      @if(!empty($readMoreUrl))
                        <a href="{{ $readMoreUrl }}" style="color:#0f172a;text-decoration:none;">{{ $campaign->subject }}</a>
                      @else
                        {{ $campaign->subject }}
                      @endif
                    </div>

                    @if(!empty($readMoreUrl) && !empty($postSnippet))
                      <div style="margin-top:10px;font-size:16px;line-height:1.65;color:#475569;">
                        {{ $postSnippet }}
                      </div>
                    @endif

                    @if(empty($readMoreUrl))
                      @if(!empty($postExcerpt))
                        <div style="margin-top:10px;font-size:16px;line-height:1.65;color:#475569;">
                          {{ $postExcerpt }}
                        </div>
                      @endif
                    @endif
                  </td>
                </tr>
              </table>

              @if(!empty($readMoreUrl))
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                  <tr>
                    <td class="px" style="padding:0 24px 22px 24px;">
                      <div style="height:1px;background:#eef2f7;"></div>
                      <div style="height:18px;line-height:18px;font-size:18px;">&nbsp;</div>

                      <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                          <td class="btn-td" bgcolor="#0606e0" style="border-radius:10px;">
                            <a href="{{ $readMoreUrl }}"
                               class="btn"
                               style="display:inline-block;padding:12px 18px;font-size:14px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:10px;mso-padding-alt:12px 18px;">
                              Lire l'article
                            </a>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              @else
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                  <tr>
                    <td class="px content" style="padding:0 24px 22px 24px;font-size:15px;line-height:1.75;color:#0f172a;overflow-wrap:anywhere;word-break:break-word;">
                      {!! $contentHtmlEmail !!}
                    </td>
                  </tr>
                </table>
              @endif

              {{-- Footer --}}
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                  <td class="px" style="padding:26px 24px;background:#03216d;color:#e2e8f0;">
                    <div style="font-size:10px;line-height:1.6;color:#94a3b8;">
                      Vous recevez cet email car vous êtes inscrit à notre liste de diffusion. Votre vie privée est importante pour nous.
                    </div>
                    <div style="margin-top:12px;font-size:10px;line-height:1.8;">
                      <a href="{{ $unsubscribeUrl }}" style="color:#ffffff;text-decoration:underline;font-weight:300;">Se désabonner</a>
                      <span style="color:#334155;">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                      <a href="{{ $frontendBase }}" style="color:#cbd5e1;text-decoration:none;">Visiter le site</a>
                    </div>
                    <div style="margin-top:16px;font-size:10px;color:#64748b;">
                      © {{ date('Y') }} Université de Mahajanga
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  {{-- Tracking pixel (invisible 1x1 image pour compter les ouvertures) --}}
  @if(!empty($trackingPixelUrl))
    <img src="{{ $trackingPixelUrl }}" width="1" height="1" alt="" style="display:block;width:1px;height:1px;border:0;outline:none;">
  @endif
</body>
</html>
