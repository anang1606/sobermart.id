    <footer id="footer">
        <div class="footer-info border-top">
            <div class="container-xxxl py-3">
                {!! dynamic_sidebar('pre_footer_sidebar') !!}
            </div>
        </div>
        @if (Widget::group('footer_sidebar')->getWidgets())
            <div class="footer-widgets">
                <div class="container-xxxl">
                    <div class="row border-top py-5">
                        {!! dynamic_sidebar('footer_sidebar') !!}
                    </div>
                </div>
            </div>
        @endif
        {{--  @if (Widget::group('bottom_footer_sidebar')->getWidgets())
            <div class="container-xxxl">
                <div class="footer__links" id="footer-links">
                    {!! dynamic_sidebar('bottom_footer_sidebar') !!}
                </div>
            </div>
        @endif  --}}
        <div class="container-xxxl">
            <div class="row border-top py-4">
                <div class="col-lg-3 col-md-4 py-3">
                    <div class="copyright d-flex justify-content-center justify-content-md-start">
                        <span>{{ theme_option('copyright') }}</span>
                    </div>
                </div>
                <div class="col-lg-6 col-md-4 py-3">
                    @if (theme_option('payment_methods_image'))
                        <div class="footer-payments d-flex justify-content-center">
                            @if (theme_option('payment_methods_link'))
                                <a href="{{ url(theme_option('payment_methods_link')) }}" target="_blank">
                            @endif

                            <img class="lazyload"
                                data-src="{{ RvMedia::getImageUrl(theme_option('payment_methods_image')) }}"
                                alt="footer-payments">

                            @if (theme_option('payment_methods_link'))
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="col-lg-3 col-md-4 py-3">
                    <div class="footer-socials d-flex justify-content-md-end justify-content-center">
                        @if (theme_option('social_links'))
                            <p class="me-3 mb-0">{{ __('Stay connected:') }}</p>
                            <div class="footer-socials-container">
                                <ul class="ps-0 mb-0">
                                    @foreach (json_decode(theme_option('social_links'), true) as $socialLink)
                                        @if (count($socialLink) == 3)
                                            <li class="d-inline-block ps-1 my-1">
                                                <a target="_blank" href="{{ Arr::get($socialLink[2], 'value') }}"
                                                    title="{{ Arr::get($socialLink[0], 'value') }}">
                                                    <img class="lazyload"
                                                        data-src="{{ RvMedia::getImageUrl(Arr::get($socialLink[1], 'value')) }}"
                                                        alt="{{ Arr::get($socialLink[0], 'value') }}" />
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @if (is_plugin_active('ecommerce'))
        <div class="panel--sidebar category-panel" id="navigation-mobile">
            <div class="panel__header">
                <span class="svg-icon close-toggle--sidebar">
                    <svg>
                        <use href="#svg-icon-arrow-left" xlink:href="#svg-icon-arrow-left"></use>
                    </svg>
                </span>
                <h3>{{ __('Categories') }}</h3>
            </div>
            <div class="panel__content">
                <ul class="menu--mobile">
                    {!! Theme::partial('product-categories-dropdown-mobile', compact('categories')) !!}
                </ul>
            </div>
        </div>
    @endif

    <div class="panel--sidebar" id="menu-mobile">
        <div class="panel__header">
            <span class="svg-icon close-toggle--sidebar">
                <svg>
                    <use href="#svg-icon-arrow-left" xlink:href="#svg-icon-arrow-left"></use>
                </svg>
            </span>
            <h3>{{ __('Menu') }}</h3>
        </div>
        <div class="panel__content">
            {!! Menu::renderMenuLocation('main-menu', [
                'view' => 'menu',
                'options' => ['class' => 'menu--mobile'],
            ]) !!}

            {!! Menu::renderMenuLocation('header-navigation', [
                'view' => 'menu',
                'options' => ['class' => 'menu--mobile'],
            ]) !!}

            <ul class="menu--mobile">

                @if (is_plugin_active('ecommerce'))
                    @if (EcommerceHelper::isCompareEnabled())
                        <li><a href="{{ route('public.compare') }}"><span>{{ __('Compare') }}</span></a></li>
                    @endif

                    @php $currencies = get_all_currencies(); @endphp
                    @if (count($currencies) > 1)
                        <li class="menu-item-has-children">
                            <a href="#">
                                <span>{{ get_application_currency()->title }}</span>
                                <span class="sub-toggle">
                                    <span class="svg-icon">
                                        <svg>
                                            <use href="#svg-icon-chevron-down" xlink:href="#svg-icon-chevron-down">
                                            </use>
                                        </svg>
                                    </span>
                                </span>
                            </a>
                            <ul class="sub-menu">
                                @foreach ($currencies as $currency)
                                    @if ($currency->id !== get_application_currency_id())
                                        <li><a
                                                href="{{ route('public.change-currency', $currency->title) }}"><span>{{ $currency->title }}</span></a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </li>
                    @endif
                @endif
                @if (is_plugin_active('language'))
                    @php
                        $supportedLocales = Language::getSupportedLocales();
                    @endphp

                    @if ($supportedLocales && count($supportedLocales) > 1)
                        @php
                            $languageDisplay = setting('language_display', 'all');
                        @endphp
                        <li class="menu-item-has-children">
                            <a href="#">
                                @if ($languageDisplay == 'all' || $languageDisplay == 'flag')
                                    {!! language_flag(Language::getCurrentLocaleFlag(), Language::getCurrentLocaleName()) !!}
                                @endif
                                @if ($languageDisplay == 'all' || $languageDisplay == 'name')
                                    {{ Language::getCurrentLocaleName() }}
                                @endif
                                <span class="sub-toggle">
                                    <span class="svg-icon">
                                        <svg>
                                            <use href="#svg-icon-chevron-down" xlink:href="#svg-icon-chevron-down">
                                            </use>
                                        </svg>
                                    </span>
                                </span>
                            </a>
                            <ul class="sub-menu">
                                @foreach ($supportedLocales as $localeCode => $properties)
                                    @if ($localeCode != Language::getCurrentLocale())
                                        <li>
                                            <a
                                                href="{{ Language::getSwitcherUrl($localeCode, $properties['lang_code']) }}">
                                                @if ($languageDisplay == 'all' || $languageDisplay == 'flag')
                                                    {!! language_flag($properties['lang_flag'], $properties['lang_name']) !!}
                                                @endif
                                                @if ($languageDisplay == 'all' || $languageDisplay == 'name')
                                                    <span>{{ $properties['lang_name'] }}</span>
                                                @endif
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </li>
                    @endif
                @endif
            </ul>
        </div>
    </div>
    <div class="panel--sidebar panel--sidebar__right" id="search-mobile">
        <div class="panel__header">
            @if (is_plugin_active('ecommerce'))
                <form class="form--quick-search w-100" action="{{ route('public.products') }}"
                    data-ajax-url="{{ route('public.ajax.search-products') }}" method="get">
                    <div class="search-inner-content">
                        <div class="text-search">
                            <div class="search-wrapper">
                                <input class="search-field input-search-product" name="q" type="text"
                                    placeholder="{{ __('Search something...') }}" autocomplete="off">
                                <button class="btn" type="submit">
                                    <span class="svg-icon">
                                        <svg>
                                            <use href="#svg-icon-search" xlink:href="#svg-icon-search"></use>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                            <a class="close-search-panel close-toggle--sidebar" href="#">
                                <span class="svg-icon">
                                    <svg>
                                        <use href="#svg-icon-times" xlink:href="#svg-icon-times"></use>
                                    </svg>
                                </span>
                            </a>
                        </div>
                    </div>
                    <div class="panel--search-result"></div>
                </form>
            @endif
        </div>
    </div>
    <div class="footer-mobile">
        <ul class="menu--footer">
            <li>
                <a href="{{ route('public.index') }}">
                    <i class="icon-home3"></i>
                    <span>{{ __('Home') }}</span>
                </a>
            </li>
            @if (is_plugin_active('ecommerce'))
                <li>
                    <a class="toggle--sidebar" href="#navigation-mobile">
                        <i class="icon-list"></i>
                        <span>{{ __('Category') }}</span>
                    </a>
                </li>
                @if (EcommerceHelper::isCartEnabled())
                    <li>
                        <a class="toggle--sidebar" href="#cart-mobile">
                            <i class="icon-cart">
                                <span class="cart-counter">{{ Cart::instance('cart')->count() }}</span>
                            </i>
                            <span>{{ __('Cart') }}</span>
                        </a>
                    </li>
                @endif
                @if (EcommerceHelper::isWishlistEnabled())
                    <li>
                        <a href="{{ route('public.wishlist') }}">
                            <i class="icon-heart"></i>
                            <span>{{ __('Wishlist') }}</span>
                        </a>
                    </li>
                @endif
                <li>
                    <a href="{{ route('customer.overview') }}">
                        <i class="icon-user"></i>
                        <span>{{ __('Account') }}</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
    @if (is_plugin_active('ecommerce'))
        {!! Theme::partial('ecommerce.quick-view-modal') !!}
    @endif
    {!! Theme::partial('toast') !!}

    <div class="panel-overlay-layer"></div>
    <div id="back2top">
        <span class="svg-icon">
            <svg>
                <use href="#svg-icon-arrow-up" xlink:href="#svg-icon-arrow-up"></use>
            </svg>
        </span>
    </div>

    <script>
        'use strict';

        window.trans = {
            "View All": "{{ __('View All') }}",
            "No reviews!": "{{ __('No reviews!') }}"
        };

        window.siteConfig = {
            "url": "{{ route('public.index') }}",
            "img_placeholder": "{{ theme_option('lazy_load_image_enabled', 'yes') == 'yes' ? image_placeholder() : null }}",
            "countdown_text": {
                "days": "{{ __('days') }}",
                "hours": "{{ __('hours') }}",
                "minutes": "{{ __('mins') }}",
                "seconds": "{{ __('secs') }}"
            }
        };

        @if (is_plugin_active('ecommerce') && EcommerceHelper::isCartEnabled())
            siteConfig.ajaxCart = "{{ route('public.ajax.cart') }}";
            siteConfig.cartUrl = "{{ route('public.cart') }}";
        @endif
    </script>

    {!! Theme::footer() !!}

    @if (session()->has('success_msg') ||
            session()->has('error_msg') ||
            (isset($errors) && $errors->count() > 0) ||
            isset($error_msg))
        <script type="text/javascript">
            window.onload = function() {
                @if (session()->has('success_msg'))
                    MartApp.showSuccess('{{ session('success_msg') }}');
                @endif

                @if (session()->has('error_msg'))
                    MartApp.showError('{{ session('error_msg') }}');
                @endif

                @if (isset($error_msg))
                    MartApp.showError('{{ $error_msg }}');
                @endif

                @if (isset($errors))
                    @foreach ($errors->all() as $error)
                        MartApp.showError('{!! BaseHelper::clean($error) !!}');
                    @endforeach
                @endif
            };
        </script>
    @endif
    <script src="{{ Theme::asset()->url('js/bootstrap-show-password.js') }}"></script>
    <script>
        const is_member = document.querySelectorAll('.show-if-members')
        if (is_member.length > 0) {
            $(document).on('click', 'input[name=is_vendor]', function(event) {
                1 == $(this).val() ?
                    (
                        $(".show-if-vendor").slideDown().show(),
                        $(".show-if-members").slideUp()
                    ) :
                    (
                        $(".show-if-vendor").slideUp(500),
                        $(".show-if-members").slideDown().show(),
                        $(this).closest("form").find("button[type=submit]").prop("disabled", !1)
                    )
            })
        }
        const check_referral = () => {
            const code = document.getElementById('referral_code');
            const button_code = document.getElementById('button_code');
            $.ajax({
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    code: code.value
                },
                url: '{{ route('customer.checkRefferal') }}',
                success: function(succ) {
                    if (succ.error) {
                        MartApp.showError('Referral code not found!!');
                    }
                    $('#paket-select').html(succ.data)
                }
            })
        }
    </script>
    <script>
        $(document).on('click', '.password_confirmation', function() {
            const input_pass_confirm = document.getElementById('password_confirmation')
            if (input_pass_confirm.type === 'password') {
                input_pass_confirm.type = 'text'
                $(this).find('i').removeClass('fa fa-eye').addClass('fa fa-eye-slash')
            } else {
                input_pass_confirm.type = 'password'
                $(this).find('i').removeClass('fa fa-eye-slash').addClass('fa fa-eye')
            }
        })

        const validatePassword = (password) => {
            if (password.length < 8) return "Password should be at least 8 characters long.";
            if (!/[A-Z]/.test(password)) return "Password should contain at least one uppercase letter.";
            if ((!/[0-9]/.test(password))) return "Password should contain at least one digit.";

            return true
        }

        $(document).on('keyup', '.password-check', function() {
            const pass = $(this).val();
            const verifiedPass = $('.verified-pass').val();
            const invalidElem = $(this).closest('.input-group').find('.invalid-feedback');

            const validationError = validatePassword(pass);
            if (validationError !== true) {
                $(this).addClass('is-invalid');
                invalidElem.text(validationError);
            } else if (pass !== verifiedPass) {
                $(this).addClass('is-invalid');
                invalidElem.text("Passwords do not match.");
            } else {
                $(this).removeClass('is-invalid');
                invalidElem.text('');
            }
        });

        $(document).on('click', '.DQXPDCmiQw', function() {
            $('.DQXPDCmiQw.checked').removeClass('checked')
            $(this).addClass('checked')
        })
    </script>
    <script>
        $(document).on('click', '.css-vk082c', function() {
            if ($('.header-js-handler').hasClass('is-openHeader')) {
                removeCategoryPanel()
            } else {
                $('.e1429ojz2').removeClass('css-hidden')
                setTimeout(function() {
                    $('.edxse4c3').removeClass('css-hidden')
                    $('.header-js-handler').addClass('header--sticky')
                    $('.header-js-handler').addClass('is-openHeader')
                    $(document.body).css('overflow', 'hidden')
                    var firstElement = $('.css-me46ht').first()
                    $('.css-me46ht').removeAttr('style');
                    firstElement.css({
                        'background-color': 'rgb(243, 244, 245)',
                        'color': 'rgba(49, 53, 59, 0.96)'
                    });
                    ajaxLoadCategory(firstElement.data('testid'))
                }, 350)
            }
        })

        const removeCategoryPanel = () => {
            if (!$('.e1429ojz2').is(':focus')) {
                if ($('.header-js-handler').hasClass('header--sticky')) {
                    $('.header-js-handler').removeClass('header--sticky is-openHeader')
                } else {
                    $('.header-js-handler').removeClass('is-openHeader')
                }
                $('.e1429ojz2').addClass('css-hidden');
                $('.edxse4c3').addClass('css-hidden')
                $(document.body).css('overflow', 'auto')
            }
        }

        $(document).on('click', '.edxse4c3', function() {
            removeCategoryPanel()
        });

        $('.e1429ojz2').on('focus', function() {
            $(this).removeClass('css-hidden');
        });

        $(document).on('mouseenter', '.css-me46ht', function() {
            $('.css-me46ht').removeAttr('style');
            $(this).css({
                'background-color': 'rgb(243, 244, 245)',
                'color': 'rgba(49, 53, 59, 0.96)'
            });
            ajaxLoadCategory($(this).data('testid'))
        })
        const ajaxLoadCategory = (params) => {
            $.ajax({
                url: "{{ route('public.ajax.get-category-level-1') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    __ex_cat: params
                },
                success: function(response) {
                    const result = response.data
                    $('.css-11p7ov6').html(result)
                }
            })
        }
    </script>
    @php
        use Botble\Slug\Models\Slug;
        use Botble\Marketplace\Models\Store;
        $currentRouteName = Route::currentRouteName();
    @endphp
    @if (auth('customer')->check() && $currentRouteName !== 'customer.bantuan')
        <button class="css-ehm296-unf-btn">
            <div class="chatImgWrapper" id="chatImgWrapper">

            </div>
            Chat
        </button>
    @endif
    @if (auth('customer')->check())
        <?php
        $url = 'https://chat.sobermart.id/?nref=sm&key=';

        $slug = request()->route('slug');

        $url .= auth('customer')->user()->id;
        $url .= '&type=customer&logo=' . RvMedia::getImageUrl(theme_option('logo'));

        if ($slug) {
            $slug_type = '';
            $getSlug = Slug::where('key', $slug)->first();
            $slug = $getSlug->reference_id . '-' . $getSlug->prefix;

            $url .= '&store=' . $slug;
        } else {
            $url .= '&store=none';
        }
        ?>
        <div class="css-1lya59d">
            <iframe id="frame-chat" src="{{ $url }}" width="100%" height="100%"></iframe>
        </div>
        <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
        <script src="{{ asset('themes/farmart/js/chat.js') }}"></script>
    @endif
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="{{ asset('themes/farmart/js/customer-payment.js') }}"></script>
    <script src="{{ asset('themes/farmart/js/buy-now.js') }}"></script>
    <script>
        function copyToClipboard(text) {
            var textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            return Toastify({
                text: 'Code copy to clipboard ' + text,
                duration: 3000,
                close: false,
                gravity: "top", // `top` or `bottom`
                position: "center", // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                style: {
                    background: "black",
                },
                onClick: function() {}, // Callback after click
            }).showToast();
        }
        @if (Session::get('message-member'))
            Toastify({
                text: "{{ Session::get('message-member') }}",
                duration: 3000,
                close: false,
                gravity: "top",
                position: "center",
                stopOnFocus: true,
                style: {
                    background: "black",
                },
                onClick: function() {},
            }).showToast();
        @endif
    </script>
    @if ($currentRouteName === 'customer.member-list')
        <script>
            $(document).ready(function() {
                $("#withdrawal-request").hide();
                $("#withdrawal-data").hide();

                $(document).on('click', '.withdrawal-btn', function() {
                    if ($(this).text() === 'Details Member') {
                        $(this).text('Withdrawals')
                        $("#withdrawal-data").hide();
                        $("#details-member").show();
                        $("#withdrawal-request").hide();
                    } else {
                        $(this).text('Details Member')
                        $("#withdrawal-data").show();
                        $("#details-member").hide();
                    }
                })

                $(document).on('click', '.withdrawal-request', function() {
                    $("#withdrawal-request").show();
                })
                $(document).on('click', '.withdrawal-data', function() {
                    $("#withdrawal-request").hide();
                })
            })
        </script>
    @endif
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"
        integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(".custom-carousel").owlCarousel({
            autoWidth: true,
            loop: true,
            stagePadding: 50,
            margin: 10,
            nav: false,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 3
                },
                1000: {
                    items: 5
                }
            }
        });

        const check_referral_join = () => {
            const code = document.getElementById('referral_code');
            const button_code = document.getElementById('button_code');
            $.ajax({
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    code: code.value
                },
                url: '{{ route('customer.paket.get_referral') }}',
                success: function(succ) {
                    if (succ.error) {
                        MartApp.showError('Referral code not found!!');
                    }
                    const loadCarousel = document.getElementById('load-carousel')
                    loadCarousel.innerHTML = `
                        <div class="owl-carousel owl-theme custom-carousel row">
                            ${succ.data}
                        </div>
                    `
                    $(".custom-carousel").owlCarousel({
                        autoWidth: true,
                        loop: true,
                        stagePadding: 50,
                        margin: 10,
                        nav: false,
                        responsive: {
                            0: {
                                items: 1
                            },
                            600: {
                                items: 3
                            },
                            1000: {
                                items: 5
                            }
                        }
                    });
                }
            })
        };

        setTimeout(function() {
            var currentUrl = window.location.href;
            var urlObject = new URL(currentUrl);
            var tempIdValue = urlObject.searchParams.get("temp_id");
            if (tempIdValue) {
                var elementId = 'small-' + tempIdValue;
                var element = document.getElementById(elementId);
                if (element) {
                    element.checked = true;
                }
            }
        }, 250)
    </script>
    <script>
        setTimeout(() => {
            $('[data-bg]').css({
                'background-image': 'none'
            })
        }, 250);
        console.error = () => {};
    </script>
    @if ($currentRouteName === 'public.product-category' || $currentRouteName === 'public.products')
        <script>
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
        </script>
    @endif
    @if ($currentRouteName === 'public.index')
        <div id="app">
            <banner-popup ads-url="{{ route('public.fetchBannerPopups') }}"></banner-popup>
        </div>
        <script src="{{ asset('vendor/core/core/base/js/vue-app.js?v=1.10.0') }}"></script>
        <script src="{{ asset('vendor/core/plugins/popup-ads/js/popup-ads.js') }}"></script>
    @endif
    @if ($currentRouteName === 'customer.ahli-waris.create' || $currentRouteName === 'customer.ahli-waris.edit')
    <script>
        $(document).on('change','.same-ktp',function(){
            if($(this).prop('checked')){
                $('._tinggal').each(function () {
                    $(this).prop('required', false);
                });
                $('#address_life').hide()
            }else{
                $('#address_life').show()
                $('._tinggal').each(function () {
                    $(this).prop('required', true);
                });
            }
        })
    </script>
    @endif
    </body>

    </html>
