<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Cetak Resi {{ order.shipment.shipment_id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-size: 11.2px;
        }

        .box {
            border: 2px solid black;
            padding-bottom: 10px;
        }

        .box-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }

        .box-header>img {
            width: 120px;
        }

        .box-header>h4 {
            font-size: 26px;
        }

        hr.line {
            border-style: dashed;
        }

        .box-resi {
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 10px;
        }

        .no-resi {
            border: 1px solid black;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px 0;
            font-size: 11.2px;
            font-weight: 600;
            width: 100%;
        }

        .barcode {
            width: 450px;
            height: 130px;
        }

        .box-body {
            display: flex;
            flex-direction: column;
        }

        .box-details {
            display: flex;
            justify-content: space-between;
            padding: 10px 20px;
        }

        .box-destination,
        .box-origin {
            width: 100%;
        }

        .box-addrees {
            border: 1.3px solid black;
            text-align: center;
            padding: 3px 0;
            font-size: 8.5px;
        }

        .box-box {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 10px;
            padding: 10px 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%
        }

        table tr td {
            padding: 0
        }
    </style>
</head>
<body style="padding: 10px;">
    <div class="box">
        <table>
            <tr>
                <td style="padding: 10px;" width="30px">
                    <img src="{{ logo_full_path }}" style="width: 80px;" alt="Logo sober">
                </td>
                <td style="padding: 10px;text-align:center">
                    {{ order.shipping_service }}
                </td>
                <td style="text-align: right;padding: 10px 20px;" width="30px">
                    <img src="{{ logoShipper }}" style="width: 80px;" alt="Logo sober">
                </td>
            </tr>
        </table>
        <hr class="line">
        <table>
            <tr>
                <td style="padding: 2px 10px;">
                    <div class="no-resi" style="text-align: center;">
                        No. Resi {{ order.shipment.shipment_id }}
                    </div>
                </td>
            </tr>
            <tr>
                <td style="padding: 2px 10px;">
                    <div style="width: 100%;text-align: center;">
                        <img src="{{ barcode }}" style="width: 80%;height: 45px;">
                    </div>
                </td>
            </tr>
        </table>
        <hr class="line">
        <table>
            <tr>
                <td style="padding: 0 10px;" width="48%">
                    <div class="box-destination">
                        <div class="name">
                            <b>Penerima: </b> {{ order.address.name }}
                        </div>
                        <br> {{ order.address.phone }}
                        <br>
                        <br> {{ order.address.address }}
                    </div>
                </td>
                <td style="padding: 0 10px;">
                    <div class="box-origin">
                        <div class="name">
                            <b>Pengirim: </b> {{ order.store.name }}
                        </div>
                        <br> {{ order.store.phone }}
                        <br>
                        <br> {{ order.store.address }}
                    </div>
                </td>
            </tr>
        </table>
        <table style="margin-top: 20px;">
            <tr>
                <td style="padding-left: 6px;padding-right: 3px;">
                    <div class="box-addrees" style="text-transform: uppercase;">
                        {{ order.address.city }}
                    </div>
                </td>
                <td style="padding-right: 6px;">
                    <div class="box-addrees" style="text-transform: uppercase;">
                        {{ order.store.city }}
                    </div>
                </td>
            </tr>
        </table>
        <table style="margin-top: 5px;">
            <tr>
                <td style="padding-left: 6px;padding-right: 3px;">
                    <div class="box-addrees" style="text-transform: uppercase;">
                        <b>Cashless</b>
                    </div>
                </td>
                <td style="padding-right: 6px;">
                    <div class="box-addrees">
                        <em>Penjual tidak perlu bayar ongkir ke kurir</em>
                    </div>
                </td>
            </tr>
        </table>
        <table style="font-size: 11.2px;margin-top: 6px;">
            <tr>
                <td style="padding: 0 6px;">
                    <span>
                        <b>Berat:</b> {{ order.shipment.weight/1000 }}gr
                    </span>
                </td>
                <td colspan="2" style="text-align: center;">
                    <img src="{{ barcode }}" class="barcode" style="height: 40px;width: 160px;">
                </td>
            </tr>
            <tr>
                <td style="padding: 0 6px;">
                    <b>NO. Pesanan:</b> {{ order.code }}
                </td>
            </tr>
        </table>
    </div>
    <table style="width: 100%;">
        <thead>
            <tr style="font-weight: 600;">
                <td>Nama Produk</td>
                <td>SKU</td>
                <td>QTY</td>
            </tr>
        </thead>
        <tbody>
            {% for item in order.products %}
                <tr>
                    <td>{{ item.product_name }}</td>
                    <td>{{ item.product.sku }}</td>
                    <td>{{ item.qty }}</td>
                </tr>
            {% endfor %}
        </tbody>
    </table>
</body>
</html>
