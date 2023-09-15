/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};
/*!*************************************************************************!*\
  !*** ./platform/plugins/ecommerce/resources/assets/js/global-option.js ***!
  \*************************************************************************/


$(document).ready(function () {
  var jsOption = {
    currentType: 'N/A',
    init: function init() {
      this.eventListeners();
      $('.option-sortable').sortable({
        stop: function stop() {
          var idsInOrder = $('.option-sortable').sortable('toArray', {
            attribute: 'data-index'
          });
          idsInOrder.map(function (id, index) {
            $('.option-row[data-index="' + id + '"]').find('.option-order').val(index);
          });
        }
      });
    },
    addNewRow: function addNewRow() {
        $(".add-new-row").click(function () {
            var t = $(this).parent().find("table tbody"),
                n = t.find("tr").last().clone(),
                e = "options[" + t.find("tr").length + "][option_value]",
                i = "options[" + t.find("tr").length + "][option_description]",
                o = "options[" + t.find("tr").length + "][option_target]";
            n.find(".option-label").attr("name", e), n.find(".option-description").attr("name", i), n.find(".option-target").attr("name", o), t.append(n);
        }),
        this
    },
    removeRow: function removeRow() {
      $('.option-setting-tab').on('click', '.remove-row', function () {
        var table = $(this).parent().parent().parent();
        if (table.find('tr').length > 1) {
          $(this).parent().parent().remove();
        } else {
          return false;
        }
      });
      return this;
    },
    eventListeners: function eventListeners() {
      this.addNewRow().removeRow();
    }
  };
  jsOption.init();
});
/******/ })()
;
