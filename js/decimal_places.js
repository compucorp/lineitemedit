/**
 * Restricts the quantity and unit price fields of a line item to a fixed number of decimal places (2 by default).
 */
CRM.$(function($) {
  var settings = (CRM.vars && CRM.vars.lineitemedit) ? CRM.vars.lineitemedit : {};
  var maxDecimalPlaces = parseInt(settings.maxDecimalPlaces, 10);
  if (isNaN(maxDecimalPlaces) || maxDecimalPlaces < 0) {
    maxDecimalPlaces = 2;
  }

  var decimalPoint = settings.decimalPoint || '.';
  var thousandSeparator = settings.thousandSeparator || ',';

  var separators = [decimalPoint];
  if (decimalPoint !== '.' && thousandSeparator !== '.') {
    separators.push('.');
  }

  var selector = [
    'input[id^="item_qty"]',
    'input[id^="item_unit_price"]',
    'input#qty',
    'input#unit_price'
  ].join(', ');

  /**
   * Drops any decimal places beyond the allowed number.
   *
   * @param string value
   * @returns string
   */
  function restrict(value) {
    var position = -1;
    $.each(separators, function(index, separator) {
      position = Math.max(position, value.lastIndexOf(separator));
    });

    if (position === -1 || (value.length - position - 1) <= maxDecimalPlaces) {
      return value;
    }

    // Keep the separator itself only when decimals are allowed at all.
    return value.substring(0, position + (maxDecimalPlaces ? maxDecimalPlaces + 1 : 0));
  }

  $(selector).on('input change', function() {
    var value = $(this).val();
    if (typeof value !== 'string') {
      return;
    }

    var restricted = restrict(value);
    if (restricted !== value) {
      $(this).val(restricted);
    }
  });
});
