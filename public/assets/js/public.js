(
  function ($) {
    'use strict';

    var checkoutForm = function () {
      var SELF = this;

      SELF.init = function () {
        SELF.$form = $('.edd_form');

        if (!SELF.$form.length) {
          return;
        }

        SELF.$section = SELF.$form.find('.buy_as_gift_section');
        SELF.$toggle = SELF.$form.find('.buy_as_gift_checkbox');
        SELF.$sendDateToggle = SELF.$form.find('input[name=edd_ld_send_toggle]');
        SELF.$giftDatePicker = SELF.$form.find('#edd-ld-gift-date');
        SELF.$requiredFields = SELF.$form.find('#edd-ld-gift-email, #edd-ld-gift-first-name, #edd-ld-gift-last-name');

        if (SELF.$giftDatePicker.length) {
          SELF.initGiftDate();
        }

        if (SELF.$sendDateToggle.length) {
          SELF.$sendDateToggle.on('change', SELF.toggleDatePicker);
        }

        SELF.$toggle.on('change', SELF.toggleFields);
      };

      SELF.toggleFields = function () {
        if (SELF.$toggle.prop('checked') === true) {
          SELF.$section.show();
          SELF.$requiredFields.attr('required', 'required');
        } else {
          SELF.$section.hide();
          SELF.$requiredFields.removeAttr('required');
        }
      };

      SELF.toggleDatePicker = function() {
        if ( SELF.$form.find('input[name=edd_ld_send_toggle]:checked').val() === 'now' ) {
          SELF.$form.find('.edd-ld-gift-date-cont').hide();
        } else {
          SELF.$form.find('.edd-ld-gift-date-cont').show();
        }
      };

      SELF.initGiftDate = function () {
        SELF.$giftDatePicker.datetimepicker({
          minDate         : 0,
          dateFormat      : 'dd-mm-yy',
          timeFormat      : 'hh:mm tt',
          timeInput       : true,
          showTimezone    : true,
          timezoneList    : [
            {label: '(UTC-12) Baker Island Time', value: -720},
            {label: '(UTC-11) Niue Time, Samoa Standard Time', value: -660},
            {label: '(UTC-10) Hawaii-Aleutian Standard Time', value: -600},
            {label: '(UTC-9:30) Marquesas Islands Time', value: -570},
            {label: '(UTC-9) Alaska Standard Time', value: -540},
            {label: '(UTC-8) Pacific Standard Time', value: -480},
            {label: '(UTC-7) Mountain Standard Time', value: -420},
            {label: '(UTC-6) Central Standard Time', value: -360},
            {label: '(UTC-5) Eastern Standard Time', value: -300},
            {label: '(UTC-4:30) Venezuelan Standard Time', value: -270},
            {label: '(UTC-4) Atlantic Standard Time', value: -240},
            {label: '(UTC-3:30) Newfoundland Standard Time', value: -210},
            {label: '(UTC-3) Amazon Standard Time', value: -180},
            {label: '(UTC-2) Fernando de Noronha Time', value: -120},
            {label: '(UTC-1) Azores Standard Time', value: -60},
            {label: '(UTC) Western European Time', value: 0},
            {label: '(UTC+1) Central European Time', value: 60},
            {label: '(UTC+2) Eastern European Time', value: 120},
            {label: '(UTC+3) Moscow Standard Time', value: 180},
            {label: '(UTC+3:30) Iran Standard Time', value: 210},
            {label: '(UTC+4) Gulf Standard Time', value: 240},
            {label: '(UTC+4:30) Afghanistan Time', value: 270},
            {label: '(UTC+5) Pakistan Standard Time', value: 300},
            {label: '(UTC+5:30) Indian Standard Time', value: 330},
            {label: '(UTC+5:45) Nepal Time', value: 345},
            {label: '(UTC+6) Bangladesh Time', value: 360},
            {label: '(UTC+6:30) Cocos Islands Time', value: 390},
            {label: '(UTC+7) Indochina Time', value: 420},
            {label: '(UTC+8) Chinese Standard Time', value: 480},
            {label: '(UTC+8:45) Southeastern Western Australia Standard Time', value: 525},
            {label: '(UTC+9) Japan Standard Time', value: 540},
            {label: '(UTC+9:30) Australian Central Standard Time', value: 570},
            {label: '(UTC+10) Australian Eastern Standard Time', value: 600},
            {label: '(UTC+10:30) Lord Howe Standard Time', value: 630},
            {label: '(UTC+11) Solomon Island Time', value: 660},
            {label: '(UTC+11:30) Norfolk Island Time', value: 690},
            {label: '(UTC+12) New Zealand Time', value: 720},
            {label: '(UTC+12:45) Chatham Islands Time', value: 765},
            {label: '(UTC+13) Tonga Time', value: 780},
            {label: '(UTC+14) Line Island Time', value: 840}
          ],
          altField        : '#edd_ld_gift_timestamp',
          altFieldTimeOnly: false,
          altFormat       : 'yy-mm-dd',
          altTimeFormat   : 'HH:mm:00' + 'Z',
          altSeparator    : 'T',
        });

        SELF.$giftDatePicker.datetimepicker('setDate', new Date());
      };

      SELF.init();
    };

    $(document).ready(function () {
      new checkoutForm();
    });
  }(jQuery)
);