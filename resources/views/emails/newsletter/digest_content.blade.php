@php
  $primary = '#1010df';
  $bgDark = '#000061';
@endphp

<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="width:100%;">
  @if(!empty($posts) && count($posts) > 0)
    @php($hero = $posts[0])

    {{-- Digest header --}}
    <tr>
      <td style="padding:0 0 16px 0;">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        </table>
        <div style="height:12px;line-height:12px;font-size:12px;">&nbsp;</div>
        <div style="font-size:16px;line-height:1.2;font-weight:400;letter-spacing:-.2px;color:#0d0d1c;">
         Actualités et articles récents
        </div>
        <div style="margin-top:6px;font-size:14px;color:#6b7280;font-weight:600;">
          {{ $issue_label ?? '' }}@if(!empty($issue_label) && !empty($date_label)) • @endif{{ $date_label ?? '' }}
        </div>
      </td>
    </tr>

    {{-- Featured hero card (email-safe) --}}
    <tr>
      <td style="padding:0 0 18px 0;">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-radius:16px;overflow:hidden;box-shadow:0 18px 40px hsla(240, 92%, 39%, 0.12);">
          @if(!empty($hero['cover_image_url']))
            <tr>
              <td style="padding:0;">
                <a href="{{ $hero['url'] }}" style="text-decoration:none;display:block;">
                  <img src="{{ $hero['cover_image_url'] }}"
                       alt="{{ $hero['title'] }}"
                       width="632"
                       style="display:block;width:100%;max-width:632px;height:auto;border:0;outline:none;text-decoration:none;">
                </a>
              </td>
            </tr>
          @endif
          <tr>
            <td style="padding:18px 18px;background:{{ $bgDark }};">
              <div style="margin:0 0 10px 0;">
                <span style="display:inline-block;background:{{ $primary }};color:#ffffff;font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;padding:6px 10px;border-radius:8px;">
                  À la une
                </span>
              </div>
              @if(!empty($hero['category']))
                <div style="font-size:12px;font-weight:700;color:#c7c7ff;margin:0 0 8px 0;">
                  {{ $hero['category'] }}
                </div>
              @endif
              <div style="font-size:22px;line-height:1.25;font-weight:900;letter-spacing:-.2px;color:#ffffff;">
                <a href="{{ $hero['url'] }}" style="color:#ffffff;text-decoration:none;">{{ $hero['title'] }}</a>
              </div>
              @if(!empty($hero['excerpt']))
                <div style="margin-top:10px;font-size:14px;line-height:1.65;color:#d7d7e6;">
                  {{ $hero['excerpt'] }}
                </div>
              @endif
              <div style="margin-top:14px;">
                <a href="{{ $hero['url'] }}" style="color:#ffffff;text-decoration:none;font-weight:800;font-size:13px;letter-spacing:.08em;text-transform:uppercase;">
                  Lire la suite →
                </a>
              </div>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    @if(count($posts) > 1)
      <tr>
        <td style="padding:0;">
          <div style="font-size:18px;font-weight:900;letter-spacing:-.2px;color:#0d0d1c;margin:0 0 12px 0;">
            Cette semaine
          </div>

          <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
            @php($rest = array_slice($posts, 1))
            @foreach(array_chunk($rest, 2) as $row)
              <tr>
                @foreach($row as $idx => $item)
                  <td class="stack" width="50%" valign="top" style="{{ $idx === 0 ? 'padding:0 10px 16px 0;' : 'padding:0 0 16px 10px;' }}">
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#ffffff;border:1px solid #e6e6f4;border-radius:14px;overflow:hidden;box-shadow:0 12px 22px rgba(13,13,28,.06);">
                      @if(!empty($item['cover_image_url']))
                        <tr>
                          <td style="padding:0;">
                            <a href="{{ $item['url'] }}" style="text-decoration:none;display:block;">
                              <img src="{{ $item['cover_image_url'] }}"
                                   alt="{{ $item['title'] }}"
                                   width="300"
                                   style="display:block;width:100%;max-width:300px;height:auto;border:0;outline:none;text-decoration:none;">
                            </a>
                          </td>
                        </tr>
                      @endif
                      <tr>
                        <td style="padding:16px 16px 18px 16px;">
                          <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                            <tr>
                              <td style="font-size:11px;font-weight:900;letter-spacing:.12em;text-transform:uppercase;color:{{ $primary }};">
                                {{ $item['category'] ?? 'Actualité' }}
                              </td>
                              <td align="right" style="font-size:12px;color:#9ca3af;font-weight:600;">
                                —
                              </td>
                            </tr>
                          </table>

                          <div style="margin-top:10px;font-size:16px;line-height:1.3;font-weight:900;color:#0d0d1c;">
                            <a href="{{ $item['url'] }}" style="color:#0d0d1c;text-decoration:none;">{{ $item['title'] }}</a>
                          </div>

                          @if(!empty($item['excerpt']))
                            <div style="margin-top:10px;font-size:13px;line-height:1.65;color:#6b7280;">
                              {{ $item['excerpt'] }}
                            </div>
                          @endif

                          <div style="margin-top:12px;">
                            <a href="{{ $item['url'] }}" style="color:{{ $primary }};text-decoration:none;font-weight:800;">
                              Lire plus ›
                            </a>
                          </div>
                        </td>
                      </tr>
                    </table>
                  </td>
                @endforeach

                @if(count($row) === 1)
                  <td class="stack" width="50%" valign="top" style="padding:0 0 16px 10px;"></td>
                @endif
              </tr>
            @endforeach
          </table>
        </td>
      </tr>
    @endif
  @endif
</table>
