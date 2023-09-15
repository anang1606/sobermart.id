<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex,follow" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{!! BaseHelper::clean(page_title()->getTitle()) !!}</title>
    @if (setting('admin_favicon') || config('core.base.general.favicon'))
        <link rel="icon shortcut"
            href="{{ setting('admin_favicon') ? RvMedia::getImageUrl(setting('admin_favicon'), 'thumb') : url(config('core.base.general.favicon')) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Roboto+Slab:wght@500&display=swap"
        rel="stylesheet">

    <style>
        body {
            margin: 0;
        }

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
            padding: 17px 16px;
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
            width: 297mm;
            margin: 20px auto;
            padding: 20px 30px;
            margin-top: 60px;
            box-shadow: 0 0 0.5cm rgba(0, 0, 0, 0.5);
            height: 202mm;
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
            margin-top: 7px;
        }
    </style>
</head>
<body>
    <div class="topnav">
        <a href="#">SPT PPh BADAN {{ date('Y') }}</a>
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
    <div style="text-align:center;padding-top: 10px;">
        <div class="order-box">
            <div class="header-surat">
                <div style="text-align: center;width: 100%;display: flex;flex-direction: column;">
                    <h4
                        style="text-transform: uppercase;font-weight: 500;font-family: 'Roboto Slab', serif;font-size: 18px;margin-bottom: 8px;">
                        {{ get_ecommerce_setting('store_company') }}
                    </h4>
                    <span
                        style="text-transform: uppercase;font-weight: 500;font-family: 'Roboto Slab',
                            serif;font-size: 14px;margin-bottom: 8px;">
                        laporan posisi keuangan
                    </span>
                    <span style="font-weight: 500;font-family: 'Roboto Slab', serif;font-size: 14px;">
                        Per {{ date('d F Y') }}
                    </span>
                </div>
            </div>
            <div class="body-surat">
                <div
                    style="width: 100%;display: flex;justify-content: space-between;border-bottom: 2px solid black;padding-bottom: 2px;">
                    <span style="font-size: 14px;font-weight: 600;font-style: italic;">ASET</span>
                    <span style="font-size: 14px;font-weight: 600;font-style: italic;">LIABILITAS DAN EKUITAS</span>
                </div>
                <div style="width: 100%;display: flex;">
                    <div style="width: 50%;border-right: 2px solid black;">
                        <table style="width: 100%;padding-right: 20px;">
                            <tbody>
                                <tr>
                                    <td style="padding: 0;" width="800">
                                        <h4
                                            style="text-transform: capitalize;font-weight: 600;font-family: 'Open Sans', sans-serif;font-size: 15px;text-decoration: underline;">
                                            Aset Lancar
                                        </h4>
                                    </td>
                                    <td></td>
                                </tr>
                                @php
                                    $total_asset_lancar = 0;
                                @endphp
                                @foreach ($asset_lancar as $al)
                                    @php
                                        $total_asset_lancar += $al->amount;
                                    @endphp
                                    <tr style="line-height: 15px;">
                                        <td style="padding: 4px 0 0 20px;" width="650">
                                            <h4
                                                style="margin:0;font-size: 15px;font-weight: 500;font-family: 'Open Sans', sans-serif;text-transform: capitalize;">
                                                {{ $al->keterangan }}
                                            </h4>
                                        </td>
                                        <td style="text-align: right;padding: 4px 0 0 20px;" width="150">
                                            <h4
                                                style="margin:0;font-size: 15px;font-weight: 500;font-family: 'Open Sans', sans-serif;">
                                                {{ number_format($al->amount, 1, ',', '.') }}
                                            </h4>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr style="line-height: 15px;">
                                    <td style="padding: 4px 0 0 48px;" width="450">
                                        <h4
                                            style="margin:0;font-size: 15px;font-weight: 600;font-family: 'Open Sans', sans-serif;text-transform: capitalize;">
                                            total aset Lancar
                                        </h4>
                                    </td>
                                    <td style="text-align: right;padding: 4px 0 0 20px;border-top: 2px solid black;"
                                        width="150">
                                        <h4
                                            style="margin:0;font-size: 15px;font-weight: 600;font-family: 'Open Sans', sans-serif;">
                                            {{ number_format($total_asset_lancar, 1, ',', '.') }}
                                        </h4>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table style="width: 100%;padding-right: 20px;margin-top: 5px;">
                            <tbody>
                                <tr>
                                    <td style="padding: 0;" width="800">
                                        <h4
                                            style="text-transform: capitalize;font-weight: 600;font-family: 'Open Sans', sans-serif;font-size: 15px;text-decoration: underline;">
                                            Aset tidak Lancar
                                        </h4>
                                    </td>
                                    <td></td>
                                </tr>
                                @php
                                    $total_asset_tidak_lancar = 0;
                                @endphp
                                @foreach ($asset_tidak_lancar as $key => $al)
                                    @php
                                        $total_asset_tidak_lancar += $al->amount;
                                    @endphp
                                    <tr style="line-height: 15px;">
                                        <td style="padding: 4px 0 0 20px;" width="650">
                                            <h4
                                                style="margin:0;font-size: 15px;font-weight: 500;font-family: 'Open Sans', sans-serif;text-transform: capitalize;">
                                                {{ $al->keterangan }}
                                            </h4>
                                        </td>
                                        <td style="text-align: right;padding: 4px 0 0 20px;" width="150">
                                            <h4
                                                style="margin:0;font-size: 15px;font-weight: 500;font-family: 'Open Sans', sans-serif; {{ $key + 1 === count($asset_tidak_lancar) ? 'border-bottom: 2px solid black;' : '' }}">
                                                {{ number_format($al->amount, 1, ',', '.') }}
                                            </h4>
                                        </td>
                                        <td width="350"></td>
                                    </tr>
                                @endforeach
                                <tr style="line-height: 15px;">
                                    <td width="250"></td>
                                    <td width="250"></td>
                                    <td style="text-align: right;padding: 4px 0 0 20px;" width="150">
                                        <h4
                                            style="margin:0;font-size: 15px;font-weight: 500;font-family: 'Open Sans', sans-serif;">
                                            {{ number_format($total_asset_tidak_lancar, 1, ',', '.') }}
                                        </h4>
                                    </td>
                                </tr>
                                <tr style="line-height: 15px;">
                                    <td style="padding: 4px 0 0 20px;" width="650">
                                        <h4
                                            style="margin:0;font-size: 15px;font-weight: 500;font-family: 'Open Sans', sans-serif;text-transform: capitalize;">
                                            Akumulasi penyusutan
                                        </h4>
                                    </td>
                                    <td></td>
                                    @php
                                        $penyusutan = $total_asset_lancar - $total_asset_tidak_lancar;
                                    @endphp
                                    <td style="text-align: right;padding: 4px 0 0 20px;" width="150">
                                        <h4
                                            style="margin:0;font-size: 15px;font-weight: 500;font-family: 'Open Sans', sans-serif;border-bottom: 2px solid black;">
                                            @if ((int) $penyusutan < 0)
                                                ({{ number_format(str_replace('-', '', $penyusutan), 1, ',', '.') }})
                                            @else
                                                {{ number_format($penyusutan, 1, ',', '.') }}
                                            @endif
                                        </h4>
                                    </td>
                                </tr>
                                <tr style="line-height: 15px;">
                                    <td style="padding: 4px 0 0 48px;" width="650">
                                        <h4
                                            style="margin:0;font-size: 15px;font-weight: 600;font-family: 'Open Sans', sans-serif;text-transform: capitalize;">
                                            Total aset tidak lancar
                                        </h4>
                                    </td>
                                    <td></td>
                                    <td style="text-align: right;padding: 4px 0 0 20px;" width="150">
                                        <h4
                                            style="margin:0;font-size: 15px;font-weight: 600;font-family: 'Open Sans', sans-serif;">
                                            {{ number_format($total_asset_tidak_lancar - abs($penyusutan), 1, ',', '.') }}
                                        </h4>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table style="margin-top: 15px;padding-right: 20px;">
                            <tbody>
                                <tr style="line-height: 15px;">
                                    <td style="padding: 4px 0 0 20px;" width="650">
                                        <h4
                                            style="margin:0;font-size: 15px;font-weight: 600;font-family: 'Open Sans', sans-serif;text-transform: capitalize;">
                                            Total aset
                                        </h4>
                                    </td>
                                    <td></td>
                                    <td style="text-align: right;padding: 4px 0 0 20px;" width="150">
                                        <h4
                                            style="margin:0;font-size: 15px;font-weight: 600;font-family: 'Open Sans', sans-serif;border-bottom: 3px double black;">
                                            {{ number_format($total_asset_tidak_lancar - abs($penyusutan) + $total_asset_lancar, 1, ',', '.') }}
                                        </h4>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="width: 50%;position: relative;">
                        <table style="width: 100%;padding-right: 20px;">
                            <tbody>
                                <tr>
                                    <td style="padding: 0;" width="800">
                                        <h4
                                            style="text-transform: capitalize;font-weight: 600;font-family: 'Open Sans', sans-serif;font-size: 15px;text-decoration: underline;">
                                            Liabilitas Lancar
                                        </h4>
                                    </td>
                                    <td></td>
                                </tr>
                                @php
                                    $total_liabilitas = 0;
                                @endphp
                                @foreach ($liabilitas as $al)
                                    @php
                                        $total_liabilitas += $al->amount;
                                    @endphp
                                    <tr style="line-height: 15px;">
                                        <td style="padding: 4px 0 0 20px;" width="650">
                                            <h4
                                                style="margin:0;font-size: 15px;font-weight: 500;font-family: 'Open Sans', sans-serif;text-transform: capitalize;">
                                                {{ $al->keterangan }}
                                            </h4>
                                        </td>
                                        <td style="text-align: right;padding: 4px 0 0 20px;" width="150">
                                            <h4
                                                style="margin:0;font-size: 15px;font-weight: 500;font-family: 'Open Sans', sans-serif;">
                                                {{ number_format($al->amount, 1, ',', '.') }}
                                            </h4>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr style="line-height: 15px;">
                                    <td style="padding: 4px 0 0 48px;" width="450">
                                        <h4
                                            style="margin:0;font-size: 15px;font-weight: 600;font-family: 'Open Sans', sans-serif;text-transform: capitalize;">
                                            Total Liabilitas Lancar
                                        </h4>
                                    </td>
                                    <td style="text-align: right;padding: 4px 0 0 20px;border-top: 2px solid black;"
                                        width="150">
                                        <h4
                                            style="margin:0;font-size: 15px;font-weight: 600;font-family: 'Open Sans', sans-serif;">
                                            {{ number_format($total_liabilitas, 1, ',', '.') }}
                                        </h4>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table style="width: 100%;padding-right: 20px;margin-top: 20px;">
                            <tbody>
                                <tr>
                                    <td style="padding: 0;" width="800">
                                        <h4
                                            style="text-transform: capitalize;font-weight: 600;font-family: 'Open Sans', sans-serif;font-size: 15px;text-decoration: underline;">
                                            Ekuitas
                                        </h4>
                                    </td>
                                    <td></td>
                                </tr>
                                @php
                                    $total_ekuitas = 0;
                                @endphp
                                @foreach ($ekuitas as $al)
                                    @php
                                        $total_ekuitas += $al->amount;
                                    @endphp
                                    <tr style="line-height: 15px;">
                                        <td style="padding: 4px 0 0 20px;" width="650">
                                            <h4
                                                style="margin:0;font-size: 15px;font-weight: 500;font-family: 'Open Sans', sans-serif;text-transform: capitalize;">
                                                {{ $al->keterangan }}
                                            </h4>
                                        </td>
                                        <td style="text-align: right;padding: 4px 0 0 20px;" width="150">
                                            <h4
                                                style="margin:0;font-size: 15px;font-weight: 500;font-family: 'Open Sans', sans-serif;">
                                                {{ number_format($al->amount, 1, ',', '.') }}
                                            </h4>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr style="line-height: 15px;">
                                    <td style="padding: 4px 0 0 48px;" width="450">
                                        <h4
                                            style="margin:0;font-size: 15px;font-weight: 600;font-family: 'Open Sans', sans-serif;text-transform: capitalize;">
                                            Total ekuitas
                                        </h4>
                                    </td>
                                    <td style="text-align: right;padding: 4px 0 0 20px;border-top: 2px solid black;"
                                        width="150">
                                        <h4
                                            style="margin:0;font-size: 15px;font-weight: 600;font-family: 'Open Sans', sans-serif;">
                                            {{ number_format($total_ekuitas, 1, ',', '.') }}
                                        </h4>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table style="position: absolute;bottom: 0;padding-right: 20px;">
                            <tbody>
                                <tr style="line-height: 15px;">
                                    <td style="padding: 4px 0 0 20px;" width="650">
                                        <h4
                                            style="margin:0;font-size: 15px;font-weight: 600;font-family: 'Open Sans', sans-serif;text-transform: capitalize;">
                                            Total Liabilitas dan ekuitas
                                        </h4>
                                    </td>
                                    <td></td>
                                    <td style="text-align: right;padding: 4px 0 0 20px;" width="150">
                                        <h4
                                            style="margin:0;font-size: 15px;font-weight: 600;font-family: 'Open Sans', sans-serif;border-bottom: 3px double black;">
                                            {{ number_format($total_ekuitas + $total_liabilitas, 1, ',', '.') }}
                                        </h4>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div style="width: 100%;display: flex;justify-content: flex-end;margin-top: 40px;">
                    <div
                        style="display: flex;flex-direction: column;align-items: center;font-size: 14px;padding-right: 20px;">
                        <span style="text-transform: capitalize;">
                            {{ get_ecommerce_setting('store_city') }}, {{ date('d F Y') }}
                        </span>
                        <span style="text-transform: capitalize;margin-top: 70px;">
                            {{ Auth::user()->name }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
