(function ( $ ) {

	$(document).ready(function(){
		$( ".edd_ld_gift_date" ).datepicker({
                minDate: 0,
                dateFormat: 'dd-mm-yy'
            });
        $(".edd_ld_gift_date").datepicker("setDate", new Date());
		$(".buy_as_gift_checkbox_div").on("click", ".buy_as_gift_checkbox", function() {
			if($(this).prop("checked") == true){
                $(".buy_as_gift_section").show();
				$('#edd-ld-gift-email').attr('required', 'required');
            }
            else if($(this).prop("checked") == false){
                $(".buy_as_gift_section").hide();
                $('#edd-ld-gift-email').removeAttr('required');
            }
		});
        $(".buy_as_gift_checkbox_div").on("click", ".buy_as_gift_checkbox_label", function() {
            if($('.buy_as_gift_checkbox').prop("checked") == true){
                $('.buy_as_gift_checkbox').prop('checked', false);
                $(".buy_as_gift_section").hide();
                $('#edd-ld-gift-email').removeAttr('required');
            }
            else if($('.buy_as_gift_checkbox').prop("checked") == false){
                $('.buy_as_gift_checkbox').prop('checked', true);
                $(".buy_as_gift_section").show();
                $('#edd-ld-gift-email').attr('required', 'required');
            }
        });
	});

}(jQuery));