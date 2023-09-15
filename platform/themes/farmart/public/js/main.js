(() => {
    "use strict";
    var e = e || {};
    window.MartApp = e, e.$iconChevronLeft = '<span class="slick-prev-arrow svg-icon"><svg><use href="#svg-icon-chevron-left" xlink:href="#svg-icon-chevron-left"></use></svg></span>', e.$iconChevronRight = '<span class="slick-next-arrow svg-icon"><svg><use href="#svg-icon-chevron-right" xlink:href="#svg-icon-chevron-right"></use></svg></span>', window._scrollBar = new ScrollBarHelper, e.isRTL = "rtl" === $("body").prop("dir"),
        function (t) {
            t.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": t('meta[name="csrf-token"]').attr("content")
                }
            }), t((function () {
                ! function () {
                    t(".form--quick-search .form-group--icon").show();
                    var e = t(".product-category-label .text");
                    t(document).on("change", ".product-category-select", (function () {
                        e.text(t.trim(t(this).find("option:selected").text()))
                    })), e.text(t.trim(t(".product-category-select option:selected").text())), t(document).ready((function () {
                        t(".preloader").addClass("fade-in")
                    }))
                }(), t(".menu-item-has-children > a > .sub-toggle").on("click", (function (e) {
                    e.preventDefault(), t(this).closest(".menu-item-has-children").toggleClass("active")
                })), t(".mega-menu__column > a > .sub-toggle").on("click", (function (e) {
                    e.preventDefault(), t(this).closest(".mega-menu__column").toggleClass("active")
                })), t(".toggle--sidebar").on("click", (function (e) {
                    e.preventDefault();
                    var a = t(this).attr("href");
                    t(this).toggleClass("active"), t(this).siblings("a").removeClass("active"), t(a).toggleClass("active"), t(a).siblings(".panel--sidebar").removeClass("active"), _scrollBar.hide()
                })), t(document).on("click", ".close-toggle--sidebar", (function (e) {
                    var a;
                    e.preventDefault(), t(this).data("toggle-closest") && (a = t(this).closest(t(this).data("toggle-closest"))), a && a.length || (a = t(this).closest(".panel--sidebar")), a.removeClass("active"), _scrollBar.reset()
                })), t("body").on("click", (function (e) {
                    t(e.target).siblings(".panel--sidebar").hasClass("active") && (t(".panel--sidebar").removeClass("active"), _scrollBar.reset())
                }))
            })), e.init = function () {
                e.$body = t(document.body), e.$toastLive = t("#toast-notifications"), e.$toastLive.length && (e.toast = new bootstrap.Toast(e.$toastLive)), e.$formSearch = t("#products-filter-form"), e.productListing = ".products-listing", e.$productListing = t(e.productListing), this.lazyLoad(null, !0), this.productQuickView(), this.slickSlides(), this.productQuantity(), this.addProductToWishlist(), this.addProductToCompare(), this.addProductToCart(), this.applyCouponCode(), this.productGallery(), this.lightBox(), this.handleTabBootstrap(), this.toggleViewProducts(), this.filterSlider(), this.toolbarOrderingProducts(), this.productsFilter(), this.searchProducts(), this.ajaxUpdateCart(), this.removeCartItem(), this.removeWishlistItem(), this.removeCompareItem(), this.submitReviewProduct(), this.vendorRegisterForm(), this.customerDashboard(), this.newsletterForm(), this.contactSellerForm(), this.stickyAddToCart(), this.backToTop(), this.stickyHeader(), this.recentlyViewedProducts(), e.$body.on("click", ".catalog-sidebar .backdrop, #cart-mobile .backdrop", (function (e) {
                    e.preventDefault(), t(this).parent().removeClass("active"), _scrollBar.reset()
                })), e.$body.on("click", ".sidebar-filter-mobile", (function (a) {
                    a.preventDefault(), e.toggleSidebarFilterProducts("open", t(a.currentTarget).data("toggle"))
                })), e.$body.on("submit", ".products-filter-form-vendor", (function (t) {
                    return !e.$formSearch.length || (e.$formSearch.trigger("submit"), !1)
                }))
            }, e.toggleSidebarFilterProducts = function () {
                var e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : "close",
                    a = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : "product-categories-primary-sidebar",
                    o = t('[data-toggle-target="' + a + '"]');
                "close" === e ? (o.removeClass("active"), _scrollBar.reset()) : (o.addClass("active"), _scrollBar.hide())
            }, e.productQuickView = function () {
                var a = t("#product-quick-view-modal");
                e.$body.on("click", ".product-quick-view-button .quick-view", (function (o) {
                    o.preventDefault();
                    var r = t(o.currentTarget);
                    r.addClass("loading"), a.removeClass("loaded").addClass("loading"), a.modal("show"), a.find(".product-modal-content").html(""), t.ajax({
                        url: r.data("url"),
                        type: "GET",
                        success: function (t) {
                            t.error || (a.find(".product-modal-content").html(t.data), e.productGallery(!0, a.find(".product-modal-content .product-gallery")), e.lightBox(), e.lazyLoad(a[0]))
                        },
                        error: function () { },
                        complete: function () {
                            a.addClass("loaded").removeClass("loading"), r.removeClass("loading")
                        }
                    })
                }))
            }, e.productGallery = function (a, o) {
                if (o && o.length || (o = t(".product-gallery")), o.length) {
                    var r = o.find(".product-gallery__wrapper"),
                        n = o.find(".product-gallery__variants");
                    a && (r.length && r.hasClass("slick-initialized") && r.slick("unslick"), n.length && n.hasClass("slick-initialized") && n.slick("unslick")), r.not(".slick-initialized").slick({
                        rtl: e.isRTL,
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        infinite: !1,
                        asNavFor: n,
                        dots: !1,
                        prevArrow: e.$iconChevronLeft,
                        nextArrow: e.$iconChevronRight,
                        lazyLoad: "ondemand"
                    }), n.not(".slick-initialized").slick({
                        rtl: e.isRTL,
                        slidesToShow: 8,
                        slidesToScroll: 1,
                        infinite: !1,
                        focusOnSelect: !0,
                        asNavFor: r,
                        vertical: !0,
                        prevArrow: '<span class="slick-prev-arrow svg-icon"><svg><use href="#svg-icon-arrow-up" xlink:href="#svg-icon-arrow-up"></use></svg></span>',
                        nextArrow: '<span class="slick-next-arrow svg-icon"><svg><use href="#svg-icon-chevron-down" xlink:href="#svg-icon-chevron-down"></use></svg></span>',
                        responsive: [{
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 6,
                                vertical: !1
                            }
                        }, {
                            breakpoint: 480,
                            settings: {
                                slidesToShow: 3,
                                vertical: !1
                            }
                        }]
                    })
                }
            }, e.lightBox = function () {
                t(".product-gallery--with-images").lightGallery({
                    selector: ".item a",
                    thumbnail: !0,
                    share: !1,
                    fullScreen: !1,
                    autoplay: !1,
                    autoplayControls: !1,
                    actualSize: !1
                });
                var e = t(".review-images-total.review-images");
                e.length && e.map((function (e, a) {
                    t(a).data("lightGallery") || t(a).lightGallery({
                        selector: "a",
                        thumbnail: !0,
                        share: !1,
                        fullScreen: !1,
                        autoplay: !1,
                        autoplayControls: !1,
                        actualSize: !1
                    })
                }))
            }, e.slickSlide = function (a) {
                var o = t(a);
                if (o.length && o.not(".slick-initialized")) {
                    var r = o.data("slick") || {};
                    r.appendArrows && (r.appendArrows = o.parent().find(r.appendArrows)), r = Object.assign(r, {
                        rtl: e.isRTL,
                        prevArrow: e.$iconChevronLeft,
                        nextArrow: e.$iconChevronRight
                    }), o.slick(r)
                }
            }, e.slickSlides = function () {
                t(".slick-slides-carousel").not(".slick-initialized").map((function (t, a) {
                    e.slickSlide(a)
                }))
            }, e.lazyLoad = function (t) {
                var a = arguments.length > 1 && void 0 !== arguments[1] && arguments[1];
                a ? e.lazyLoadInstance = new LazyLoad({
                    elements_selector: ".lazyload",
                    callback_error: function (e) {
                        e.setAttribute("src", siteConfig.img_placeholder)
                    }
                }) : new LazyLoad({
                    container: t,
                    elements_selector: ".lazyload",
                    callback_error: function (e) {
                        e.setAttribute("src", siteConfig.img_placeholder)
                    }
                })
            }, e.productQuantity = function () {
                e.$body.on("click", ".quantity .increase, .quantity .decrease", (function (a) {
                    a.preventDefault();
                    var o = t(this),
                        r = o.closest(".product-button").find(".quantity_button"),
                        n = o.closest(".quantity").siblings(".box-price").find(".price-current"),
                        i = n.html(),
                        s = o.siblings(".qty"),
                        c = parseInt(s.attr("step"), 10),
                        l = parseInt(s.val(), 10),
                        d = parseInt(s.attr("min"), 10),
                        u = parseInt(s.attr("max"), 10);
                    if (d = d || 1, u = u || l + 1, o.hasClass("decrease") && l > d) {
                        s.val(l - c), s.trigger("change");
                        var p = +r.attr("data-quantity");
                        p -= 1, r.attr("data-quantity", p);
                        var f = (1 * i - i / l).toFixed(2);
                        n.html(f)
                    }
                    if (o.hasClass("increase") && l < u) {
                        s.val(l + c), s.trigger("change");
                        var m = +r.attr("data-quantity");
                        m += 1, r.attr("data-quantity", m);
                        var h = (1 * i + i / l).toFixed(2);
                        n.html(h)
                    }
                    e.processUpdateCart(o)
                })), e.$body.on("keyup", ".quantity .qty", (function (a) {
                    a.preventDefault();
                    var o = t(this),
                        r = o.closest(".product-button").find(".quantity_button"),
                        n = o.closest(".quantity").siblings(".box-price").find(".price-current"),
                        i = n.data("current"),
                        s = parseInt(o.val(), 10),
                        c = parseInt(o.attr("min"), 10),
                        l = parseInt(o.attr("max"), 10);
                    if (s <= (l || s + 1) && s >= (c || 1)) {
                        r.attr("data-quantity", s);
                        var d = (i * s).toFixed(2);
                        n.html(d)
                    }
                    e.processUpdateCart(o)
                }))
            }, e.addProductToWishlist = function () {
                e.$body.on("click", ".wishlist-button .wishlist", (function (a) {
                    a.preventDefault();
                    var o = t(a.currentTarget);
                    o.addClass("loading"), t.ajax({
                        url: o.data("url"),
                        method: "POST",
                        success: function (a) {
                            var r;
                            if (a.error) return e.showError(a.message), !1;
                            e.showSuccess(a.message), t(".btn-wishlist .header-item-counter").text(a.data.count), null !== (r = a.data) && void 0 !== r && r.added ? t('.wishlist-button .wishlist[data-url="' + o.data("url") + '"]').addClass("added-to-wishlist") : t('.wishlist-button .wishlist[data-url="' + o.data("url") + '"]').removeClass("added-to-wishlist")
                        },
                        error: function (t) {
                            e.showError(t.message)
                        },
                        complete: function () {
                            o.removeClass("loading")
                        }
                    })
                }))
            }, e.addProductToCompare = function () {
                e.$body.on("click", ".compare-button .compare", (function (a) {
                    a.preventDefault();
                    var o = t(a.currentTarget);
                    o.addClass("loading"), t.ajax({
                        url: o.data("url"),
                        method: "POST",
                        success: function (a) {
                            if (a.error) return e.showError(a.message), !1;
                            e.showSuccess(a.message), t(".btn-compare .header-item-counter").text(a.data.count)
                        },
                        error: function (t) {
                            e.showError(t.message)
                        },
                        complete: function () {
                            o.removeClass("loading")
                        }
                    })
                }))
            }, e.addProductToCart = function () {
                e.$body.on("click", "form.cart-form button[type=submit]", (function (a) {
                    a.preventDefault();
                    var o = t(this).closest("form.cart-form"),
                        r = t(this);
                    r.addClass("loading");
                    var n = o.serializeArray();
                    n.push({
                        name: "checkout",
                        value: "checkout" === r.prop("name") ? 1 : 0
                    }), t.ajax({
                        type: "POST",
                        url: o.prop("action"),
                        data: t.param(n),
                        success: function (t) {
                            // console.log(t)
                            // return t.error ? (e.showError(t.message), void 0 !== t.data.next_url && (window.location.href = t.data.next_url), !1)
                            // : void 0 !== (t.data.next_url) ? (window.location.href = t.data.next_url, !1) : (e.showSuccess(t.message), void e.loadAjaxCart())
                            return t.error ? (e.showError(t.message), void 0 !== t.data && null !== t.data && void 0 !== t.data.next_url && (window.location.href = t.data.next_url), false)
                                : void 0 !== t.data && null !== t.data && void 0 !== t.data.next_url ? (window.location.href = t.data.next_url, false)
                                    : (e.showSuccess(t.message), void e.loadAjaxCart());
                        },
                        error: function (t) {
                            e.handleError(t, o)
                        },
                        complete: function () {
                            r.removeClass("loading")
                        }
                    })
                }))
            }, e.applyCouponCode = function () {
                t(document).on("keypress", ".form-coupon-wrapper .coupon-code", (function (e) {
                    if ("Enter" === e.key) return e.preventDefault(), e.stopPropagation(), t(e.currentTarget).closest(".form-coupon-wrapper").find(".btn-apply-coupon-code").trigger("click"), !1
                })), t(document).on("click", ".btn-apply-coupon-code", (function (a) {
                    a.preventDefault();
                    var o = t(a.currentTarget);
                    t.ajax({
                        url: o.data("url"),
                        type: "POST",
                        data: {
                            coupon_code: o.closest(".form-coupon-wrapper").find(".coupon-code").val(),
                            selected_cart: t('input[name="selected_cart"]').val()
                        },
                        beforeSend: function () {
                            o.prop("disabled", !0).addClass("loading")
                        },
                        success: function (a) {
                            // console.log(a)
                            a.error ? e.showError(a.message) : t(".cart-page-content").load(window.location.href + "?applied_coupon=1 .cart-page-content > *", (function () {
                                o.prop("disabled", !1).removeClass("loading"), e.showSuccess(a.message), e.calcSubTotal()
                            }))
                        },
                        error: function (t) {
                            e.handleError(t)
                        },
                        complete: function (e) {
                            var t;
                            200 == e.status && 0 == (null == e || null === (t = e.responseJSON) || void 0 === t ? void 0 : t.error) || o.prop("disabled", !1).removeClass("loading")
                        }
                    })
                })), t(document).on("click", ".btn-remove-coupon-code", (function (a) {
                    a.preventDefault();
                    var o = t(a.currentTarget),
                        r = o.text();
                    o.text(o.data("processing-text")), t.ajax({
                        url: o.data("url"),
                        type: "POST",
                        data: {
                            selected_cart: t('input[name="selected_cart"]').val()
                        },
                        success: function (a) {
                            a.error ? e.showError(a.message) : t(".cart-page-content").load(window.location.href + "?applied_coupon=0 .cart-page-content > *", (function () {
                                o.text(r), e.calcSubTotal()
                            }))
                        },
                        error: function (t) {
                            e.handleError(t)
                        },
                        complete: function (e) {
                            var t;
                            200 == e.status && 0 == (null == e || null === (t = e.responseJSON) || void 0 === t ? void 0 : t.error) || o.text(r)
                        }
                    })
                }))
            }, e.loadAjaxCart = function () {
                var a;
                null !== (a = window.siteConfig) && void 0 !== a && a.ajaxCart && t.ajax({
                    url: window.siteConfig.ajaxCart,
                    method: "GET",
                    success: function (a) {
                        // console.log(a)
                        a.error || (t(".css-nfajfx").html(a.data.html), e.lazyLoad(t(".css-nfajfx")[0]), t(".cart-counter-item").text(a.data.count), t(".menu--footer .icon-cart .cart-counter").text(a.data.count))
                        // a.error || (t(".mini-cart-content .widget-shopping-cart-content").html(a.data.html), t(".btn-shopping-cart .header-item-counter").text(a.data.count), t(".cart--mini .cart-price-total .cart-amount span").text(a.data.total_price), t(".menu--footer .icon-cart .cart-counter").text(a.data.count), e.lazyLoad(t(".mini-cart-content")[0]))
                    }
                })
            }, e.changeInputInSearchForm = function (o) {
                a = !1, e.$formSearch.find("input, select, textarea").each((function (e, a) {
                    var r = t(a),
                        n = r.attr("name"),
                        i = o[n] || null;
                    if ("checkbox" === r.attr("type")) r.prop("checked", !1), Array.isArray(i) ? r.prop("checked", i.includes(r.val())) : r.prop("checked", !!i);
                    else r.is("[name=max_price]") ? r.val(i || r.data("max")) : r.is("[name=min_price]") ? r.val(i || r.data("min")) : r.val() != i && r.val(i)
                })), a = !0
            }, e.convertFromDataToArray = function (t) {
                var a = [];
                return t.forEach((function (t) {
                    if (t.value) {
                        if (["min_price", "max_price"].includes(t.name))
                            if (e.$formSearch.find("input[name=" + t.name + "]").data(t.name.substring(0, 3)) == parseInt(t.value)) return;
                        a.push(t)
                    }
                })), a
            };
            var a = !0;
            e.productsFilter = function () {
                function o(e) {
                    e.length && (e.addClass("opened"), e.closest("ul").closest("li.category-filter").length && o(e.closest("ul").closest("li.category-filter")))
                }
                e.widgetProductCategories = ".widget-product-categories", e.$widgetProductCategories = t(e.widgetProductCategories), t(document).on("change", "#products-filter-form .product-filter-item", (function () {
                    a && t(this).closest("form").trigger("submit")
                })), t(".widget-product-categories").find("li a.active").map((function (e, a) {
                    var r = t(a).closest("li.category-filter").closest("ul").closest("li.category-filter");
                    r.length && o(r)
                })), t(".catalog-toolbar__ordering input[name=sort-by]").on("change", (function (a) {
                    e.$formSearch.find("input[name=sort-by]").val(t(a.currentTarget).val()), e.$formSearch.trigger("submit")
                })), e.$body.on("click", ".cat-menu-close", (function (e) {
                    e.preventDefault(), t(this).closest("li").toggleClass("opened")
                })), t(document).on("click", e.widgetProductCategories + " li a", (function (a) {
                    a.preventDefault();
                    var o = t(a.currentTarget),
                        r = o.hasClass("active"),
                        n = o.closest(e.widgetProductCategories);
                    n.find("li a").removeClass("active"), o.addClass("active");
                    var i = n.find("input[name='categories[]']");
                    if (i.length) r ? (o.removeClass("active"), i.val("")) : i.val(o.data("id")), i.trigger("change");
                    else {
                        var s = o.attr("href");
                        e.$formSearch.attr("action", s).trigger("submit")
                    }
                })),
                    t(document).on("submit", "#products-filter-form", function (a) {
                        a.preventDefault();
                        var o = t(a.currentTarget),
                            r = o.serializeArray(),
                            n = e.convertFromDataToArray(r),
                            i = [],
                            s = e.$productListing.find("input[name=page]");
                        s.val() && n.push({ name: "page", value: s.val() }),
                            n.map(function (e) {
                                i.push(encodeURIComponent(e.name) + "=" + e.value);
                            });
                        var c = o.attr("action") + (i && i.length ? "?" + i.join("&") : "");
                        n.push({ name: "_", value: +new Date() }),
                            t.ajax({
                                url: o.attr("action"),
                                type: "GET",
                                data: n,
                                beforeSend: function () {
                                    e.$productListing.find(".loading").show(), t("html, body").animate({ scrollTop: e.$productListing.offset().top - 200 }, 500);
                                    var a = e.$formSearch.find(".nonlinear");
                                    a.length && a[0].noUiSlider.set([e.$formSearch.find("input[name=min_price]").val(), e.$formSearch.find("input[name=max_price]").val()]), e.toggleSidebarFilterProducts();
                                },
                                success: function (a) {
                                    // console.log(a)
                                    if (0 == a.error) {
                                        // window.history.pushState(n, a.message, c)
                                        // window.location.reload()
                                        let cssAdsStyles = $('#ads-categories-css')
                                        let cssAdsPath = cssAdsStyles.attr("href")

                                        var o, r;
                                        e.$productListing.html(a.data.view);
                                        $('#ads-container').html(a.data.ads)
                                        e.lazyLoad(e.$adsContainer[0])
                                        $(".ads-categories-carousel").owlCarousel({
                                            autoWidth: false,
                                            loop: true,
                                            stagePadding: 0,
                                            margin: 0,
                                            nav: true,
                                            dots: false,
                                            items: 1,
                                            center: false,
                                            autoplay: true,
                                        });

                                        var i,
                                            s = a.message;
                                        if (
                                            (s && t(".products-found").length && t(".products-found").html('<span class="text-primary me-1">' + s.substr(0, s.indexOf(" ")) + "</span>" + s.substr(s.indexOf(" ") + 1)),
                                                e.lazyLoad(e.$productListing[0]),
                                                null !== (o = a.additional) && void 0 !== o && o.category_tree)
                                        )
                                            t(".widget-product-categories .widget-layered-nav-list").html(null === (i = a.additional) || void 0 === i ? void 0 : i.category_tree);
                                        null !== (r = a.additional) && void 0 !== r && r.breadcrumb && t(".page-breadcrumbs div").html(a.additional.breadcrumb), c != window.location.href && window.history.pushState(n, a.message, c);
                                        cssAdsStyles.attr("href", cssAdsPath)
                                    } else e.showError(a.message || "Opp!");
                                },
                                error: function (t) {
                                    e.handleError(t);
                                },
                                complete: function () {
                                    e.$productListing.find(".loading").hide();
                                },
                            });
                    }), window.addEventListener("popstate", (function () {
                        var t = window.location.origin + window.location.pathname;
                        if (e.$formSearch.length) {
                            e.$formSearch.attr("action", t);
                            var a = e.parseParamsSearch();
                            e.changeInputInSearchForm(a), e.$formSearch.trigger("submit")
                        } else history.back()
                    }), !1), t(document).on("click", e.productListing + " .pagination a", (function (a) {
                        a.preventDefault();
                        var o = new URL(t(a.currentTarget).attr("href")).searchParams.get("page");
                        e.$productListing.find("input[name=page]").val(o), e.$formSearch.trigger("submit")
                    }))
            }, e.parseParamsSearch = function (e) {
                for (var t, a = arguments.length > 1 && void 0 !== arguments[1] && arguments[1], o = e || window.location.search.substring(1), r = /([^&=]+)=?([^&]*)/g, n = /\+/g, i = function (e) {
                    return decodeURIComponent(e.replace(n, " "))
                }, s = {}; t = r.exec(o);) {
                    var c = i(t[1]),
                        l = i(t[2]);
                    "[]" == c.substring(c.length - 2) ? (a && (c = c.substring(0, c.length - 2)), (s[c] || (s[c] = [])).push(l)) : s[c] = l
                }
                return s
            }, e.searchProducts = function () {
                t("body").on("click", (function (e) {
                    t(e.target).closest(".form--quick-search").length || t(".panel--search-result").removeClass("active")
                }));
                var e = null;

                function a(a) {
                    var o = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : null,
                        r = a.find(".panel--search-result"),
                        n = a.find(".input-search-product").val();
                    if (n) {
                        var i = a.find("button[type=submit]");
                        e = t.ajax({
                            type: "GET",
                            url: o || a.data("ajax-url"),
                            data: o ? [] : a.serialize(),
                            beforeSend: function () {
                                null != e && e.abort(), i.addClass("loading")
                            },
                            success: function (e) {
                                if (e.error) r.html("").removeClass("active");
                                else if (o) {
                                    var a = t("<div>" + e.data + "</div>");
                                    r.find(".panel__content").find(".loadmore-container").remove(), r.find(".panel__content").append(a.find(".panel__content").contents())
                                } else r.html(e.data).addClass("active");
                                i.removeClass("loading")
                            },
                            error: function () {
                                i.removeClass("loading")
                            }
                        })
                    } else r.html("").removeClass("active")
                }
                t(".form--quick-search .input-search-product").on("keyup", (function () {
                    a(t(this).closest("form"))
                })), t(".form--quick-search .product-category-select").on("change", (function () {
                    a(t(this).closest("form"))
                })), t(".form--quick-search").on("click", ".loadmore", (function (e) {
                    e.preventDefault();
                    var o = t(this).closest("form");
                    t(this).addClass("loading"), a(o, t(this).attr("href"))
                }))
            }, e.processUpdateCart = function (a) {
                if (!a.data('key')) return !1
                var o = $("#" + a.data('key'));
                if (!o.length) return !1;
                t.ajax({
                    type: "POST",
                    cache: !1,
                    url: o.prop("action"),
                    data: new FormData(o[0]),
                    contentType: !1,
                    processData: !1,
                    beforeSend: function () {
                        a.addClass("loading")
                    },
                    success: function (a) {
                        // console.log(a)
                        if (a.error) return e.showError(a.message), !1;
                        t(".cart-page-content").load(window.siteConfig.cartUrl + " .cart-page-content > *", (function () {
                            e.lazyLoad(t(".cart-page-content")[0])
                        })), e.loadAjaxCart(), e.showSuccess(a.message)
                    },
                    error: function (t) {
                        a.closest(".ps-table--shopping-cart").removeClass("content-loading"), e.showError(t.message)
                    },
                    complete: function () {
                        a.removeClass("loading")
                    }
                })
            }, e.ajaxUpdateCart = function (a) {
                t(document).on("click", ".cart-page-content .update_cart", (function (a) {
                    a.preventDefault();
                    var o = t(a.currentTarget);
                    e.processUpdateCart(o)
                }))
            }, e.removeCartItem = function () {
                t(document).on("click", ".remove-cart-item", (function (a) {
                    a.preventDefault();
                    var o = t(this);
                    t.ajax({
                        url: o.data("url"),
                        method: "GET",
                        beforeSend: function () {
                            o.addClass("loading")
                        },
                        success: function (a) {
                            var o;
                            if (a.error) return e.showError(a.message), !1;
                            var r = t(".cart-page-content");
                            r.length && null !== (o = window.siteConfig) && void 0 !== o && o.cartUrl && r.load(window.siteConfig.cartUrl + " .cart-page-content > *", (function () {
                                e.lazyLoad(r[0])
                            })), e.loadAjaxCart()
                        },
                        error: function (t) {
                            e.showError(t.message)
                        },
                        complete: function () {
                            o.removeClass("loading")
                        }
                    })
                }))
            }, e.removeWishlistItem = function () {
                t(document).on("click", ".remove-wishlist-item", (function (a) {
                    a.preventDefault();
                    var o = t(this);
                    t.ajax({
                        url: o.data("url"),
                        method: "POST",
                        data: {
                            _method: "DELETE"
                        },
                        beforeSend: function () {
                            o.addClass("loading")
                        },
                        success: function (a) {
                            a.error ? e.showError(a.message) : (e.showSuccess(a.message), t(".btn-wishlist .header-item-counter").text(a.data.count), o.closest("tr").remove())
                        },
                        error: function (t) {
                            e.showError(t.message)
                        },
                        complete: function () {
                            o.removeClass("loading")
                        }
                    })
                }))
            }, e.removeCompareItem = function () {
                t(document).on("click", ".remove-compare-item", (function (a) {
                    a.preventDefault();
                    var o = t(this);
                    t.ajax({
                        url: o.data("url"),
                        method: "POST",
                        data: {
                            _method: "DELETE"
                        },
                        beforeSend: function () {
                            o.addClass("loading")
                        },
                        success: function (a) {
                            a.error ? e.showError(a.message) : (e.showSuccess(a.message), t(".btn-compare .header-item-counter").text(a.data.count), t(".compare-page-content").load(window.location.href + " .compare-page-content > *"))
                        },
                        error: function (t) {
                            e.showError(t.message)
                        },
                        complete: function () {
                            o.removeClass("loading")
                        }
                    })
                }))
            }, e.handleTabBootstrap = function () {
                var e = window.location.hash;
                if (e) {
                    var a = t('a[href="' + e + '"]');
                    if (a.length) new bootstrap.Tab(a[0]).show()
                }
            }, e.filterSlider = function () {
                t(".nonlinear").each((function (a, o) {
                    var r = t(o),
                        n = r.data("min"),
                        i = r.data("max"),
                        s = t(o).closest(".nonlinear-wrapper");
                    noUiSlider.create(o, {
                        connect: !0,
                        behaviour: "tap",
                        start: [s.find(".product-filter-item-price-0").val(), s.find(".product-filter-item-price-1").val()],
                        range: {
                            min: n,
                            "10%": .1 * i,
                            "20%": .2 * i,
                            "30%": .3 * i,
                            "40%": .4 * i,
                            "50%": .5 * i,
                            "60%": .6 * i,
                            "70%": .7 * i,
                            "80%": .8 * i,
                            "90%": .9 * i,
                            max: i
                        }
                    });
                    var c = [s.find(".slider__min"), s.find(".slider__max")];
                    o.noUiSlider.on("update", (function (t, a) {
                        c[a].html(e.numberFormat(t[a]))
                    })), o.noUiSlider.on("change", (function (e, t) {
                        s.find(".product-filter-item-price-" + t).val(Math.round(e[t])).trigger("change")
                    }))
                }))
            }, e.numberFormat = function (e, t, a, o) {
                var r = isFinite(+e) ? +e : 0,
                    n = isFinite(+t) ? Math.abs(t) : 0,
                    i = void 0 === o ? "," : o,
                    s = void 0 === a ? "." : a,
                    c = (n ? function (e, t) {
                        var a = Math.pow(10, t);
                        return Math.round(e * a) / a
                    }(r, n) : Math.round(r)).toString().split(".");
                return c[0].length > 3 && (c[0] = c[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, i)), (c[1] || "").length < n && (c[1] = c[1] || "", c[1] += new Array(n - c[1].length + 1).join("0")), c.join(s)
            }, e.submitReviewProduct = function () {
                var a = [],
                    o = function (e) {
                        for (var t = new ClipboardEvent("").clipboardData || new DataTransfer, o = 0, n = a; o < n.length; o++) {
                            var i = n[o];
                            t.items.add(i)
                        }
                        e.files = t.files, r(e)
                    },
                    r = function (e) {
                        var a = t(".image-upload__text"),
                            o = t(e).data("max-files"),
                            r = e.files.length;
                        o ? (r >= o ? a.closest(".image-upload__uploader-container").addClass("d-none") : a.closest(".image-upload__uploader-container").removeClass("d-none"), a.text(r + "/" + o)) : a.text(r);
                        var n = t(".image-viewer__list"),
                            i = t("#review-image-template").html();
                        if (n.addClass("is-loading"), n.find(".image-viewer__item").remove(), r) {
                            for (var s = r - 1; s >= 0; s--) n.prepend(i.replace("__id__", s));
                            for (var c = function (t) {
                                var a = new FileReader;
                                a.onload = function (e) {
                                    n.find(".image-viewer__item[data-id=" + t + "]").find("img").attr("src", e.target.result)
                                }, a.readAsDataURL(e.files[t])
                            }, l = r - 1; l >= 0; l--) c(l)
                        }
                        n.removeClass("is-loading")
                    };
                t(document).on("change", ".form-review-product input[type=file]", (function (r) {
                    r.preventDefault();
                    var n = this,
                        i = t(n),
                        s = i.data("max-size");
                    Object.keys(n.files).map((function (t) {
                        if (s && n.files[t].size / 1024 > s) {
                            var o = i.data("max-size-message").replace("__attribute__", n.files[t].name).replace("__max__", s);
                            e.showError(o)
                        } else a.push(n.files[t])
                    }));
                    var c = a.length,
                        l = i.data("max-files");
                    l && c > l && a.splice(c - l - 1, c - l), o(n)
                })), t(document).on("click", ".form-review-product .image-viewer__icon-remove", (function (e) {
                    e.preventDefault();
                    var r = t(e.currentTarget).closest(".image-viewer__item").data("id");
                    a.splice(r, 1);
                    var n = t(".form-review-product input[type=file]")[0];
                    o(n)
                })), sessionStorage.reloadReviewsTab && (t('#product-detail-tabs a[href="#product-reviews"]').length && new bootstrap.Tab(t('#product-detail-tabs a[href="#product-reviews"]')[0]).show(), sessionStorage.reloadReviewsTab = !1), t(document).on("click", ".form-review-product button[type=submit]", (function (a) {
                    a.preventDefault(), a.stopPropagation();
                    var o = t(a.currentTarget),
                        r = t(this).closest("form");
                    t.ajax({
                        type: "POST",
                        cache: !1,
                        url: r.prop("action"),
                        data: new FormData(r[0]),
                        contentType: !1,
                        processData: !1,
                        beforeSend: function () {
                            o.prop("disabled", !0).addClass("loading")
                        },
                        success: function (t) {
                            t.error ? e.showError(t.message) : (r.find("select").val(0), r.find("textarea").val(""), e.showSuccess(t.message), setTimeout((function () {
                                sessionStorage.reloadReviewsTab = !0, window.location.reload()
                            }), 1500))
                        },
                        error: function (t) {
                            e.handleError(t, r)
                        },
                        complete: function () {
                            o.prop("disabled", !1).removeClass("loading")
                        }
                    })
                }))
            }, e.vendorRegisterForm = function () {
                t(document).on("click", "input[name=is_vendor]", (function () {
                    1 == t(this).val() ? t(".show-if-vendor").slideDown().show() : (t(".show-if-vendor").slideUp(500), t(this).closest("form").find("button[type=submit]").prop("disabled", !1))
                })), t("#shop-url-register").on("keyup", (function () {
                    var e = t(this).closest(".form-group").find("span small");
                    e.html(e.data("base-url") + "/<strong>" + t(this).val().toLowerCase() + "</strong>")
                })).on("change", (function () {
                    var e = t(this),
                        a = e.val();
                    if (a) {
                        var o = e.closest(".form-group").find("span small");
                        t.ajax({
                            url: e.data("url"),
                            type: "POST",
                            data: {
                                url: a
                            },
                            beforeSend: function () {
                                e.prop("disabled", !0), e.closest("form").find("button[type=submit]").prop("disabled", !0)
                            },
                            success: function (a) {
                                var r, n;
                                (a.error ? (e.addClass("is-invalid").removeClass("is-valid"), t(".shop-url-status").removeClass("text-success").addClass("text-danger").text(a.message)) : (e.addClass("is-valid").removeClass("is-invalid"), t(".shop-url-status").removeClass("text-danger").addClass("text-success").text(a.message), e.closest("form").find("button[type=submit]").prop("disabled", !1)), null !== (r = a.data) && void 0 !== r && r.slug) && o.html(o.data("base-url") + "/<strong>" + (null === (n = a.data) || void 0 === n ? void 0 : n.slug) + "</strong>")
                            },
                            error: function () { },
                            complete: function () {
                                e.prop("disabled", !1)
                            }
                        })
                    }
                }))
            }, e.customerDashboard = function () {
                t.fn.datepicker && t("#date_of_birth").datepicker({
                    format: "yyyy-mm-dd",
                    orientation: "bottom"
                }), t("#avatar").on("change", (function (e) {
                    var a = e.currentTarget;
                    if (a.files && a.files[0]) {
                        var o = new FileReader;
                        o.onload = function (e) {
                            t(".userpic-avatar").attr("src", e.target.result)
                        }, o.readAsDataURL(a.files[0])
                    }
                })), t(document).on("click", ".btn-trigger-delete-address", (function (e) {
                    e.preventDefault(), t(".btn-confirm-delete").data("url", t(this).data("url")), t("#confirm-delete-modal").modal("show")
                })), t(document).on("click", ".btn-confirm-delete", (function (a) {
                    a.preventDefault();
                    var o = t(this);
                    t.ajax({
                        url: o.data("url"),
                        type: "GET",
                        beforeSend: function () {
                            o.addClass("loading")
                        },
                        success: function (a) {
                            o.closest(".modal").modal("hide"), a.error ? e.showError(a.message) : (e.showSuccess(a.message), t('.btn-trigger-delete-address[data-url="' + o.data("url") + '"]').closest(".col").remove())
                        },
                        error: function (t) {
                            e.handleError(t)
                        },
                        complete: function () {
                            o.removeClass("loading")
                        }
                    })
                }))
            }, e.newsletterForm = function () {
                t(document).on("submit", "form.subscribe-form", (function (a) {
                    a.preventDefault(), a.stopPropagation();
                    var o = t(a.currentTarget),
                        r = o.find("button[type=submit]");
                    t.ajax({
                        type: "POST",
                        cache: !1,
                        url: o.prop("action"),
                        data: new FormData(o[0]),
                        contentType: !1,
                        processData: !1,
                        beforeSend: function () {
                            r.prop("disabled", !0).addClass("button-loading")
                        },
                        success: function (t) {
                            "undefined" != typeof refreshRecaptcha && refreshRecaptcha(), t.error ? e.showError(t.message) : (o.find("input[type=email]").val(""), e.showSuccess(t.message))
                        },
                        error: function (t) {
                            "undefined" != typeof refreshRecaptcha && refreshRecaptcha(), e.handleError(t)
                        },
                        complete: function () {
                            r.prop("disabled", !1).removeClass("button-loading")
                        }
                    })
                }))
            }, e.contactSellerForm = function () {
                t(document).on("click", "form.form-contact-store button[type=submit]", (function (a) {
                    a.preventDefault(), a.stopPropagation();
                    var o = t(a.currentTarget),
                        r = o.closest("form");
                    t.ajax({
                        type: "POST",
                        cache: !1,
                        url: r.prop("action"),
                        data: new FormData(r[0]),
                        contentType: !1,
                        processData: !1,
                        beforeSend: function () {
                            o.prop("disabled", !0).addClass("button-loading")
                        },
                        success: function (t) {
                            "undefined" != typeof refreshRecaptcha && refreshRecaptcha(), t.error ? e.showError(t.message) : (r.find("input[type=email]:not(:disabled)").val(""), r.find("input[type=text]:not(:disabled)").val(""), r.find("textarea").val(""), e.showSuccess(t.message))
                        },
                        error: function (t) {
                            "undefined" != typeof refreshRecaptcha && refreshRecaptcha(), e.handleError(t)
                        },
                        complete: function () {
                            o.prop("disabled", !1).removeClass("button-loading")
                        }
                    })
                }))
            }, e.recentlyViewedProducts = function () {
                e.$body.find(".header-recently-viewed").each((function () {
                    var a, o = t(this);
                    o.hover((function () {
                        var r = o.find(".recently-viewed-products");
                        if (!o.data("loaded") && !a) {
                            var n = o.data("url");
                            n && t.ajax({
                                type: "GET",
                                url: n,
                                beforeSend: function () {
                                    a = !0
                                },
                                success: function (t) {
                                    t.error ? e.showError(t.message) : (r.html(t.data), r.find(".product-list li").length > 0 && e.slickSlide(r.find(".product-list")), o.data("loaded", !0).find(".loading--wrapper").addClass("d-none"))
                                },
                                error: function (t) {
                                    e.handleError(t)
                                },
                                complete: function () {
                                    a = !1
                                }
                            })
                        }
                    }))
                }))
            }, e.showNotice = function (t, a) {
                e.$toastLive.removeClass((function (e, t) {
                    return (t.match(/(^|\s)toast--\S+/g) || []).join(" ")
                })), e.$toastLive.addClass("toast--" + t), e.$toastLive.find(".toast-body .toast-message").html(a), e.toast.show()
            }, e.handleValidationError = function (a) {
                var o = "";
                t.each(a, (function (e, t) {
                    "" !== o && (o += "<br />"), o += t
                })), e.showError(o)
            }, e.toggleViewProducts = function () {
                t(document).on("click", ".store-list-filter-button", (function (e) {
                    e.preventDefault(), t("#store-listing-filter-form-wrap").toggle(500)
                })), e.$body.on("click", ".toolbar-view__icon a", (function (a) {
                    a.preventDefault();
                    var o = t(a.currentTarget);
                    o.closest(".toolbar-view__icon").find("a").removeClass("active"), o.addClass("active"), t(o.data("target")).removeClass(o.data("class-remove")).addClass(o.data("class-add")), e.$formSearch.find("input[name=layout]").val(o.data("layout"));
                    var r = new URLSearchParams(window.location.search);
                    r.set("layout", o.data("layout"));
                    var n = window.location.protocol + "//" + window.location.host + window.location.pathname + "?" + r.toString();
                    n != window.location.href && window.history.pushState(e.$productListing.html(), "", n)
                }))
            }, e.toolbarOrderingProducts = function () {
                e.$body.on("click", ".catalog-toolbar__ordering .dropdown .dropdown-menu a", (function (e) {
                    e.preventDefault();
                    var a = t(e.currentTarget),
                        o = a.closest(".dropdown");
                    o.find("li").removeClass("active"), a.closest("li").addClass("active"), o.find("a[data-bs-toggle=dropdown").html(a.html()), a.closest(".catalog-toolbar__ordering").find("input[name=sort-by]").val(a.data("value")).trigger("change")
                }))
            }, e.backToTop = function () {
                var e = 0,
                    a = t("#back2top");
                t(window).scroll((function () {
                    var o = t(window).scrollTop();
                    o > e && o > 500 ? a.addClass("active") : a.removeClass("active"), e = o
                })), a.on("click", (function () {
                    t("html, body").animate({
                        scrollTop: "0px"
                    }, 0)
                }))
            }, e.stickyHeader = function () {
                var e = t(".header-js-handler"),
                    a = e.height();
                e.each((function () {
                    if (!0 === t(this).data("sticky")) {
                        var e = t(this);
                        t(window).scroll((function () {
                            t(this).scrollTop() > a ? e.addClass("header--sticky") : e.removeClass("header--sticky")
                        }))
                    }
                }))
            }, e.stickyAddToCart = function () {
                var e = t(".header--product");
                t(window).scroll((function () {
                    t(this).scrollTop() > 50 ? e.addClass("header--sticky") : e.removeClass("header--sticky")
                })), t(".header--product ul li > a ").on("click", (function (e) {
                    e.preventDefault();
                    var a = t(this).attr("href");
                    t(this).closest("li").siblings("li").removeClass("active"), t(this).closest("li").addClass("active"), t(a).closest(".product-detail-tabs").find("a").removeClass("active"), t(a).addClass("active"), t(".header--product ul li").removeClass("active"), t('.header--product ul li a[href="' + a + '"]').closest("li").addClass("active"), t("#product-detail-tabs-content > .tab-pane").removeClass("active show"), t(t(a).attr("href")).addClass("active show"), t("html, body").animate({
                        scrollTop: t(a).offset().top - t(".header--product .navigation").height() - 165 + "px"
                    }, 0)
                }));
                var a = t(".product-details .entry-product-header"),
                    o = t(".sticky-atc-wrap");
                if (o.length && a.length && t(window).width() < 768) {
                    var r = a.offset().top + a.outerHeight(),
                        n = t(".footer-mobile"),
                        i = 0,
                        s = n.length > 0,
                        c = function () {
                            var e = t(window).scrollTop(),
                                a = t(window).height(),
                                c = t(document).height();
                            i = s ? n.offset().top - n.height() : e, e + a === c || r > e || e > i ? o.removeClass("sticky-atc-shown") : r < e && e + a !== c && o.addClass("sticky-atc-shown")
                        };
                    c(), t(window).scroll(c)
                }
            }, t((function () {
                e.init(), window.onBeforeChangeSwatches = function (e, t) {
                    var a = t.closest(".product-details"),
                        o = a.find(".cart-form");
                    a.find(".error-message").hide(), a.find(".success-message").hide(), a.find(".number-items-available").html("").hide();
                    var r = o.find("button[type=submit]");
                    r.addClass("loading"), e && e.attributes && r.prop("disabled", !0)
                }, window.onChangeSwatchesSuccess = function (a, o) {
                    var r = o.closest(".product-details"),
                        n = r.find(".cart-form"),
                        i = t(".footer-cart-form");
                    if (r.find(".error-message").hide(), r.find(".success-message").hide(), a) {
                        var s = n.find("button[type=submit]");
                        if (s.removeClass("loading"), a.error) s.prop("disabled", !0), r.find(".number-items-available").html('<span class="text-danger">(' + a.message + ")</span>").show(), n.find(".hidden-product-id").val(""), i.find(".hidden-product-id").val("");
                        else {
                            var c = a.data,
                                l = t(document).find(".js-product-content"),
                                d = l.find(".product-price-sale"),
                                ptn = l.find(".product-title-name"),
                                ldpq = l.find(".label-details-product-quantity"),
                                u = l.find(".product-price-original");

                            document.title = c.title
                            ptn.text(c.title)
                            if (c.with_storehouse_management) {
                                ldpq.text(c.quantity + ' ' + ldpq.data('translate').replaceAll('0 ', ''))
                                t('.input-text.qty').attr('max', c.quantity)
                            } else {
                                ldpq.text(ldpq.data('warehouse'))
                                t('.input-text.qty').attr('max', 1000)
                            }
                            t('.input-text.qty').val(1)
                            c.sale_price !== c.price ? (d.removeClass("d-none"), u.addClass("d-none")) : (d.addClass("d-none"), u.removeClass("d-none")), d.find("ins .amount").text(c.display_sale_price), d.find("del .amount").text(c.display_price), u.find(".amount").text(c.display_sale_price), c.sku ? (r.find(".meta-sku .meta-value").text(c.sku), r.find(".meta-sku").removeClass("d-none")) : r.find(".meta-sku").addClass("d-none"), n.find(".hidden-product-id").val(c.id), i.find(".hidden-product-id").val(c.id), s.prop("disabled", !1), c.error_message ? (s.prop("disabled", !0), r.find(".number-items-available").html('<span class="text-danger">(' + c.error_message + ")</span>").show()) : c.success_message ? r.find(".number-items-available").html(a.data.stock_status_html).show() : r.find(".number-items-available").html("").hide();
                            var p = c.unavailable_attribute_ids || [];
                            r.find(".attribute-swatch-item").removeClass("pe-none"), r.find(".product-filter-item option").prop("disabled", !1), p && p.length && p.map((function (e) {
                                var t = r.find('.attribute-swatch-item[data-id="' + e + '"]');
                                t.length ? (t.addClass("pe-none"), t.find("input").prop("checked", !1)) : (t = r.find('.product-filter-item option[data-id="' + e + '"]')).length && t.prop("disabled", "disabled").prop("selected", !1)
                            }));
                            var f = r.closest(".product-detail-container").find(".product-gallery");
                            c.image_with_sizes.origin.length || c.image_with_sizes.origin.push(siteConfig.img_placeholder), c.image_with_sizes.thumb.length || c.image_with_sizes.thumb.push(siteConfig.img_placeholder);
                            var m = "";
                            c.image_with_sizes.origin.forEach((function (e) {
                                m += '<div class="product-gallery__image item">\n                                <a class="img-fluid-eq" href="'.concat(e, '">\n                                    <div class="img-fluid-eq__dummy"></div>\n                                    <div class="img-fluid-eq__wrap">\n                                        <img class="mx-auto" alt="').concat(c.name, '" title="').concat(c.name, '" src="').concat(siteConfig.img_placeholder ? siteConfig.img_placeholder : e, '" data-lazy="').concat(e, '">\n                                    </div>\n                                </a>\n                            </div>')
                            })), f.find(".product-gallery__wrapper").slick("unslick").html(m);
                            var h = "";
                            c.image_with_sizes.thumb.forEach((function (e) {
                                h += '<div class="item">\n                            <div class="border p-1 m-1">\n                                <img class="lazyload" alt="'.concat(c.name, '" title="').concat(c.name, '" src="').concat(siteConfig.img_placeholder ? siteConfig.img_placeholder : e, '" data-src="').concat(e, '" data-lazy="').concat(e, '">\n                            </div>\n                        </div>')
                            })), f.find(".product-gallery__variants").slick("unslick").html(h), e.productGallery(!0, f), e.lightBox()
                        }
                    }
                }, jQuery().mCustomScrollbar && t(".ps-custom-scrollbar").mCustomScrollbar({
                    theme: "dark",
                    scrollInertia: 0
                }), t(document).on("click", ".toggle-show-more", (function (e) {
                    e.preventDefault(), t("#store-short-description").fadeOut(), t(this).hide(), t("#store-content").slideDown(500), t(".toggle-show-less").show()
                })), t(document).on("click", ".toggle-show-less", (function (e) {
                    e.preventDefault(), t(this).hide(), t("#store-content").slideUp(500), t("#store-short-description").fadeIn(), t(".toggle-show-more").show()
                }));
                var a = function () {
                    t(".page-breadcrumbs ol li").each((function () {
                        var e = t(this);
                        e.is(":first-child") || e.is(":nth-child(2)") || e.is(":last-child") || (e.is(":nth-child(3)") ? (e.find("a").hide(), e.find(".extra-breadcrumb-name").text("...").show()) : e.find("a").closest("li").hide())
                    }))
                };
                t(window).width() < 768 && a(), t(window).on("resize", (function () {
                    // a()
                    t(".page-breadcrumbs ol li").each((function () {
                        var e = t(this);
                        e.is(":first-child") || e.is(":nth-child(2)") || e.is(":last-child") || (e.is(":nth-child(3)") ? (e.find("a").hide(), e.find(".extra-breadcrumb-name").text("...").show()) : e.find("a").closest("li").hide())
                    }))
                })), t(".product-entry-meta .anchor-link").on("click", (function (e) {
                    e.preventDefault();
                    var a = t(this).attr("href");
                    t("#product-detail-tabs a").removeClass("active"), t(a).addClass("active"), t("#product-detail-tabs-content > .tab-pane").removeClass("active show"), t(t(a).attr("href")).addClass("active show"), t("html, body").animate({
                        scrollTop: t(a).offset().top - t(".header--product .navigation").height() - 250 + "px"
                    }, 0)
                })), t(document).on("click", "#sticky-add-to-cart .add-to-cart-button", (function (e) {
                    e.preventDefault(), e.stopPropagation();
                    var a = t(e.currentTarget);
                    a.addClass("button-loading"), setTimeout((function () {
                        var e = ".js-product-content .cart-form button[name=" + a.prop("name") + "].add-to-cart-button";
                        t(document).find(e).trigger("click"), a.removeClass("button-loading")
                    }), 200)
                })), e.calcSubTotal = function () {
                    var subTotal = 0
                    var couponDiscountAmount = 0
                    var selectedVal = ''

                    const n = t('.ajax-count-total').data('url')
                    var ajaxRequests = [];

                    t('.e1chjk5t0').each(function (e) {
                        if (t(this).is(':checked')) {
                            selectedVal = t(this).val() + ',' + selectedVal
                            var ajaxRequest = t.ajax({
                                type: "POST",
                                data: {
                                    '_exp': t(this).val()
                                },
                                url: n,
                                beforeSend: function () {
                                    a = true;
                                },
                                success: function (t) {
                                    // console.log(t);
                                    subTotal += t.data.subTotal;
                                    couponDiscountAmount += t.data.couponDiscountAmount;
                                },
                                error: function (t) {
                                    // console.log(t);
                                    e.handleError(t);
                                },
                                complete: function () {
                                    a = false;
                                }
                            });

                            ajaxRequests.push(ajaxRequest);
                        }
                    });

                    $.when.apply($, ajaxRequests).then(function () {
                        const total = (couponDiscountAmount > subTotal) ? 0 : subTotal - couponDiscountAmount;
                        // console.log(total)
                        t('input[name="selected_cart"]').val(selectedVal)
                        t('.price-current__sub-total').text(t('.price-current__sub-total').data('format').replaceAll('0', '') + e.formatRupiah(subTotal.toString()));
                        t('.price-current__total').text(t('.price-current__total').data('format').replaceAll('0', '') + e.formatRupiah(total.toString()))
                        t('.price-current__sub-coupon').text(t('.price-current__sub-coupon').data('format').replaceAll('0', '') + e.formatRupiah(couponDiscountAmount.toString()));
                    });
                }, t(document).on('change', '.e1chjk5t0-all', function () {
                    const s = t(this).is(':checked')
                    if (s) {
                        t('.e1chjk5t0').prop('checked', true)
                        e.calcSubTotal()
                    } else {
                        t('.e1chjk5t0').prop('checked', false)
                        e.calcSubTotal()
                    }
                }), t(document).on('change', '.e1chjk5t0', function () {
                    var allChecked = true;

                    t('.e1chjk5t0').each(function () {
                        if (!t(this).is(':checked')) {
                            allChecked = false;
                            return false;
                        }
                    });
                    e.calcSubTotal()
                    t('.e1chjk5t0-all').prop('checked', allChecked);
                }), e.formatRupiah = function (angka) {
                    var number_string = angka.replace(/[^,\d]/g, '').toString(),
                        split = number_string.split(','),
                        sisa = split[0].length % 3,
                        rupiah = split[0].substr(0, sisa),
                        ribuan = split[0].substr(sisa).match(/\d{3}/gi),
                        separator = '';

                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }

                    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                    return rupiah;
                },
                    // t(document).ready((function () {
                    // // e.calcSubTotal()
                    // }))
                    e.showError = function (e) {
                        this.showNotice("error", e)
                    }, e.showSuccess = function (e) {
                        this.showNotice("success", e)
                    }, e.handleError = function (t) {
                        void 0 !== t.errors && t.errors.length ? e.handleValidationError(t.errors) : void 0 !== t.responseJSON ? void 0 !== t.responseJSON.errors ? 422 === t.status && e.handleValidationError(t.responseJSON.errors) : void 0 !== t.responseJSON.message ? e.showError(t.responseJSON.message) : e.showError(t.responseJSON.join(", ").join(", ")) : e.showError(t.statusText)
                    }
            }))
        }(jQuery)
})();
