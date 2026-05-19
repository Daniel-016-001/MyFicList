@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
<img src="{{ Storage::disk('s3')->url('logo-mail.png') }}" class="logo" alt="MyFicList Logo" style="vertical-align: middle; height: 48px; width: 48px; margin: 0;">
<span style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 24px; font-weight: 900; color: #ffffff; vertical-align: middle; margin-left: 10px; letter-spacing: -0.5px;">MyFicList</span>
</a>
</td>
</tr>
