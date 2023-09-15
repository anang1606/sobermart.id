<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex,follow"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{!! BaseHelper::clean(page_title()->getTitle()) !!}</title>
    @if (setting('admin_favicon') || config('core.base.general.favicon'))
        <link rel="icon shortcut" href="{{ setting('admin_favicon') ? RvMedia::getImageUrl(setting('admin_favicon'), 'thumb') : url(config('core.base.general.favicon')) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Roboto+Slab:wght@500&display=swap"
        rel="stylesheet">

    <style>
        @page {
            margin: 20px 15px 10px 15px !important;
            font-size: 16px;
            line-height: 18px;
        }

        @font-face {
            font-family: sans-serif;
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: sans-serif;
            background-color: #606060;
            margin: 0;
        }

        h3 {
            font-size: 1.75rem;
        }

        h4 {
            font-size: 1.3rem;
        }

        h1,
        h2,
        h3,
        h4,
        h5 {
            margin: .5rem 0 .5rem 0;
            font-weight: 500;
            line-height: 1.2;
            font-family: sans-serif;
        }

        .topnav {
            overflow: hidden;
            background-color: #333;
            position: fixed;
            top: 0px;
            width: 100%;
        }

        .topnav a {
            float: left;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            font-size: 17px;
        }

        .nav-right {
            float: right !important;
        }

        .order-box {
            font-size: 12px;
            line-height: 15px;
            font-family: sans-serif;
            width: 216mm;
            margin: 20px auto;
            padding: 20px;
            margin-top: 60px;
            box-shadow: 0 0 0.5cm rgba(0, 0, 0, 0.5);
            min-height: 297mm;
            background-color: #fff;
        }

        @media print {
            .order-box {
                font-size: 12px;
                line-height: 15px;
                font-family: sans-serif;
                padding: 0px;
                margin-top: 0px;
                box-shadow: 0 0 0 rgba(0, 0, 0, 0);
                background-color: unset;
            }

            .topnav {
                display: none;
            }

            .addreess {
                border: none !important;
            }
        }

        .addreess {
            border: none;
            width: 57px;
            border-bottom: 1px dashed;
            outline: none;
            text-align: end;
        }

        .gr {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .gr-r {
            border-right: 1px solid black !important;
            border-collapse: collapse;
        }

        .order-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .order-box table td,
        .order-box table th {
            padding: 5px;
            vertical-align: top;
        }

        .header-surat {
            display: flex;
            justify-content: space-between;
            gap: 2.2rem;
        }

        .body-surat {
            margin-top: 14px;
        }
    </style>
</head>

<body>
    <div class="topnav">
        <a href="">SPT PPh BADAN {{ date('Y') }}</a>
        <a href="#"> </a>
        <a href="#" class="nav-right" onclick="window.print()">
            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="5px" viewBox="0 0 512 450"
                style="width:20px;" xml:space="preserve">
                <g>
                    <path style="fill:#fff"
                        d="M433.231,124.718V45.949H78.769v78.769H0v262.564h78.769v78.769h354.462v-78.769H512V124.718H433.231z M118.154,85.333 h275.692v39.385H118.154V85.333z M393.846,426.667H118.154V321.641h275.692V426.667z M472.615,347.897h-39.385v-65.641H78.769    v65.641H39.385V164.103h433.231V347.897z" />
                </g>
            </svg>
            Print
        </a>
    </div>
    <div style="text-align:center">
        <div class="order-box">
            <div class="header-surat">
                <div style="text-align: center;width: 100%;display: flex;flex-direction: column;">
                    <h4
                        style="text-transform: uppercase;font-weight: 500;font-family: 'Roboto Slab', serif;font-size: 27px;margin-bottom: 13px;">
                        {{ get_ecommerce_setting('store_company') }}
                    </h4>
                    <span
                        style="text-transform: uppercase;font-weight: 500;font-family: 'Roboto Slab',
                            serif;font-size: 18px;margin-bottom: 13px;">laporan
                        rugi laba</span>
                    <span style="font-weight: 500;font-family: 'Roboto Slab', serif;font-size: 18px;">Untuk
                        tahun berakhir {{ date('d F Y') }}</span>
                </div>
            </div>
            <div
                style="width: 100%;background: transparent;margin-top: 23px;border-top: 2px solid black;border-bottom: 2px solid black;padding: 1px 0;">
            </div>
            <div class="body-surat">
                <table style="width: 100%;">
                    <tbody>
                        <?php echo $data; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
