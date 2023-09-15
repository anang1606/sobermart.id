@php Theme::layout('full-width'); @endphp
{!! Theme::partial('page-header', ['withTitle' => false, 'size' => 'xl']) !!}
<div class="container">
    <div class="row customer-auth-page py-5 mt-5 justify-content-center">
        <div class="col-sm-9 col-md-6 col-lg-5 col-xl-4">
            <div class="customer-auth-form bg-light pt-1 py-3 px-4">
                <nav>
                    <div class="nav nav-tabs">
                        <h1 class="nav-link fs-5 fw-bold">{{ __('Register An Account') }}</h1>
                    </div>
                </nav>
                <div class="tab-content my-3">
                    <div class="tab-pane fade pt-4 show active" id="nav-register-content" role="tabpanel"
                        aria-labelledby="nav-profile-tab">
                        <form method="POST" action="{{ route('customer.register.post') }}">
                            @csrf
                            <div class="input-group mb-3">
                                <input class="form-control @if ($errors->has('name')) is-invalid @endif"
                                    name="name" id="name-register" type="text" value="{{ old('name') }}"
                                    placeholder="{{ __('Your Name') }}">
                                @if ($errors->has('name'))
                                    <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                                @endif
                            </div>
                            <div class="input-group mb-3">
                                <input class="form-control @if ($errors->has('email')) is-invalid @endif"
                                    type="email" required="required" placeholder="{{ __('Email Address') }}"
                                    name="email" autocomplete="email" value="{{ old('email') }}">
                                @if ($errors->has('email'))
                                    <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                                @endif
                            </div>
                            <div class="input-group mb-3 input-group-with-text">
                                <input
                                    class="form-control password-check verified-pass @if ($errors->has('password')) is-invalid @endif"
                                    type="password" name="password" id="password" data-toggle="password"
                                    placeholder="{{ __('Password') }}" aria-label="{{ __('Password') }}"
                                    autocomplete="password">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                                <div class="invalid-feedback"></div>
                                @if ($errors->has('password'))
                                    <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                                @endif
                            </div>
                            <div class="input-group mb-3 input-group-with-text">
                                <input
                                    class="form-control password-check @if ($errors->has('password_confirmation')) is-invalid @endif"
                                    type="password" name="password_confirmation" id="password_confirmation"
                                    data-toggle="password_confirmation" placeholder="{{ __('Password confirmation') }}"
                                    aria-label="{{ __('Password confirmation') }}"
                                    autocomplete="password_confirmation">
                                <div class="input-group-append">
                                    <span class="input-group-text input-password-hide password_confirmation"
                                        style="cursor: pointer;">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                                <div class="invalid-feedback"></div>
                                @if ($errors->has('password_confirmation'))
                                    <div class="invalid-feedback">{{ $errors->first('password_confirmation') }}</div>
                                @endif
                            </div>
                            <div class="show-if-members"
                                @if (old('is_member') === 1) style="display: block" @endif>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group mb-3">
                                            <div class="input-group">
                                                <input id="referral_code"
                                                    class="form-control @if ($errors->has('referral_code')) is-invalid @endif"
                                                    type="number" placeholder="Referral Code"
                                                    aria-label="{{ __('Password') }}" name="referral_code">
                                                <button class="btn btn-outline-secondary" type="button"
                                                    onclick="check_referral()" id="button_code">
                                                    Check
                                                </button>
                                            </div>
                                            @if ($errors->has('referral_code'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('referral_code') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row" id="paket-select">
                                            @foreach ($memberPaket as $paket)
                                                <div class="col-md-6 mb-3">
                                                    <div class="DjhkItMLcf" style="height: 100%">
                                                        <label for="small-{{ $paket->id }}" class="DQXPDCmiQw"
                                                            style="height: 100%">
                                                            <div class="pAEwcwpIAP">
                                                                <div class="LFNFvicbva">
                                                                    <div class="vhrStrXljR"></div>
                                                                    <div class="jppTUaEgYy">
                                                                        <h5 class="zqYgwSlvmX">{{ $paket->name }}</h5>
                                                                        <span class="dICQuslKpk d-flex flex-column">
                                                                            <span>{{ format_price($paket->nominal) }}</span>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <input id="small-{{ $paket->id }}" type="radio"
                                                                    name="paket" value="{{ $paket->id }}"
                                                                    class="haHukqnIXN">
                                                            </div>
                                                            {{-- <div class="line-hr"></div> --}}
                                                            {{-- <div class="relqpLAcxs oUYXsUylHm">
                                                                <ul class="URAXAoOcDM">
                                                                    @foreach ($paket->details as $detail)
                                                                        <li class="AGZeUBWgnP">
                                                                            <div class="dec-icons"
                                                                                style="width: 15px;height: 15px;">
                                                                            </div>
                                                                            <div class="dec-details">
                                                                                {{ $detail->content }}
                                                                            </div>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div> --}}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <div class="col-md-12">
                                                @if ($errors->has('paket'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('paket') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (is_plugin_active('marketplace'))
                                <div class="show-if-vendor"
                                    @if (old('is_vendor') != 1) style="display: none" @endif>
                                    @include(Theme::getThemeNamespace() .
                                            '::views.marketplace.includes.become-vendor-form')
                                </div>
                                <div class="vendor-customer-registration">
                                    <div class="form-check my-1">
                                        <input class="form-check-input" name="is_vendor" value="0"
                                            id="customer-role-register" type="radio"
                                            @if (old('is_vendor') != 1) checked="checked" @endif>
                                        <label class="form-check-label"
                                            for="customer-role-register">{{ __('I am a customer') }}</label>
                                    </div>
                                </div>
                            @endif
                            <div class="form-group">
                                <p>{{ __('Your personal data will be used to support your experience throughout this website, to manage access to your account, and for other purposes described in our privacy policy.') }}
                                </p>
                            </div>
                            <div class="form-check mb-3">
                                <input type="hidden" name="agree_terms_and_policy" value="0">
                                <input class="form-check-input" type="checkbox" name="agree_terms_and_policy"
                                    id="agree-terms-and-policy" value="1"
                                    @if (old('agree_terms_and_policy') == 1) checked @endif>
                                <label for="agree-terms-and-policy">{{ __('I agree to terms & Policy.') }}</label>
                                @if ($errors->has('agree_terms_and_policy'))
                                    <div class="mt-1">
                                        <span
                                            class="text-danger small">{{ $errors->first('agree_terms_and_policy') }}</span>
                                    </div>
                                @endif
                            </div>

                            @if (is_plugin_active('captcha') &&
                                    setting('enable_captcha') &&
                                    get_ecommerce_setting('enable_recaptcha_in_register_page', 0))
                                <div class="form-group mb-3">
                                    {!! Captcha::display() !!}
                                </div>
                            @endif
                            <div class="d-grid">
                                <button class="btn btn-primary" type="submit">{{ __('Register') }}</button>
                            </div>

                            <div class="mt-3">
                                <p class="text-center">{{ __('Already have an account?') }} <a
                                        href="{{ route('customer.login') }}"
                                        class="d-inline-block text-primary">{{ __('Log in') }}</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bg-light pt-1 px-4 pb-3">
                {!! apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\Ecommerce\Models\Customer::class) !!}
            </div>
        </div>
    </div>
</div>
