(() => {
    "use strict";
    $(document).ready(function () {
        ({
            currentType: "N/A",
            init: function () {
                this.initFormFields($(".option-type").val()),
                    this.eventListeners(),
                    $(".option-sortable").sortable({
                        stop: function () {
                            $(".option-sortable")
                                .sortable("toArray", { attribute: "data-index" })
                                .map(function (t, n) {
                                    $('.option-row[data-index="' + t + '"]')
                                        .find(".option-order")
                                        .val(n);
                                });
                        },
                    });
            },
            addNewRow: function () {
                return (
                    $(".add-new-row").click(function () {
                        var t = $(this).parent().find("table tbody"),
                            n = t.find("tr").last().clone(),
                            e = "options[" + t.find("tr").length + "][option_value]",
                            i = "options[" + t.find("tr").length + "][affect_price]",
                            o = "options[" + t.find("tr").length + "][affect_type]";
                        n.find(".option-label").attr("name", e), n.find(".affect_price").attr("name", i), n.find(".affect_type").attr("name", o), t.append(n);
                    }),
                    this
                );
            },
            removeRow: function () {
                return (
                    $(".option-setting-tab").on("click", ".remove-row", function () {
                        if (!($(this).parent().parent().parent().find("tr").length > 1)) return !1;
                        $(this).parent().parent().remove();
                    }),
                    this
                );
            },
            eventListeners: function () {
                this.onOptionChange(), this.addNewRow().removeRow();
            },
            onOptionChange: function () {
                var t = this;
                $(".option-type").change(function () {
                    var n = $(this).val();
                    (this.currentType = n), t.initFormFields(n);
                });
            },
            initFormFields: function (t) {
                if (((this.currentType = t), "N/A" !== t)) {
                    var n = (t = t.split("\\"))[t.length - 1];
                    "Field" !== n && (n = "multiple"), $(".empty, .option-setting-tab").hide(), (n = "#option-setting-" + n.toLowerCase()), $(n).show();
                }
            },
        }.init());
    });
})();
