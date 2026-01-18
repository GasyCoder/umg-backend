<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="width:100%;">
  @if(!empty($posts) && count($posts) > 0)
    @php($hero = $posts[0])
    <tr>
      <td style="padding:0;">
        <div style="margin:0 0 14px 0;">
          <span style="display:inline-block;padding:3px 10px;border-radius:999px;background:#eaf2ff;color:#137fec;font-size:12px;font-weight:700;border:1px solid rgba(19,127,236,.18);">
            À la une
          </span>
        </div>

        @if(!empty($hero['cover_image_url']))
          <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr>
              <td style="border-radius:12px;overflow:hidden;">
                <a href="{{ $hero['url'] }}" style="text-decoration:none;display:block;">
                  <img src="{{ $hero['cover_image_url'] }}"
                       alt=""
                       width="632"
                       style="display:block;width:100%;max-width:632px;height:auto;border:0;outline:none;text-decoration:none;border-radius:12px;">
                </a>
              </td>
            </tr>
          </table>
          <div style="height:14px;line-height:14px;font-size:14px;">&nbsp;</div>
        @endif

        @if(!empty($hero['category']))
          <div style="font-size:12px;font-weight:700;color:#137fec;margin:0 0 6px 0;">
            {{ $hero['category'] }}
          </div>
        @endif

        <div style="font-size:22px;line-height:1.25;font-weight:800;letter-spacing:-.2px;color:#0f172a;">
          <a href="{{ $hero['url'] }}" style="color:#0f172a;text-decoration:none;">{{ $hero['title'] }}</a>
        </div>

        @if(!empty($hero['excerpt']))
          <div style="margin-top:8px;font-size:15px;line-height:1.65;color:#475569;">
            {{ $hero['excerpt'] }}
          </div>
        @endif

        <div style="margin-top:14px;">
          <table role="presentation" cellpadding="0" cellspacing="0" border="0">
            <tr>
              <td bgcolor="#137fec" style="border-radius:10px;">
                <a href="{{ $hero['url'] }}"
                   style="display:inline-block;padding:12px 18px;font-size:14px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:10px;mso-padding-alt:12px 18px;">
                  Lire l'article
                </a>
              </td>
            </tr>
          </table>
        </div>

        <div style="height:18px;line-height:18px;font-size:18px;">&nbsp;</div>
        <div style="height:1px;background:#eef2f7;"></div>
        <div style="height:18px;line-height:18px;font-size:18px;">&nbsp;</div>
      </td>
    </tr>

    @if(count($posts) > 1)
      <tr>
        <td style="padding:0;">
          <div style="font-size:18px;font-weight:800;letter-spacing:-.2px;color:#0f172a;margin:0 0 12px 0;">
            Dernières actualités
          </div>

          <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
            @php($rest = array_slice($posts, 1))
            @foreach(array_chunk($rest, 2) as $row)
              <tr>
                @foreach($row as $item)
                  <td class="stack" width="50%" valign="top" style="padding:0 10px 16px 0;">
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border:1px solid #eef2f7;border-radius:12px;overflow:hidden;">
                      @if(!empty($item['cover_image_url']))
                        <tr>
                          <td style="padding:0;">
                            <a href="{{ $item['url'] }}" style="text-decoration:none;display:block;">
                              <img src="{{ $item['cover_image_url'] }}"
                                   alt=""
                                   width="300"
                                   style="display:block;width:100%;max-width:300px;height:auto;border:0;outline:none;text-decoration:none;">
                            </a>
                          </td>
                        </tr>
                      @endif
                      <tr>
                        <td style="padding:14px 14px 16px 14px;">
                          @if(!empty($item['category']))
                            <div style="font-size:12px;font-weight:700;color:#137fec;margin:0 0 6px 0;">
                              {{ $item['category'] }}
                            </div>
                          @endif
                          <div style="font-size:14px;line-height:1.3;font-weight:800;color:#0f172a;">
                            <a href="{{ $item['url'] }}" style="color:#0f172a;text-decoration:none;">{{ $item['title'] }}</a>
                          </div>
                          @if(!empty($item['excerpt']))
                            <div style="margin-top:8px;font-size:13px;line-height:1.6;color:#64748b;">
                              {{ $item['excerpt'] }}
                            </div>
                          @endif
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
