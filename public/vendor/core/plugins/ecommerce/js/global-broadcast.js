/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
    var __webpack_exports__ = {};
    /*!*************************************************************************!*\
      !*** ./platform/plugins/ecommerce/resources/assets/js/global-option.js ***!
      \*************************************************************************/

    var styleContent = `
      .select2.select2-container {
        width: 100% !important;
      }

      .select2.select2-container .select2-selection {
        border: 1px solid #ccc;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        height: 34px;
        margin-bottom: 15px;
        outline: none !important;
        transition: all .15s ease-in-out;
      }

      .select2.select2-container .select2-selection .select2-selection__rendered {
        color: #333;
        line-height: 32px;
        padding-right: 33px;
      }

      .select2.select2-container .select2-selection .select2-selection__arrow {
        background: #f8f8f8;
        border-left: 1px solid #ccc;
        -webkit-border-radius: 0 3px 3px 0;
        -moz-border-radius: 0 3px 3px 0;
        border-radius: 0 3px 3px 0;
        height: 32px;
        width: 33px;
      }

      .select2.select2-container.select2-container--open .select2-selection.select2-selection--single {
        background: #f8f8f8;
      }

      .select2.select2-container.select2-container--open .select2-selection.select2-selection--single .select2-selection__arrow {
        -webkit-border-radius: 0 3px 0 0;
        -moz-border-radius: 0 3px 0 0;
        border-radius: 0 3px 0 0;
      }

      .select2.select2-container.select2-container--open .select2-selection.select2-selection--multiple {
        border: 1px solid #34495e;
      }

      .select2.select2-container .select2-selection--multiple {
        height: auto;
        min-height: 34px;
      }

      .select2.select2-container .select2-selection--multiple .select2-search--inline .select2-search__field {
        margin-top: 0;
        height: 32px;
      }

      .select2.select2-container .select2-selection--multiple .select2-selection__rendered {
        display: block;
        padding: 0 4px;
        line-height: 29px;
      }

      .select2.select2-container .select2-selection--multiple .select2-selection__choice {
        background-color: #f8f8f8;
        border: 1px solid #ccc;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        margin: 4px 4px 0 0;
        padding: 0 6px 0 22px;
        height: 24px;
        line-height: 24px;
        font-size: 12px;
        position: relative;
      }

      .select2.select2-container .select2-selection--multiple .select2-selection__choice .select2-selection__choice__remove {
        position: absolute;
        top: 0;
        left: 0;
        height: 22px;
        width: 22px;
        margin: 0;
        text-align: center;
        color: #e74c3c;
        font-weight: bold;
        font-size: 16px;
      }

      .select2-container .select2-dropdown {
        background: transparent;
        border: none;
        margin-top: -5px;
      }

      .select2-container .select2-dropdown .select2-search {
        padding: 0;
      }

      .select2-container .select2-dropdown .select2-search input {
        outline: none !important;
        border: 1px solid #34495e !important;
        border-bottom: none !important;
        padding: 4px 6px !important;
      }

      .select2-container .select2-dropdown .select2-results {
        padding: 0;
      }

      .select2-container .select2-dropdown .select2-results ul {
        background: #fff;
        border: 1px solid #34495e;
      }

      .select2-container .select2-dropdown .select2-results ul .select2-results__option--highlighted[aria-selected] {
        background-color: #3498db;
      }
    `;

    $(document).ready(function () {
        var jsOption = {
            currentType: 'N/A',
            init: function init() {
                // var styleElement = document.createElement('style');
                // styleElement.innerHTML = styleContent;
                // document.head.appendChild(styleElement);
                this.initFormFields($('.option-type').val(), 'option');
                this.initFormFields($('.target-type').val(), 'target');
                // this.initFormFields($('.short-code').val(), 'short-code');
                this.eventListeners();
                setTimeout(() => {
                    $('.tagify').hide();
                    $('.target-customer').hide();
                    $('.product-type').closest('.form-group').hide();
                }, 150);
                $('.product-type').select2({
                    placeholder: 'Select Product',
                    minimumInputLength: 3,
                    theme: 'bootstrap-5',
                    ajax: {
                        url: $('.product-type').data('url'),
                        dataType: 'json',
                        delay: 250,
                        processResults: function (data) {
                            return {
                                results: $.map(data.data, function (item) {
                                    return {
                                        id: item.id,
                                        text: item.name,
                                        image: item.image,
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    templateResult: formatData,
                });
                function formatData(data) {
                    console.log(data)
                    if (data.loading) {
                        return data.text;
                    }

                    var $state = $(
                        `<span><img src="${data.image}" style="width: 50px;margin-right: 20px;"/>${data.text}</span>`
                    );
                    return $state;
                }
            },
            eventListeners: function eventListeners() {
                this.onOptionChange();
            },
            onOptionChange: function onOptionChange() {
                var self = this;
                $('.option-type').change(function () {
                    var value = $(this).val();
                    this.currentType = value;
                    self.initFormFields(value, 'option');
                });
                $('.target-type').change(function () {
                    var value = $(this).val();
                    this.currentType = value;
                    self.initFormFields(value, 'target');
                });
            },
            initFormFields: function initFormFields(value, key) {
                this.currentType = value;
                if (key === 'option') {
                    if (value === 'general') {
                        $('.general-type').show();
                        $('.product-type').closest('.form-group').hide();
                    } else {
                        $('.product-type').closest('.form-group').show();
                        $('.general-type').hide();
                    }
                } else {
                    if (value === 'all') {
                        $('.tagify').hide();
                        $('.target-customer').hide();
                    } else {
                        $('.target-customer').show();
                        $('.tagify').show();
                    }
                }
            }
        };
        jsOption.init();
    });
    /******/
})();
