<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>@yield('title', 'The District Tapas + Bar')</title>
    <!--[if mso]>
    <noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
    <![endif]-->
    <style>
        body { margin:0; padding:0; background:#F7F3EC; }
        table { border-collapse:collapse; }
        a { color:#B8381F; text-decoration:underline; }
        @media screen and (max-width:620px) {
            .card { width:100% !important; }
            .px-pad { padding-left:24px !important; padding-right:24px !important; }
            .h1 { font-size:28px !important; line-height:1.15 !important; }
        }
    </style>
</head>
<body style="margin:0; padding:0; background:#F7F3EC; font-family:Georgia,'Times New Roman',serif; color:#0E0E0E;">
    <div style="display:none; overflow:hidden; line-height:1; opacity:0; max-height:0; max-width:0;">@yield('preheader')</div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F7F3EC;">
        <tr>
            <td align="center" style="padding:32px 16px;">

                <table role="presentation" class="card" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;">
                    <tr>
                        <td align="center" style="padding:8px 0 24px 0;">
                            <div style="font-family:Georgia,'Times New Roman',serif; font-style:italic; font-weight:300; font-size:28px; color:#0E0E0E; letter-spacing:-0.01em;">
                                The <span style="color:#B8860B;">District</span>
                            </div>
                            <div style="font-family:'Helvetica Neue',Arial,sans-serif; font-size:10px; letter-spacing:0.22em; text-transform:uppercase; color:#6B6B6B; margin-top:6px;">
                                Tapas + Bar · Hamilton
                            </div>
                        </td>
                    </tr>
                </table>

                <table role="presentation" class="card" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; background:#FFFFFF; border:1px solid #E9E4DA; border-radius:12px; overflow:hidden;">
                    <tr>
                        <td class="px-pad" style="padding:36px 40px 0 40px;">
                            @hasSection('eyebrow')
                                <div style="font-family:'Helvetica Neue',Arial,sans-serif; font-size:11px; letter-spacing:0.2em; text-transform:uppercase; color:#B8860B; font-weight:600; margin-bottom:12px;">@yield('eyebrow')</div>
                            @endif
                            <h1 class="h1" style="margin:0; font-family:Georgia,'Times New Roman',serif; font-weight:300; font-size:34px; line-height:1.1; color:#0E0E0E; letter-spacing:-0.015em;">
                                @yield('heading')
                            </h1>
                            @hasSection('subheading')
                                <p style="margin:14px 0 0 0; font-size:16px; line-height:1.6; color:#555;">@yield('subheading')</p>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="px-pad" style="padding:24px 40px 40px 40px; font-family:'Helvetica Neue',Arial,sans-serif; font-size:15px; line-height:1.65; color:#2A2A2A;">
                            @yield('content')
                        </td>
                    </tr>
                </table>

                <table role="presentation" class="card" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; margin-top:24px;">
                    <tr>
                        <td align="center" style="padding:8px 24px 24px; font-family:'Helvetica Neue',Arial,sans-serif; font-size:12px; line-height:1.7; color:#777;">
                            <div style="font-family:Georgia,serif; font-style:italic; color:#B8860B; font-size:14px;">The District Tapas + Bar</div>
                            <div style="margin-top:6px;">61 Barton St E, Hamilton, ON L8L 2V7 · <a href="tel:+19055222580" style="color:#777; text-decoration:none;">(905) 522-2580</a></div>
                            <div style="margin-top:10px; color:#999;">© {{ date('Y') }} The District Tapas + Bar. All rights reserved.</div>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>
</html>
