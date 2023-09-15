@php
    $logo = theme_option('logo_in_the_checkout_page') ?: theme_option('logo');
@endphp
<style>
    :root {
        --hue: 223;
        --bg: hsl(var(--hue),10%,90%);
        --fg: hsl(var(--hue),10%,10%);
        --primary: hsl(var(--hue),90%,55%);
        --trans-dur: 0.3s;
    }
    .css-4xk3hb {
        min-height: 0px;
        position: relative;
    }

    .css-ve9dke {
        display: block;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding-right: 20px;
        padding-left: 20px;
        background: white;
        border-bottom: 1px solid var(--N100, #DBDEE2);
        box-sizing: border-box;
        min-width: 1024px;
        padding-top: 20px;
    }

    .css-xz6h1x {
        width: 100%;
        max-width: 1080px;
        min-width: 1024px;
        margin: auto;
    }

    .css-urtwg0 {
        width: 157px;
        height: 60px;
    }

    .css-urtwg0 a {
        display: block;
        padding: 0px;
        cursor: pointer;
    }

    .css-4xk3hb h1 {
        font-size: 20px;
        color: var(--N700, #31353B);
        margin: 0px 0px 19px;
    }

    .css-1jhc3ur-unf-heading {
        display: block;
        position: relative;
        font-weight: 700;
        font-size: 1rem;
        line-height: 20px;
        letter-spacing: 0px;
        color: var(--NN950, #212121);
        text-decoration: initial;
        margin: 16px 0px 0px;
    }

    .css-157s6vo .shop-group {
        padding: 16px 0px 0px;
        border-bottom: 6px solid var(--N50, #F3F4F5);
    }

    .css-157s6vo .shop-heading {
        margin-bottom: 20px;
    }

    .css-157s6vo .shop-heading__flex {
        display: flex;
        -webkit-box-pack: justify;
        justify-content: space-between;
    }

    .css-157s6vo .shop-heading__left {
        padding-right: 15px;
    }

    .css-4xk3hb {
        min-height: 0px;
        position: relative;
    }

    .css-157s6vo .shop-name-n-badges-wrapper {
        line-height: 1.4;
        margin-bottom: 4px;
        display: flex;
        -webkit-box-align: center;
        align-items: center;
    }

    .css-157s6vo .shop-name {
        text-decoration: none;
        font-weight: 700;
        color: var(--N700, #31353B);
        margin-right: 8px;
    }

    .css-qf88ll {
        display: inline-flex;
        -webkit-box-align: center;
        align-items: center;
        color: var(--N700, rgba(49, 53, 59, 0.68));
        margin-top: 4px;
    }

    .css-fkvnka-unf-heading {
        display: block;
        position: relative;
        font-weight: 400;
        font-size: 0.857143rem;
        line-height: 18px;
        letter-spacing: 0px;
        color: var(--NN600, #6D7588);
        text-decoration: initial;
        margin: 0px;
    }

    .css-qf88ll p {
        font-size: 12px;
    }

    .css-qf88ll .shop__inline {
        display: inline-flex;
        -webkit-box-align: center;
        align-items: center;
    }

    .css-157s6vo .shop-body-content-wrapper {
        display: flex;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--N100, #DBDEE2);
    }

    .css-157s6vo .shop-body-content__left {
        -webkit-box-flex: 1;
        flex-grow: 1;
        padding-right: 15px;
    }

    .css-157s6vo .shop-product {
        margin-bottom: 25px;
    }

    .css-157s6vo .shop-product__flex {
        display: flex;
    }

    .css-157s6vo .shop-product__left {
        flex-shrink: 0;
    }

    .css-157s6vo .product-img {
        display: block;
        width: 60px;
        height: 60px;
        color: var(--N0, #FFFFFF);
        background: var(--N0, #FFFFFF);
    }

    .css-m2nf2c {
        background-repeat: no-repeat;
        background-size: 99% 100%;
        display: inline-block;
        height: auto;
        margin: 0px auto;
        position: relative;
        text-align: center;
        width: 100%;
    }

    .css-157s6vo .product-img img {
        display: block;
        width: 100%;
        border-radius: 6px;
    }

    .css-m2nf2c>img.fade.success,
    .css-m2nf2c>img.fade.default {
        opacity: 1;
    }

    .css-157s6vo .shop-product__right {
        padding-left: 15px;
    }

    .css-m3bste-unf-heading {
        display: block;
        position: relative;
        font-weight: 400;
        font-size: 1rem;
        line-height: 20px;
        letter-spacing: 0px;
        color: var(--NN600, #6D7588);
        text-decoration: initial;
        margin: 0px 0px 2px;
    }

    .css-157s6vo .shop-product__right .product__name {
        color: var(--N700, rgba(49, 53, 59, 0.96));
        overflow-wrap: anywhere;
    }

    .css-157s6vo .variant-wrapper {
        display: flex;
    }

    .css-1of93gz-unf-heading {
        display: block;
        position: relative;
        font-weight: 400;
        font-size: 0.857143rem;
        line-height: 18px;
        letter-spacing: 0px;
        color: var(--NN600, #6D7588);
        text-decoration: initial;
        margin: 4px 8px 4px 0px;
    }

    .css-157s6vo .variant-wrapper .variant__text,
    .css-157s6vo .variant-wrapper .variant__quantity {
        color: var(--N700, rgba(49, 53, 59, 0.96));
    }

    .css-12ydbts-unf-heading {
        display: block;
        position: relative;
        font-weight: 400;
        font-size: 0.857143rem;
        line-height: 18px;
        letter-spacing: 0px;
        color: var(--NN600, #6D7588);
        text-decoration: initial;
        margin: 4px 0px;
    }

    .css-157s6vo .variant-wrapper .variant__text,
    .css-157s6vo .variant-wrapper .variant__quantity {
        color: var(--N700, rgba(49, 53, 59, 0.96));
    }

    .css-157s6vo .price-wrapper {
        margin-bottom: 5px;
        display: flex;
        -webkit-box-align: center;
        align-items: center;
    }

    .css-rbvr5f-unf-heading {
        display: block;
        position: relative;
        font-weight: 400;
        font-size: 0.857143rem;
        line-height: 18px;
        letter-spacing: 0px;
        color: var(--NN600, #6D7588);
        text-decoration: initial;
        margin: 0px 8px 0px 0px;
    }

    .css-157s6vo .price-wrapper .slashed-price {
        text-decoration: line-through;
        color: var(--N200, #9FA6B0);
        margin-right: 10px;
    }

    .css-1fqqzz-unf-heading {
        display: block;
        position: relative;
        font-weight: 700;
        font-size: 1rem;
        line-height: 20px;
        letter-spacing: 0px;
        color: var(--NN950, #212121);
        text-decoration: initial;
        margin: 0px;
    }

    .css-lwa81l-unf-btn {
        background-color: var(--GN500, #00AA5B);
        border: none;
        border-radius: 8px;
        color: rgb(255, 255, 255);
        cursor: pointer;
        display: block;
        font-family: inherit;
        font-weight: 700;
        font-size: 1rem;
        height: 40px;
        line-height: 20px;
        outline: none;
        position: relative;
        text-indent: initial;
        transition: background-color 300ms ease 0s;
        width: 100%;
    }

    .css-lwa81l-unf-btn span {
        display: flex;
        font-size: inherit;
        padding: 0px 16px;
        width: 100%;
        height: 40px;
        opacity: 1;
        overflow: hidden;
        position: relative;
        text-overflow: ellipsis;
        top: 0px;
        transition: opacity 300ms linear 0s, top 300ms linear 0s;
        white-space: nowrap;
        justify-content: space-around;
        align-items: center;
    }

    .css-83gmwr .ddsd-cap-fill {
        position: relative;
        display: flex;
        width: 100%;
        height: 100%;
        align-items: center;
    }

    .css-83gmwr .ddsd-cap-text {
        width: calc(100% - 11px);
        font-size: 12px;
        font-weight: 700;
    }

    .css-83gmwr .ddsd-caret {
        display: block;
        width: 11px;
        height: 11px;
        background-size: contain;
        background-position: center center;
        background-repeat: no-repeat;
        position: absolute;
        right: 0px;
        top: 50%;
        background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20307.1%20307.1%22%3E%3Cpath%20d%3D%22M302.4%20205.8L164.6%2068c-6.1-6.1-16.1-6.1-22.2%200L4.6%20205.8c-6.1%206.1-6.1%2016.1%200%2022.2l11.1%2011.1c6.1%206.1%2016.1%206.1%2022.2%200l115.6-115.6%20115.6%20115.6c6.1%206.1%2016.1%206.1%2022.2%200l11.1-11.1c6.2-6.1%206.2-16.1%200-22.2z%22%20fill%3D%22%23fff%22%2F%3E%3C%2Fsvg%3E');
        transform: rotate(180deg) translate(10px, 5px);
        transition: all 300ms cubic-bezier(0.63, 0.01, 0.29, 1) 0s;
    }

    .css-157s6vo .shop-body-content__right {
        flex-shrink: 0;
        width: 306px;
    }

    .css-83gmwr .ddsd-label {
        line-height: 1.3;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--N700, #31353B);
    }

    .css-157s6vo .right-side__lower-section-wrapper {
        margin-top: 20px;
    }

    .css-157s6vo .rslsw__dropdown-shipping-courier-wrapper {
        margin-bottom: 8px;
    }

    .css-12w03s4 .ddsc-label {
        line-height: 1.3;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--N700, #31353B);
    }

    .css-12w03s4 .ddsc-cap {
        display: flex;
        -webkit-box-pack: justify;
        justify-content: space-between;
    }

    .css-12w03s4 .ddsc-cap__result {
        display: flex;
        flex-direction: column;
        margin-right: 10px;
        line-height: 1.3;
        font-size: 12px;
    }

    .css-12w03s4 .ddsc-option__mvc-left {
        display: flex;
        -webkit-box-align: start;
        align-items: start;
    }

    .css-164r41r {
        margin-top: 8px;
    }

    .css-m6di7s {
        padding: 6px 0px;
    }

    .css-m6di7s .shop-footer__row {
        display: flex;
        -webkit-box-pack: justify;
        justify-content: space-between;
    }

    .css-m6di7s .shop-footer__subtotal .shop-footer__row {
        padding: 6px 0px;
    }

    .css-m6di7s .shop-footer__row .sf-row-value {
        flex-shrink: 0;
        line-height: 1.4;
    }

    .css-m6di7s .shop-footer__row .sf-row-value.subtotal {
        font-weight: 700;
        color: var(--Y500, #FA591D);
        cursor: pointer;
        font-size: 16px;
    }

    .css-m6di7s .subtotal__arrow {
        content: "";
        display: inline-block;
        width: 15px;
        height: 15px;
        vertical-align: top;
        background-size: 10px;
        background-repeat: no-repeat;
        background-position: center center;
        background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20292.4%20292.4%22%3E%3Cpath%20d%3D%22M292.4%20210.1c0-4.9-1.8-9.2-5.4-12.8L159%2069.4c-3.6-3.6-7.9-5.4-12.8-5.4s-9.2%201.8-12.9%205.4L5.4%20197.3c-3.6%203.6-5.4%207.9-5.4%2012.8s1.8%209.2%205.4%2012.9c3.6%203.6%207.9%205.4%2012.9%205.4h255.8c4.9%200%209.2-1.8%2012.8-5.4%203.6-3.6%205.5-7.9%205.5-12.9z%22%20fill%3D%22%23dbdee2%22%2F%3E%3C%2Fsvg%3E');
        margin-left: 2px;
        position: relative;
        top: 2px;
        transition: all 300ms cubic-bezier(0.63, 0.01, 0.29, 1) 0s;
        transform: rotate(180deg);
        transform-origin: center center;
    }

    .css-m6di7s .sf-row-value .subtotal__text,
    .css-m6di7s .sf-row-value .subtotal__arrow {
        cursor: pointer;
    }

    .css-m6di7s .shop-footer__row .sf-row-label {
        padding-right: 14px;
        line-height: 1.4;
    }

    .css-48fkxu .sf-row-value {
        color: var(--N700, #31353B);
        font-weight: 700;
    }

    .css-m6di7s.shop-footer--expanded .subtotal__arrow {
        transform: rotate(0deg);
    }

    .shop-footer__details {
        display: none;
    }

    .css-m6di7s.shop-footer--expanded .shop-footer__details {
        display: block;
    }

    .css-83gmwr .ddsd--is-open .ddsd-caret {
        transform: rotate(0deg) translate(-10px, -5px);
    }

    .css-trcznm-unf-CircularV2 {
        display: inline-block;
        opacity: 0;
        z-index: -1;
        height: 24px;
        width: 24px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        transition: opacity 0.5s ease 0s, z-index 0.6s ease 0s;
    }

    .css-trcznm-unf-CircularV2.unf-loading-circle--show {
        opacity: 1;
        z-index: 1;
    }

    .css-10loe1m-unf-btn {
        background-color: var(--GN500, #00AA5B);
        border: none;
        border-radius: 8px;
        color: rgb(255, 255, 255);
        cursor: not-allowed;
        display: block;
        font-family: inherit;
        font-weight: 800;
        font-size: 1rem;
        height: 40px;
        line-height: 20px;
        outline: none;
        position: relative;
        padding: 0px 16px;
        text-indent: initial;
        transition: background-color 300ms ease 0s;
        width: 100%;
    }

    .css-trcznm-unf-CircularV2 .unf-loading-circle__loader {
        transition-property: stroke;
        height: 100%;
        transform-origin: center center;
        width: 100%;
        position: absolute;
        top: 0px;
        left: 0px;
        margin: auto;
    }

    .css-trcznm-unf-CircularV2 .unf-loading-circle__loader.bottom {
        transform: scale(-1, 1) rotate(180deg);
    }

    .css-trcznm-unf-CircularV2 .unf-loading-circle__loader.top {
        transform: rotate(180deg);
    }

    .css-trcznm-unf-CircularV2 .unf-loading-circle__path--bottom {
        transition-property: stroke;
        stroke-dasharray: 0, 200;
        animation: 1.2s ease-in-out 0s infinite normal none running animation-xhy3fo;
        stroke-linecap: round;
        stroke-opacity: 0.7;
    }

    .css-trcznm-unf-CircularV2 .unf-loading-circle__path--top {
        transition-property: stroke;
        stroke-dasharray: 0, 200;
        animation: 1.5s ease-in-out 0s infinite normal none running animation-98wu2y;
        stroke-linecap: round;
        stroke-opacity: 1;
    }

    .css-10loe1m-unf-btn::after {
        background-color: rgb(82, 88, 103);
        border-radius: inherit;
        inset: 0px;
        content: "";
        opacity: 0;
        pointer-events: none;
        position: absolute;
        transition: opacity 300ms ease 0s;
    }

    @keyframes animation-xhy3fo {
        0% {
            stroke-dasharray: 91, 200;
            stroke-dashoffset: 90;
        }

        30% {
            stroke-dasharray: 91, 200;
            stroke-dashoffset: 54;
        }

        50% {
            stroke-dasharray: 91, 200;
            stroke-dashoffset: 0;
        }

        70% {
            stroke-dasharray: 91, 200;
            stroke-dashoffset: -54;
        }

        100% {
            stroke-dasharray: 91, 200;
            stroke-dashoffset: -94;
        }
    }

    @keyframes animation-98wu2y {
        0% {
            stroke-dasharray: 0, 200;
        }

        50% {
            stroke-dasharray: 50, 200;
        }

        100% {
            stroke-dasharray: 94, 200;
            stroke-dashoffset: -94;
        }
    }

    .css-83gmwr .ddsd-body {
        position: relative;
    }

    .css-83gmwr .ddsd-body-content-positioner {
        position: absolute;
        left: 0px;
        top: 0px;
        width: 100%;
        z-index: 2;
    }

    .css-83gmwr .ddsd-body-box {
        border: 1px solid rgb(224, 224, 224);
        border-radius: 4px;
        background-color: var(--N0, #FFFFFF);
        max-height: 200px;
        overflow: auto;
    }

    .css-jenq7o::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    .css-jenq7o::-webkit-scrollbar-thumb {
        background-color: var(--N200, #9FA6B0);
        outline: slategrey solid 1px;
        border-radius: 10px;
    }

    .css-jenq7o::-webkit-scrollbar-track {
        background-color: var(--N75, #E5E7E9);
    }

    .css-83gmwr .ddsd-option {
        display: block;
        cursor: pointer;
    }

    .css-83gmwr .ddsc-option__wrapper {
        padding: 12px 15px;
    }

    .css-83gmwr .ddsc-option * {
        line-height: 1.5;
    }

    .css-83gmwr .ddsc-option::after {
        content: "";
        display: block;
        height: 1px;
        background: var(--N100, #DBDEE2);
        width: calc(100% - 16px);
        margin-left: 16px;
    }

    .css-83gmwr .ddsc-option__flex {
        display: flex;
        -webkit-box-pack: justify;
        justify-content: space-between;
    }

    .css-83gmwr .ddsc-option__col-left {
        -webkit-box-flex: 1;
        flex-grow: 1;
        line-height: 1.4;
    }

    .css-ohge0j-unf-heading {
        display: block;
        position: relative;
        font-weight: 700;
        font-size: 0.857143rem;
        line-height: 18px;
        letter-spacing: 0px;
        color: var(--N700, #31353B);
        text-decoration: initial;
        margin: 0px;
    }

    .css-83gmwr .ddsc-option__col-right {
        flex-shrink: 0;
        margin-left: 20px;
        display: inline-flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .css-83gmwr .ddsc-option:hover {
        background-color: var(--N50, #F3F4F5);
        cursor: pointer;
    }

    .css-wrkbw4 {
        position: relative;
    }

    .css-wrkbw4 .summary-positioner {
        width: 100%;
        z-index: 1;
    }

    .css-wrkbw4 .summary-wrapper {
        opacity: 0;
        transition: all 300ms cubic-bezier(0.63, 0.01, 0.29, 1) 0s;
        transform: translateY(-30px);
    }

    .css-wrkbw4 .summary-wrapper.summary-position-initiated {
        opacity: 1;
        transform: translateY(0px);
    }

    .css-y1w77o-unf-card {
        display: block;
        position: relative;
        margin: 0px;
        padding: 0px;
        border-radius: 8px;
        border-color: var(--NN200, #D6DFEB);
        box-shadow: rgba(141, 150, 170, 0.4) 0px 1px 4px;
        background-color: var(--NN0, #FFFFFF);
        cursor: default;
    }

    .css-wrkbw4 .corplat-sidebar-card {
        overflow: visible;
    }

    .css-19midj6 {
        padding: 16px;
    }

    .css-wrkbw4 .sidebar-heading-text {
        font-weight: 700;
        color: var(--N700, #31353B);
        line-height: 1.6;
        font-size: 1.1rem;
    }

    .css-wrkbw4 .shopping-details-wrapper {
        margin-top: 16px;
        margin-bottom: 16px;
        line-height: 1.6;
    }

    .css-izuqqr {
        display: flex;
        flex-direction: column;
        -webkit-box-align: center;
        align-items: center;
        gap: 4px;
    }

    .css-12d2mry {
        width: 100%;
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        -webkit-box-pack: justify;
        justify-content: space-between;
    }

    .css-rt0bne {
        flex: 1 1 0%;
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        gap: 2px;
    }

    .css-1z0diop-unf-heading {
        display: block;
        position: relative;
        font-weight: 400;
        font-size: 0.96rem;
        line-height: 22px;
        letter-spacing: 0px;
        color: var(--NN950, #212121);
        text-decoration: initial;
        margin: 0px;
    }

    .css-171onha {
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        gap: 4px;
    }

    .css-ue30lg-unf-heading {
        display: block;
        position: relative;
        font-weight: 400;
        font-size: 0.96rem;
        line-height: 22px;
        letter-spacing: 0px;
        color: var(--NN900, #2E3137);
        text-decoration: initial;
        margin: 0px;
    }

    .sticky {
        position: sticky;
        top: 20px;
    }

    .css-wrkbw4 .summary-grand-total-row {
        display: flex;
        -webkit-box-pack: justify;
        justify-content: space-between;
        -webkit-box-align: center;
        align-items: center;
        padding-top: 16px;
        margin-bottom: 17px;
        border-top: 1px solid var(--N100, #DBDEE2);
    }

    .css-wrkbw4 .sgtr__label {
        font-weight: 700;
        color: var(--N700, #31353B);
        padding-right: 14px;
        font-size: 16px;
    }

    .css-wrkbw4 .sgtr__value {
        font-weight: 700;
        flex-shrink: 0;
        font-size: 1.21429rem;
        color: var(--N700, #31353B);
    }

    .css-1w4crhq {
        font-size: 0.695714rem;
        line-height: 1.5;
    }

    .css-wrkbw4 .summary-main-btns-wrapper {
        margin-top: 24px;
    }

    .css-wrkbw4 .summary-main-btn {
        display: block;
        margin-bottom: 14px;
    }

    .css-wrkbw4 .summary-main-btn:last-child,
    .css-wrkbw4 .summary-main-btn:only-child {
        margin-bottom: 0px;
    }

    .css-1k9qobw-unf-btn {
        background-color: var(--GN500, #00AA5B);
        border: none;
        border-radius: 8px;
        color: rgb(255, 255, 255);
        cursor: pointer;
        display: block;
        font-family: inherit;
        font-weight: 700;
        font-size: 1.14286rem;
        height: 48px;
        line-height: 22px;
        outline: none;
        position: relative;
        padding: 0px 16px;
        text-indent: initial;
        transition: background-color 300ms ease 0s;
        width: 100%;
    }

    .css-dmrkw7 {
        width: 100%;
        margin-top: 50px;
        background-color: var(--NN0, #FFFFFF);
        border-top: 1px solid var(--NN300, #BFC9D9);
        font-size: 13px;
    }

    .css-1dfix3h-unf-footer {
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        -webkit-box-pack: justify;
        justify-content: space-between;
        padding: 16px 32px;
        background-color: var(--color-page-background, #FFFFFF);
        width: 100%;
        margin: 0px auto;
    }

    .css-70qvj9 {
        display: flex;
        -webkit-box-align: center;
        align-items: center;
    }

    .css-1qjsill {
        display: inline-block;
        width: 125px;
        height: 42px;
        margin-right: 8px;
        background-image: url("{{ RvMedia::getImageUrl($logo) }}");
        background-repeat: no-repeat;
        background-size: 125px 42px;
        background-position: center center;
    }

    .css-dmrkw7 span {
        font-weight: 600;
        font-size: 13px !important;
    }

    .css-184gfr5 {
        display: flex;
        flex-flow: column nowrap;
        color: var(--color-text-low, rgba(49, 53, 59, 0.68));
    }

    .css-184gfr5 span {
        line-height: 18px;
        font-size: 0.857143rem;
    }

    .css-dmrkw7 span {
        font-weight: 600;
        font-size: 13px !important;
    }

    .ddsd-front-error {
        color: var(--R600, #D6001C);
        font-size: 12px;
        line-height: 1.4;
        margin-top: 6px;
    }

    .css-12bdkld-overlay {
        width: 100%;
        height: 100%;
        background-color: #090a0c;
        opacity: 0.9;
        position: fixed;
        top: 0px;
        left: 0px;
        overflow: hidden;
        z-index: 1040;
        will-change: opacity;
        transition: opacity 300ms cubic-bezier(0, 0, 0.3, 1) 0s;
    }

    .css-7lpkml {
        position: fixed;
        display: flex;
        top: 0px;
        left: 0px;
        width: 100%;
        height: 100%;
        -webkit-box-align: center;
        align-items: center;
        -webkit-box-pack: center;
        justify-content: center;
        padding: 24px;
        pointer-events: none;
        z-index: 1050;
    }

    .css-1qxu6r1-unf-modal {
        position: relative;
        display: flex;
        flex-direction: column;
        background-color: var(--NN0, #FFFFFF);
        border-radius: 8px;
        transition: opacity 0.2s ease 0s;
        box-shadow: rgba(141, 150, 170, 0.4) 0px 1px 4px;
        pointer-events: all;
        max-height: 100%;
        min-width: 450px;
        opacity: 1;
    }

    .css-d6mneo {
        height: 100%;
        overflow-y: auto;
        padding: 0px;
    }

    .css-1isxc0n {
        height: calc(100vh - 40px);
        max-height: 640px;
    }

    .css-1isxc0n #scrooge-iframe-wrapper {
        height: 100%;
        width: 100%;
        margin: auto;
        border-radius: 8px;
        overflow: hidden;
    }

    .css-1isxc0n #scrooge-iframe {
        width: 100%;
        height: 100%;
        border: 0px;
    }

    .preloader-modal {
        text-align: center;
        display: flex;
        width: 100%;
        height: 100%;
        align-items: center;
        justify-content: center;
    }

    .cart {
        display: block;
        margin: 0 auto 1.5em auto;
        width: 8em;
        height: 8em;
    }
    .cart__lines,
    .cart__top,
    .cart__wheel1,
    .cart__wheel2,
    .cart__wheel-stroke {
        animation: cartLines 2s ease-in-out infinite;
    }
    .cart__lines {
        stroke: var(--primary);
    }
    .cart__top {
        animation-name: cartTop;
    }
    .cart__wheel1 {
        animation-name: cartWheel1;
        transform: rotate(-0.25turn);
        transform-origin: 43px 111px;
    }
    .cart__wheel2 {
        animation-name: cartWheel2;
        transform: rotate(0.25turn);
        transform-origin: 102px 111px;
    }
    .cart__wheel-stroke {
        animation-name: cartWheelStroke
    }
    .cart__track {
        stroke: hsla(var(--hue),10%,10%,0.1);
        transition: stroke var(--trans-dur);
    }

    .css-w4ndmf {
        border-bottom: 6px solid var(--N50,#F3F4F5);
        padding: 16px;
    }

    .css-vodfio {
        min-height: 52px;
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        width: 100%;
        border-radius: 8px;
        background: var(--N0,#FFFFFF);
        border: solid 1px var(--N100,#DBDEE2);
        padding: 8px;
        cursor: pointer;
    }

    .css-vodfio .content {
        margin-left: 8px;
        width: calc(100% - 48px);
    }

    .css-tncl4u:last-child, .css-tncl4u:only-child {
        margin-bottom: 0px;
    }

    .css-1fs76gg {
        color: var(--N400,#6C727C);
        font-size: 16px;
        font-weight: 700;
        display: block;
    }

    .css-tncl4u span {
        margin-bottom: 0;
    }

    .css-12gses5 {
        background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgdmlld0JveD0iMCAwIDQwIDQwIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxjaXJjbGUgY3g9IjI0IiBjeT0iMjQiIHI9IjI0Ii8+PHBhdGggc3Ryb2tlPSIjNmM3MjdjIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiIHN0cm9rZS13aWR0aD0iNCIgZD0iTTE2LjUzIDI3LjQ3bDcuMjAyLTcuMjAybS03LjIwMi03LjJsNy4yMDIgNy4yMDMiLz48L2c+PC9zdmc+Cg==");
        height: 24px;
        width: 24px;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center center;
        display: inline-block;
        margin-bottom: 0;
    }

    @keyframes cartLines {
        from,
        to {
            opacity: 0;
        }
        8%,
        92% {
            opacity: 1;
        }
    }
    @keyframes cartTop {
        from {
            stroke-dashoffset: -338;
        }
        50% {
            stroke-dashoffset: 0;
        }
        to {
            stroke-dashoffset: 338;
        }
    }
    @keyframes cartWheel1 {
        from {
            transform: rotate(-0.25turn);
        }
        to {
            transform: rotate(2.75turn);
        }
    }
    @keyframes cartWheel2 {
        from {
            transform: rotate(0.25turn);
        }
        to {
            transform: rotate(3.25turn);
        }
    }
    @keyframes cartWheelStroke {
        from,
        to {
            stroke-dashoffset: 81.68;
        }
        50% {
            stroke-dashoffset: 40.84;
        }
    }

    @media screen and (max-width: 767px) {
        .css-157s6vo .shop-body-content-wrapper {
            flex-direction: column;
        }

        .css-ve9dke,
        .css-xz6h1x {
            min-width: 0;
        }

        .css-urtwg0 {
            width: 100%;
        }

        .css-157s6vo .shop-body-content__right {
            width: 100%;
        }

        .css-y1w77o-unf-card {
            box-shadow: none;
            border: none;
        }

        .css-dmrkw7 {
            margin-top: 0;
        }

        .css-1dfix3h-unf-footer {
            justify-content: center;
        }

        .css-70qvj9 {
            flex-direction: column;
        }
    }
</style>
