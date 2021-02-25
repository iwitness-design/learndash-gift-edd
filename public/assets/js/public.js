(function ( $ ) {
    $(document).ready(function () {
      $('.edd_ld_gift_date').datetimepicker({
        minDate   : 0,
        dateFormat: 'dd-mm-yy',
        timeFormat: 'hh:mm tt',
        timeInput: true,
        altField: "#edd_ld_gift_timestamp_holder",
        altFieldTimeOnly: false,
        altFormat: "yy-mm-dd",
        altTimeFormat: "HH:mm:00",
        altSeparator:'T',
        onSelect: function () {
          var time = $('#edd_ld_gift_timestamp_holder').val()
          var timestamp = Date.parse(time);
          $('#edd_ld_gift_timestamp').val(timestamp / 1000);
        }
      });
      $('.edd_ld_gift_date').datetimepicker('setDate', new Date());
      $('.edd_form').on('click', '.buy_as_gift_checkbox', function () {
        if ($(this).prop('checked') == true) {
          $('.buy_as_gift_section').show();
          $('#edd-ld-gift-email, #edd-ld-gift-first-name, #edd-ld-gift-last-name').attr('required', 'required');
        } else if ($(this).prop('checked') == false) {
          $('.buy_as_gift_section').hide();
          $('#edd-ld-gift-email, #edd-ld-gift-first-name, #edd-ld-gift-last-name').removeAttr('required');
        }
      });
      $('.edd_form').on('click', '.buy_as_gift_checkbox_label', function () {
        if ($('.buy_as_gift_checkbox').prop('checked') == true) {
          $('.buy_as_gift_checkbox').prop('checked', false);
          $('.buy_as_gift_section').hide();
          $('#edd-ld-gift-email, #edd-ld-gift-first-name, #edd-ld-gift-last-name').removeAttr('required');
        } else if ($('.buy_as_gift_checkbox').prop('checked') == false) {
          $('.buy_as_gift_checkbox').prop('checked', true);
          $('.buy_as_gift_section').show();
          $('#edd-ld-gift-email, #edd-ld-gift-first-name, #edd-ld-gift-last-name').attr('required', 'required');
        }
      });
    });
}(jQuery));