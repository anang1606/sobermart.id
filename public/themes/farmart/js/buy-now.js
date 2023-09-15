$(document).ready(function () {
    var address = {},
        inputValues = [],
        linkUUID = "";

    $("#footer-year").text("©" + new Date().getFullYear());
    var courierRequest,
        isPayment = false;

    $(document).on(
        "click",
        '[data-unify="btnShippingDurationDropDownCap"]',
        function () {
            // $(".css-79elbk").removeClass("hidden");
            getCourier($(this));
        }
    );

    const getCourier = (e) => {
        address = {};
        $('[name^="address"]').each(function () {
            const name = $(this).attr("name");
            const value = $(this).val();

            const attributeName = name.replace("address[", "").replace("]", "");
            address[attributeName] = value;
        });
        courierRequest = $.ajax({
            beforeSend: function () {
                e.addClass("css-10loe1m-unf-btn");
                e.removeClass("css-lwa81l-unf-btn");
                e.html(
                    '<div class="css-trcznm-unf-CircularV2 unf-loading-circle--show"><svg class="unf-loading-circle__loader bottom" viewBox="25 25 50 50" height="24px" width="24px"><circle class="unf-loading-circle__path--bottom" fill="none" cx="50" cy="50" r="15" stroke="var(--NN0, #FFFFFF)" stroke-width="5"></circle></svg><svg class="unf-loading-circle__loader top" viewBox="25 25 50 50" height="24px" width="24px"><circle class="unf-loading-circle__path--top" fill="none" cx="50" cy="50" r="15" stroke="var(--NN0, #FFFFFF)" stroke-width="5"></circle></svg></div>'
                );
                return
            },
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: window.location + "/shipment",
            data: {
                // "username": "TESTAPI",
                // "api_key": "25c898a9faea1a100859ecd9ef674548",
                // "from": "CGK10000",
                address: $('select[name="address[address_id]"]').val(),
                new_address: address,
                weight: e.data("weight"),
                store_id: e.data("testid"),
            },
            success: function (a) {
                console.log(a);
                if (a.error) {
                    a.data.map((data) => {
                        return Toastify({
                            text: data,
                            duration: 3000,
                            close: false,
                            gravity: "top", // `top` or `bottom`
                            position: "right", // `left`, `center` or `right`
                            stopOnFocus: true, // Prevents dismissing of toast on hover
                            style: {
                                background:
                                    "radial-gradient( circle 860px at 11.8% 33.5%,  rgba(240,30,92,1) 0%, rgba(244,49,74,1) 30.5%, rgba(249,75,37,1) 56.1%, rgba(250,88,19,1) 75.6%, rgba(253,102,2,1) 100.2% )",
                            },
                            onClick: function () {}, // Callback after click
                        }).showToast();
                    });
                } else {
                    // e.closest(".ddsd").append(courierList(a.data));
                }
            },
            error: function (t) {
                // console.log(t)
            },
            complete: function (t) {
                e.addClass("css-lwa81l-unf-btn");
                e.removeClass("css-10loe1m-unf-btn");
                e.html(
                    '<span class="ddsd-span"> <div class= "ddsd-cap-fill"><div class="ddsd-cap-text"> Shipping </div> <div class="ddsd-caret"></div> </div> </span> '
                );
            },
        });
    };

    function convertEtdToEstimasi(etdFrom, etdThru) {
        var today = new Date();
        var estimasiTibaFrom = new Date(today);
        var estimasiTibaThru = new Date(today);

        etdFrom = etdFrom !== null ? etdFrom : 1;
        etdThru = etdThru !== null ? etdThru : 1;

        estimasiTibaFrom.setDate(today.getDate() + parseInt(etdFrom));
        estimasiTibaThru.setDate(today.getDate() + parseInt(etdThru));

        var months = [
            "Januari",
            "Februari",
            "Maret",
            "April",
            "Mei",
            "Juni",
            "Juli",
            "Agustus",
            "September",
            "Oktober",
            "November",
            "Desember",
        ];

        var estimasiTiba = "";

        if (estimasiTibaFrom.getDate() === today.getDate() + 1) {
            if (estimasiTibaThru.getDate() === today.getDate() + 1) {
                estimasiTiba = "Besok";
            } else {
                estimasiTiba =
                    "Besok - " +
                    estimasiTibaThru.getDate() +
                    " " +
                    months[estimasiTibaThru.getMonth()];
            }
        } else if (
            estimasiTibaFrom.getMonth() === estimasiTibaThru.getMonth()
        ) {
            estimasiTiba =
                estimasiTibaFrom.getDate() +
                " - " +
                estimasiTibaThru.getDate() +
                " " +
                months[estimasiTibaFrom.getMonth()];
        } else {
            estimasiTiba =
                estimasiTibaFrom.getDate() +
                " " +
                months[estimasiTibaFrom.getMonth()] +
                " - " +
                estimasiTibaThru.getDate() +
                " " +
                months[estimasiTibaThru.getMonth()];
        }

        return estimasiTiba;
    }

    const formatRupiah = function (angka) {
        var number_string = angka.replace(/[^,\d]/g, "").toString(),
            split = number_string.split(","),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi),
            separator = "";

        if (ribuan) {
            separator = sisa ? "." : "";
            rupiah += separator + ribuan.join(".");
        }

        rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        return rupiah;
    };
})
