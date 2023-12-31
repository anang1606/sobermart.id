(() => {
    "use strict";
    function t(t, e, a, r, n, o, s, i) {
        var l,
            d = "function" == typeof t ? t.options : t;
        if (
            (e && ((d.render = e), (d.staticRenderFns = a), (d._compiled = !0)),
            r && (d.functional = !0),
            o && (d._scopeId = "data-v-" + o),
            s
                ? ((l = function (t) {
                      (t = t || (this.$vnode && this.$vnode.ssrContext) || (this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext)) || "undefined" == typeof __VUE_SSR_CONTEXT__ || (t = __VUE_SSR_CONTEXT__),
                          n && n.call(this, t),
                          t && t._registeredComponents && t._registeredComponents.add(s);
                  }),
                  (d._ssrRegister = l))
                : n &&
                  (l = i
                      ? function () {
                            n.call(this, (d.functional ? this.parent : this).$root.$options.shadowRoot);
                        }
                      : n),
            l)
        )
            if (d.functional) {
                d._injectStyles = l;
                var c = d.render;
                d.render = function (t, e) {
                    return l.call(e), c(t, e);
                };
            } else {
                var u = d.beforeCreate;
                d.beforeCreate = u ? [].concat(u, l) : [l];
            }
        return { exports: t, options: d };
    }
    const e = t(
        {
            data: function () {
                return { isLoading: !0, earningSales: [], colors: ["#fcb800", "#80bc00"], chart: null, filtering: "" };
            },
            props: {
                url: { type: String, default: null, required: !0 },
                format: { type: String, default: "dd/MM/yy", required: !1 },
                filters: {
                    type: Array,
                    default: function () {
                        return [];
                    },
                    required: !1,
                },
                filterDefault: { type: String, default: "", required: !1 },
            },
            mounted: function () {
                this.setFiltering(), this.renderChart();
            },
            methods: {
                setFiltering: function () {
                    var t = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : "";
                    if ((t || (t = this.filterDefault), this.filters.length)) {
                        var e = this.filters.find(function (e) {
                            return e.key == t;
                        });
                        this.filtering = e ? e.text : t;
                    }
                },
                renderChart: function () {
                    var t = this;
                    this.url &&
                        axios.get(this.url).then(function (e) {
                            e.data.error
                                ? Botble.showError(e.data.message)
                                : ((t.earningSales = e.data.data.earningSales),
                                  (t.chart = new ApexCharts(t.$el.querySelector(".sales-reports-chart"), {
                                      series: e.data.data.series,
                                      chart: { height: 350, type: "area", toolbar: { show: !1 } },
                                      dataLabels: { enabled: !1 },
                                      stroke: { curve: "smooth" },
                                      colors: e.data.data.colors,
                                      xaxis: { type: "datetime", categories: e.data.data.dates },
                                      tooltip: { x: { format: t.format } },
                                      noData: { text: BotbleVariables.languages.tables.no_data },
                                  })),
                                  t.chart.render());
                        });
                },
                clickFilter: function (t, e) {
                    var a = this;
                    e.preventDefault(), this.setFiltering("...");
                    var r = this;
                    axios.get(r.url, { params: { filter: t } }).then(function (e) {
                        if (e.data.error) Botble.showError(e.data.message);
                        else {
                            r.earningSales = e.data.data.earningSales;
                            var n = { xaxis: { type: "datetime", categories: e.data.data.dates }, series: e.data.data.series };
                            e.data.data.colors && (n.colors = e.data.data.colors), a.chart.updateOptions(n);
                        }
                        a.setFiltering(t);
                    });
                },
            },
        },
        function () {
            var t = this,
                e = t.$createElement,
                a = t._self._c || e;
            return a("div", [
                t.filters.length
                    ? a("div", { staticClass: "btn-group d-block text-end" }, [
                          a("a", { staticClass: "btn btn-sm btn-secondary", attrs: { href: "javascript:;", "data-bs-toggle": "dropdown", "aria-expanded": "false" } }, [
                              a("i", { staticClass: "fa fa-filter", attrs: { "aria-hidden": "true" } }),
                              t._v(" "),
                              a("span", [t._v(t._s(t.filtering))]),
                              t._v(" "),
                              a("i", { staticClass: "fa fa-angle-down " }),
                          ]),
                          t._v(" "),
                          a(
                              "ul",
                              { staticClass: "dropdown-menu float-end" },
                              t._l(t.filters, function (e) {
                                  return a("li", { key: e.key }, [
                                      a(
                                          "a",
                                          {
                                              attrs: { href: "#" },
                                              on: {
                                                  click: function (a) {
                                                      return t.clickFilter(e.key, a);
                                                  },
                                              },
                                          },
                                          [t._v("\n                    " + t._s(e.text) + "\n                ")]
                                      ),
                                  ]);
                              }),
                              0
                          ),
                      ])
                    : t._e(),
                t._v(" "),
                a("div", { staticClass: "sales-reports-chart" }),
                t._v(" "),
                t.earningSales.length
                    ? a("div", { staticClass: "row" }, [
                          a("div", { staticClass: "col-12" }, [
                              a(
                                  "ul",
                                  t._l(t.earningSales, function (e) {
                                      return a("li", { key: e.text }, [a("i", { staticClass: "fas fa-circle", style: { color: e.color } }), t._v(" " + t._s(e.text) + "\n                ")]);
                                  }),
                                  0
                              ),
                          ]),
                      ])
                    : t._e(),
                t._v(" "),
                a("div", { staticClass: "loading" }),
            ]);
        },
        [],
        !1,
        null,
        null,
        null
    ).exports;
    const a = t(
        {
            data: function () {
                return { isLoading: !0 };
            },
            props: {
                data: {
                    type: Array,
                    default: function () {
                        return [];
                    },
                    required: !0,
                },
            },
            mounted: function () {
                var t = this;
                if (this.data.length) {
                    var e = [],
                        a = [],
                        r = [],
                        n = 0;
                    this.data.map(function (t) {
                        (n += parseFloat(t.value)), r.push(t.label), a.push(t.color);
                    }),
                        0 == n
                            ? this.data.map(function () {
                                  e.push(100 / t.data.length);
                              })
                            : this.data.map(function (t) {
                                  e.push((100 / n) * parseFloat(t.value));
                              }),
                        new ApexCharts(this.$el.querySelector(".revenue-chart"), {
                            series: e,
                            colors: a,
                            chart: { height: "250", type: "donut" },
                            chartOptions: { labels: r },
                            plotOptions: { pie: { donut: { size: "71%", polygons: { strokeWidth: 0 } }, expandOnClick: !0 } },
                            states: { hover: { filter: { type: "darken", value: 0.9 } } },
                            dataLabels: { enabled: !1 },
                            legend: { show: !1 },
                            tooltip: { enabled: !1 },
                        }).render(),
                        jQuery && jQuery().tooltip && $('[data-bs-toggle="tooltip"]').tooltip({ placement: "top", boundary: "window" });
                }
            },
        },
        function () {
            var t = this,
                e = t.$createElement;
            t._self._c;
            return t._m(0);
        },
        [
            function () {
                var t = this.$createElement,
                    e = this._self._c || t;
                return e("div", [e("div", { staticClass: "revenue-chart" })]);
            },
        ],
        !1,
        null,
        null,
        null
    ).exports;
    function r(t) {
        return (
            (r =
                "function" == typeof Symbol && "symbol" == typeof Symbol.iterator
                    ? function (t) {
                          return typeof t;
                      }
                    : function (t) {
                          return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : typeof t;
                      }),
            r(t)
        );
    }
    function n(t, e, a) {
        return (
            (e = (function (t) {
                var e = (function (t, e) {
                    if ("object" !== r(t) || null === t) return t;
                    var a = t[Symbol.toPrimitive];
                    if (void 0 !== a) {
                        var n = a.call(t, e || "default");
                        if ("object" !== r(n)) return n;
                        throw new TypeError("@@toPrimitive must return a primitive value.");
                    }
                    return ("string" === e ? String : Number)(t);
                })(t, "string");
                return "symbol" === r(e) ? e : String(e);
            })(e)) in t
                ? Object.defineProperty(t, e, { value: a, enumerable: !0, configurable: !0, writable: !0 })
                : (t[e] = a),
            t
        );
    }
    vueApp.booting(function (t) {
        t.component("sales-reports-chart", e), t.component("revenue-chart", a);
    }),
        $(function () {
            var t;
            if (window.moment && jQuery().daterangepicker) {
                moment.locale($("html").attr("lang"));
                var a = $(document).find(".date-range-picker"),
                    r = a.data("format") || "YYYY-MM-DD",
                    o = a.data("start-date") || moment().subtract(29, "days"),
                    s = moment(),
                    i = moment().endOf("month");
                i > s && (i = s);
                var l = BotbleVariables.languages.reports,
                    d =
                        (n((t = {}), l.today, [s, s]),
                        n(t, l.this_week, [moment().startOf("week"), s]),
                        n(t, l.last_7_days, [moment().subtract(6, "days"), s]),
                        n(t, l.last_30_days, [moment().subtract(29, "days"), s]),
                        n(t, l.this_month, [moment().startOf("month"), i]),
                        n(t, l.this_year, [moment().startOf("year"), moment().endOf("year")]),
                        t);
                a.daterangepicker({ ranges: d, alwaysShowCalendars: !0, startDate: o, endDate: i, maxDate: i, opens: "left", drops: "auto", locale: { format: r }, autoUpdateInput: !1 }, function (t, r, n) {
                    $.ajax({
                        url: a.data("href"),
                        data: { date_from: t.format("YYYY-MM-DD"), date_to: r.format("YYYY-MM-DD"), predefined_range: n },
                        type: "GET",
                        success: function (a) {
                            a.error
                                ? Botble.showError(a.message)
                                : ($(".widget-item").each(function (t, e) {
                                      var r = $(e).prop("id");
                                      $("#".concat(r)).replaceWith($(a.data).find("#".concat(r)));
                                  }),
                                  $(".report-chart-content").length && ($(".report-chart-content").html(a.data.html), window.vueApp.vue.component("sales-reports-chart", e), new window.vueApp.vue({ el: "#report-chart" })),
                                  window.LaravelDataTables &&
                                      Object.keys(window.LaravelDataTables).map(function (e) {
                                          var a = window.LaravelDataTables[e],
                                              n = new URL(a.ajax.url());
                                          n.searchParams.set("date_from", t.format("YYYY-MM-DD")), n.searchParams.set("date_to", r.format("YYYY-MM-DD")), a.ajax.url(n.href).load();
                                      }));
                        },
                        error: function (t) {
                            Botble.handleError(t);
                        },
                    });
                }),
                    a.on("apply.daterangepicker", function (t, e) {
                        var a = $(this),
                            n = a.data("format-value");
                        n || (n = "__from__ - __to__");
                        var o = n.replace("__from__", e.startDate.format(r)).replace("__to__", e.endDate.format(r));
                        a.find("span").text(o);
                    });
            }
        });
})();
