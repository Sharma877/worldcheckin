jQuery(function() {
	jQuery('.gd-cart-pmethods [name="gd_payment_method"]').click(function() {
		if (jQuery('.gd-cart-pmethods [name="gd_payment_method"]').length > 1) {
			var target = jQuery('div.gd-payment-box.' + jQuery(this).attr('ID'));
			if (jQuery(this).is(':checked') && !target.is(':visible')) {
				jQuery('div.gd-payment-box').filter(':visible').slideUp(250);
				if (jQuery(this).is(':checked')) {
					var slDown = jQuery('div.gd-payment-box.' + jQuery(this).attr('ID')).html();
					if (typeof slDown != 'undefined') {
						if (parseInt(slDown.trim().length) > 5) {
							jQuery('div.gd-payment-box.' + jQuery(this).attr('ID') + ' #' + jQuery(this).val() + 'options').show();
							jQuery('div.gd-payment-box.' + jQuery(this).attr('ID')).slideDown(250);
						}
					}
				}
			}
		} else {
			jQuery('div.gd-payment-box').show();
		}
		
		var btnTxt = jQuery(this).data('btn-txt');
		
		if (btnTxt && btnTxt.trim()) {
			jQuery('.gd-checkout-actions input[name="gd_checkout_paynow"]').val(btnTxt);
		} else {
			jQuery('.gd-checkout-actions input[name="gd_checkout_paynow"]').val(jQuery('.gd-checkout-actions input[name="gd_checkout_paynow"]').data('btn-txt'));
		}
	});
	jQuery('.gd-cart-pmethods [name="gd_payment_method"]:checked').click();
	
	jQuery("input#gd_coupon_btn").click(function() {
		var $this = this;
		var $form = jQuery($this).closest('form');
		var coupon = jQuery('input#gd_coupon', $form).val().trim();
		var nonce = jQuery('#gd_cart_nonce', $form).val();
	
		jQuery.ajax({
			url: geodir_payment_all_js_msg.geodir_payment_admin_ajax_url,
			type: 'POST',
			dataType: 'json',
			data: 'action=geodir_payment_manager_ajax&task=apply_coupon&coupon=' + coupon + '&_wpnonce=' + nonce,
			beforeSend: function() {
				//console.log('beforeSend');
				jQuery('.gd-cart-info', $form).css({'opacity':'.6'});
			},
			success: function(data, textStatus, xhr) {
				//console.log('success');
				if ( data && typeof(data) == 'object' ) {
					if (!data.success) {
						jQuery('input#gd_coupon', $form).val('');
					}
					if (data.msg) {
						alert(data.msg);
					}
					
					if (data.reload) {
						window.location.reload();
					}
				}
			},
			error: function(xhr, textStatus, errorThrown) {
				//console.log('error');
				console.log(errorThrown);
			},
			complete: function(xhr, textStatus) {
				//console.log('complete');
				jQuery('.gd-cart-info', $form).css({'opacity':'1'});
			}
		});
	});
	
	jQuery('#gd_checkout_paynow', '#gd_checkout_form').on('click', function(e) {
		var form = jQuery(this).closest('#gd_checkout_form');
		var pmethod = jQuery('input[name="gd_payment_method"]:checked', form).val();
		
		if (pmethod === 'authorizenet') {
			if (jQuery('#cardholder_name').val() == '') {
				alert(geodir_payment_all_js_msg.authorizenet_cardholder_name_empty);
				return false;
			}
			if (jQuery('#cc_number').val() == '') {
				alert(geodir_payment_all_js_msg.authorizenet_cc_number_empty);
				return false;
			}
			if (jQuery('#cc_month').val() == '' || jQuery('#cc_year').val() == '') {
				alert(geodir_payment_all_js_msg.authorizenet_cc_date_empty);
				return false;
			}
			

		}

	});
});