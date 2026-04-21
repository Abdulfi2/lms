<!DOCTYPE html>
<html>

<head>
    <title>Verifikasi Token</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <style>
        body {
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            background-color: #ffffff;
            color: #718096;
            height: 100%;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            width: 100% !important;
        }

        .wrapper {
            background-color: #edf2f7;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .content {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .header {
            padding: 25px 0;
            text-align: center;
        }

        .header a {
            color: #3d4852;
            font-size: 19px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }

        .logo {
            max-width: 100%;
            border: none;
            height: 64px;
            max-height: 64px;
            width: 64px;
        }

        .body {
            background-color: #edf2f7;
            border-bottom: 1px solid #edf2f7;
            border-top: 1px solid #edf2f7;
            margin: 0;
            padding: 0;
            width: 100%;
            border: hidden !important;
        }

        .inner-body {
            background-color: #ffffff;
            border-color: #e8e5ef;
            border-radius: 2px;
            border-width: 1px;
            box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015);
            margin: 0 auto;
            padding: 32px;
            width: 570px;
            max-width: 100%;
        }

        .content-cell {
            max-width: 100vw;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #00a63e;
            color: white !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin-top: 20px;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #718096;
            text-align: center;
        }

        /* Responsive Styles */
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
</head>

<body>
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td class="header">
                            <a href="{{ env('APP_URL') }}">
                                <img src="https://zakatsukses.org/wp-content/uploads/2022/12/logo-cycle-150x150.png"
                                    class="logo" alt="Zakat Sukses Logo">
                            </a>
                        </td>
                    </tr>

                    <!-- Email Body -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0"
                                role="presentation">
                                <tr>
                                    <td class="content-cell">
                                        <h2>Halo, {{ $name }}</h2>
                                        <p>Token Anda Berhasil Dibuat</p>
                                        <p>Terima Kasih telah berkontribusi untuk zakat sukses 🙏😁</p>
                                        <p>Terima Kasih, <br>Team IT Zakat Sukses</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0"
                                role="presentation">
                                <tr>
                                    <td class="content-cell" align="center">
                                        <p>
                                            © {{ date('Y') }} Zakat Sukses. All Rights Reserved.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
