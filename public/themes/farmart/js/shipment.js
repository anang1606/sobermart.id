$(document).ready(function () {
    var address = {},
        inputValues = [],
        linkUUID = "";

    $("#footer-year").text("©" + new Date().getFullYear());
    var courierRequest,
        isPayment = false;

    $(document).on(
        "click",
        '.css-m6di7s [data-unify="btnSafExpandSubtotalDetail"]',
        function () {
            $(this).closest(".css-m6di7s").toggleClass("shop-footer--expanded");
        }
    );

    $(document).on(
        "click",
        '.ddsd [data-unify="btnShippingDurationDropDownCap"]',
        function () {
            $(this).closest(".ddsd").addClass("ddsd--is-open");
            getCourier($(this));
        }
    );

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

    $(document).on("click", function (event) {
        if (
            !$(event.target).is(".ddsd-cap-text") &&
            !$(event.target).is(".ddsd-cap-fill") &&
            !$(event.target).is("button.eg8apji0.css-lwa81l-unf-btn") &&
            !$(event.target).is("span.ddsd-span") &&
            !$(event.target).is(".ddsd-caret")
        ) {
            if (courierRequest && typeof courierRequest.abort === "function") {
                courierRequest.abort(); // Membatalkan permintaan AJAX getCourier()
            }
            $(".ddsd").children(":not(.ddsd-cap)").remove();
            $(".ddsd").removeClass("ddsd--is-open");
            $(".css-10loe1m-unf-btn").addClass("css-lwa81l-unf-btn");
            $(".css-10loe1m-unf-btn").removeClass("css-10loe1m-unf-btn");
            $(".css-10loe1m-unf-btn").html(
                '<span class="ddsd-span"> <div class= "ddsd-cap-fill"><div class="ddsd-cap-text"> Shipping </div> <div class="ddsd-caret"></div> </div> </span> '
            );
        }
    });

    $(document).on('click','.modalVoucher',function(){
        $.ajax({
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: window.location + "/voucher",
            success: function (a) {
                // console.log(a)
                $('#voucher-body').html('')
                $('#voucher-body').html(a.data)
            },
            error: function (t) {
                console.log(t)
            },
        })
    })

    // $(document).on('click','.vc_RadioButton_radio',function(){
    //     const slug = $(this).data('slug')
    //     $('.vc_RadioButton_radio.vc_RadioButton_radioSelected.'+slug).html('')
    //     $('.vc_RadioButton_radio.vc_RadioButton_radioSelected.'+slug).removeClass('vc_RadioButton_radioSelected')
    //     $(this).addClass('vc_RadioButton_radioSelected')
    //     $(this).html('<svg enable-background="new 0 0 15 15" viewBox="0 0 15 15" role="img" class="stardust-icon stardust-icon-tick vc_RadioButton_tick"> <path stroke="none" d="m6.5 13.6c-.2 0-.5-.1-.7-.2l-5.5-4.8c-.4-.4-.5-1-.1-1.4s1-.5 1.4-.1l4.7 4 6.8-9.4c.3-.4.9-.5 1.4-.2.4.3.5 1 .2 1.4l-7.4 10.3c-.2.2-.4.4-.7.4 0 0 0 0-.1 0z"> </path> </svg>')
    // })

    $(document).on('change','.vc_RadioButton_',function(){
        $('#submit-modalVoucher').prop('disabled',false)
    })

    $(document).on('click','.vc_Card_container',function(){
        if (!$(this).hasClass('not-selected')) {
            const slug = $(this).data('slug')
            $('.vc_RadioButton_.'+slug).prop('checked', false).trigger('change')
            const vc_RadioButton_radio = $(this).find('.vc_RadioButton_.'+slug)
            vc_RadioButton_radio.prop('checked', true).trigger('change');
            $('#submit-modalVoucher').prop('disabled',false)
            // $('.vc_RadioButton_radio.vc_RadioButton_radioSelected.'+slug).html('')
            // $('.vc_RadioButton_radio.vc_RadioButton_radioSelected.'+slug).removeClass('vc_RadioButton_radioSelected')
            // vc_RadioButton_radio.addClass('vc_RadioButton_radioSelected')
            // vc_RadioButton_radio.html('<svg enable-background="new 0 0 15 15" viewBox="0 0 15 15" role="img" class="stardust-icon stardust-icon-tick vc_RadioButton_tick"> <path stroke="none" d="m6.5 13.6c-.2 0-.5-.1-.7-.2l-5.5-4.8c-.4-.4-.5-1-.1-1.4s1-.5 1.4-.1l4.7 4 6.8-9.4c.3-.4.9-.5 1.4-.2.4.3.5 1 .2 1.4l-7.4 10.3c-.2.2-.4.4-.7.4 0 0 0 0-.1 0z"> </path> </svg>')
        }
    })

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
                            onClick: function () { }, // Callback after click
                        }).showToast();
                    });
                } else {
                    // console.log(a)
                    e.closest(".ddsd").append(courierList(a.data));
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

    $(document).on("change", "#address_id", function () {
        changeAddress();
    });

    const changeAddress = () => {
        $(".right-side__lower-section-wrapper").remove();
        $("input.courier_price_el").val("");
        $("input.courier_details").val("");
        $("input.free_shipping").val("");
        $(".shop-footer__details")
            .children(":not(.shop-footer__row.subtotal)")
            .remove();
        $(".shop-footer__subtotal .shop-footer__row .subtotal p span.subtotal").each(function () {
            $(this).text(
                "Rp" + formatRupiah($(this).data("subtotal").toString())
            );
        });
        countAllShipFee();
    };

    $(document).on(
        "click",
        ".shop-group .shop-shipping-wrapper .ddsc-option",
        function () {
            const data = JSON.parse(atob($(this).data("shipment")));
            $(this)
                .closest(".shop-shipping-wrapper")
                .children(":not(.coachmark-target-wrapper-dropdown-shipping)")
                .remove();
            $(this)
                .closest(".shop-shipping-wrapper")
                .append(courierDetails(data));

            var subtotal = $(this)
                .closest(".shop-group")
                .find(
                    ".shop-footer__subtotal .shop-footer__row .subtotal p span.subtotal"
                );
            var fressShipping = $(this)
                .closest(".shop-shipping-wrapper")
                .find(".ddsd-cap input.free_shipping")
                .val();
            var subShipping =
                parseInt(data.price) -
                parseInt(fressShipping !== "" ? fressShipping : 0);

            const subShippingFee = subShipping < 0 ? 0 : subShipping;

            const subShipFee =
                parseInt(subtotal.data("subtotal")) + subShippingFee;

            subtotal.text("Rp" + formatRupiah(subShipFee.toString()));

            $(this)
                .closest(".shop-shipping-wrapper")
                .find(".ddsd-cap input.courier_price_el")
                .val(data.price);
            $(this)
                .closest(".shop-shipping-wrapper")
                .find(".ddsd-cap input.courier_details")
                .val($(this).data("shipment"));

            var shopFooterDetails = $(this)
                .closest(".shop-group")
                .find(".shop-footer__details");
            shopFooterDetails
                .children(":not(.shop-footer__row.subtotal)")
                .remove();
            shopFooterDetails.append(`<div class="shop-footer__row">
                    <div class="sf-row-label">
                        Biaya Pengiriman
                    </div>
                    <div class="sf-row-value">
                        Rp${formatRupiah(data.price.toString())}
                    </div>
                </div>`);
            if (fressShipping !== "") {
                shopFooterDetails.append(`<div class="shop-footer__row">
                    <div class="sf-row-label">
                        Diskon Pengiriman
                    </div>
                    <div class="sf-row-value">
                        - Rp${formatRupiah(fressShipping.toString())}
                    </div>
                </div>`);
            }
            countAllShipFee();
        }
    );

    const countAllShipFee = () => {
        var totalFee = 0,
            totalAll = 0,
            totalFreeShipping = 0,
            elementVallShipment = 0;
        const totalElementShipment = $("input.courier_price_el").length;

        $("input.courier_price_el").each(function () {
            if ($(this).val() !== "") {
                totalFee += parseInt($(this).val());
                elementVallShipment += 1;
            }
        });

        $("input.free_shipping").each(function () {
            if ($(this).val() !== "") {
                totalFreeShipping += parseInt($(this).val());
            }
        });

        if (totalElementShipment === elementVallShipment) {
            isPayment = true;
            $("#shipping-fee").show();
            $("#shipping-fee")
                .find(".shipping-fee")
                .text("Rp" + formatRupiah(totalFee.toString()));
            $(".count-summary").each(function () {
                totalAll += parseInt($(this).data("testid"));
            });
            if (totalFreeShipping > 0) {
                $("#shipping-fee-discount").show();
                $("#shipping-fee-discount")
                    .find(".shipping-fee-discount")
                    .text("- Rp" + formatRupiah(totalFreeShipping.toString()));
            }

            const diskon =
                $(".count-summary-diskon").data("testid") !== ""
                    ? $(".count-summary-diskon").data("testid")
                    : 0;

            $(".sgtr__value").text(
                "Rp" +
                formatRupiah(
                    (
                        totalAll -
                        diskon +
                        (totalFee - totalFreeShipping)
                    ).toString()
                )
            );
        } else {
            isPayment = false;
            $("#shipping-fee").hide();
            $("#shipping-fee").find(".shipping-fee").text(0);
            $(".count-summary").each(function () {
                totalAll += parseInt($(this).data("testid"));
            });
            const diskon =
                $(".count-summary-diskon").data("testid") !== ""
                    ? $(".count-summary-diskon").data("testid")
                    : 0;
            $(".sgtr__value").text(
                "Rp" +
                formatRupiah(
                    (
                        totalAll -
                        diskon +
                        (totalFee - totalFreeShipping)
                    ).toString()
                )
            );
        }
    };

    $(document).on(
        "click",
        'button[data-submit="choose-payment"]',
        function () {
            if (isPayment) {
                inputValues = [];

                $('input[name="courier_price[]"]').each(function (index) {
                    const courierPrice = $(this).val();
                    const courierDetails = $('input[name="courier_details[]"]')
                        .eq(index)
                        .val();
                    const storeId = $('input[name="store_id[]"]')
                        .eq(index)
                        .val();
                    const free_shipping = $('input[name="free_shipping[]"]')
                        .eq(index)
                        .val();

                    const inputObject = {
                        courier_price: courierPrice,
                        courier_details: courierDetails,
                        free_shipping: free_shipping,
                        store_id: storeId,
                    };

                    inputValues.push(inputObject);
                });
                address = {};
                $('[name^="address"]').each(function () {
                    const name = $(this).attr("name");
                    const value = $(this).val();

                    const attributeName = name
                        .replace("address[", "")
                        .replace("]", "");
                    address[attributeName] = value;
                });

                $('[data-testid="textErrorMessage"]').remove();
                var t = $(this),
                    n = t.closest("form"),
                    r = $(this).html();

                linkUUID = t.data("uuid");
                openModalPayment(n, t, r, inputValues, address);
            } else {
                $('[data-testid="textErrorMessage"]').remove();
                $(".summary-main-btn").append(
                    `<div class="ddsd-front-error" data-testid="textErrorMessage">Silahkan pilih pengiriman terlebih dahulu!!</div>`
                );
            }
        }
    );

    const openModalPayment = (form, t, r, inputValues, address) => {
        const body = document.querySelector("body");

        body.style.overflow = "hidden";
        body.innerHTML += `
            <div id="modal-payment">
                <div class="css-12bdkld-overlay">
                </div>
                <div class="css-7lpkml">
                    <article class="css-1qxu6r1-unf-modal">
                        <div class="css-d6mneo">
                            <div class="css-1isxc0n">
                                <div id="scrooge-iframe-wrapper" >
                                    <iframe src="https://pay.sobermart.id?origin_host=${btoa(window.location.origin)}" id="scrooge-iframe" frameborder="0" title="scrooge-iframe" name="scrooge_iframe" src></iframe>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        `;

        setTimeout(() => {
            const iframe = document.getElementById("scrooge-iframe");

            setTimeout(() => {
                const message = {
                    shimpent: inputValues,
                    address: $('select[name="address[address_id]"]').val(),
                    new_address: address,
                    customer: t.data("testid"),
                    code_unique: t.data("codeunique"),
                    voucher_applied: t.data("voucherapplied"),
                };
                const jsonMessage = JSON.stringify(message);
                iframe.contentWindow.postMessage(jsonMessage, iframe.src);
            }, 250);
            iframe.addEventListener("load", () => {

            });
        }, 750);
    };

    window.addEventListener("message", (event) => {
        if (event.origin === "https://pay.sobermart.id") {
            const message = event.data;
            if (message === "closeModal") {
                $("#modal-payment").remove();
                document.querySelector("body").style.overflow = "";
            } else {
                handlePayment(message);
            }
        }
    });

    const handlePayment = (paymentSelected) => {
        axios({
            method: "POST",
            url: linkUUID,
            data: {
                shimpent: inputValues,
                address: $('select[name="address[address_id]"]').val(),
                new_address: address,
                payment: JSON.parse(paymentSelected),
                // app_fee: t.data("fee-app"),
            },
        })
            .then((response) => {
                const result = response.data;
                // console.log(result);
                if (!result.error) {
                    // t.removeAttr("disabled"), t.html(r)
                    // window.location.href = (result.data.link_url.indexOf('https://') != '-1') ? result.data.link_url : 'https://' + result.data.link_url

                    window.location.href = window.location.href + "/success/" + result.data;
                    history.replaceState(null, null, "/cart");
                }
            })
            .catch((err) => {
                console.log(err);
            });
    };

    const handlePaymentResponse = (
        form,
        formSerialize,
        request,
        t,
        r,
        status
    ) => {
        request["payment_order_id"] = request["order_id"];
        request["payment_method"] = request["payment_type"];
        request["payment_status"] = status;
        delete request["order_id"];

        const jsonString = $.param(request);
        axios({
            method: "POST",
            url: form[0].getAttribute("action"),
            data: formSerialize + "&" + jsonString,
        })
            .then((response) => {
                const resultData = response.data;
                t.removeAttr("disabled"), t.html(r);
                // console.log(resultData);
                // if (resultData.success) {
                //     window.location.href = window.location.href + '/success'
                // }
            })
            .catch((err) => {
                console.log(err);
                t.removeAttr("disabled"), t.html(r);
            });
    };

    const courierDetails = (a) => {
        return `
            <div class="right-side__lower-section-wrapper">
                <div class="rslsw__dropdown-shipping-courier-wrapper">
                    <div>
                        <div class="css-12w03s4">
                            <div class="ddsc-label">
                                Courier Details
                            </div>
                            <div class="ddsc false">
                                <div class="ddsc-cap">
                                    <div class="ddsc-cap__result">
                                        <div class="ddsc-option__mvc-left">
                                            ${a.service_display
            } (Rp${formatRupiah(
                a.price.toString()
            )})
                                        </div>
                                        <div class="css-164r41r">
                                            Estimasi tiba ${convertEtdToEstimasi(
                a.etd_from,
                a.etd_thru
            )}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    };

    const courierList = (a) => {
        return `
            <div>
                <div class="ddsd-body">
                    <div class="ddsd-body-content-positioner">
                        <div class="ddsd-body-box css-jenq7o">
                            <div class="ddsd-options-wrapper">
                                ${a
                .map((data, key) => {
                    // console.log(data)
                    return featchCourier(data);
                })
                .join("")}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    };

    const featchCourier = (data) => {
        const json = btoa(JSON.stringify(data));
        return `
            <div class="ddsc-option" data-shipment="${json}">
                <div class="ddsc-option__wrapper">
                    <div class="ddsc-option__flex">
                        <div class="ddsc-option__col-left">
                            <div>
                                <p class="css-ohge0j-unf-heading e1qvo2ff8">
                                    ${data.service_display}
                                </p>
                            </div>
                            <p class="css-fkvnka-unf-heading e1qvo2ff8">Estimasi tiba ${convertEtdToEstimasi(
            data.etd_from,
            data.etd_thru
        )}</p>
                        </div>
                        <div class="ddsc-option__col-right">
                            <p class="css-fkvnka-unf-heading e1qvo2ff8">
                                Rp ${formatRupiah(data.price.toString())}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    };
});
