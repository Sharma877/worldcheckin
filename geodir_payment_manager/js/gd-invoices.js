jQuery(function() {
				
});
function gd_invoice_gopage(obj, pid) {
	var pid = parseInt(pid);
	var container = jQuery(obj).closest('.gd-payment-invoices');
	
	if (!pid > 0 || !(container && typeof container != 'undefined')) {
		return false;
	}
	
	var data = {
		'action': 'geodir_payment_manager_ajax',
		'task': 'invoices',
		'_wpnonce': geodir_payment_all_js_msg.ajax_invoices_nonce,
		'pageno': pid
	};
	jQuery(document).ajaxStop(function() {
		jQuery('table', container).css({'opacity': '1'});
		jQuery('.gdp-invoices-loading', container).hide();
	});
	
	jQuery('table', container).css({'opacity': '0.4'});
	jQuery('.gdp-invoices-loading', container).show();
	
	jQuery.post(geodir_payment_all_js_msg.geodir_payment_admin_ajax_url, data, function(response) {
		if (response && response != '0') {
			jQuery(container).html(response);
		}
	});
}

function gd_invoice_paynow(invoice_id, row) {
	var nonce = jQuery('.gd-inv-nonce', row).val();
	
	if (!invoice_id || !nonce) {
		return false;
	}
	
	if (!confirm(geodir_payment_all_js_msg.geodir_pay_invoice_confirm)) {
		return false;
	}
	
	jQuery.ajax({
		url: geodir_payment_all_js_msg.geodir_payment_admin_ajax_url,
		type: 'POST',
		dataType: 'json',
		data: 'action=geodir_payment_manager_ajax&task=invoice_pay&invoice_id=' + invoice_id + '&_wpnonce=' + nonce,
		beforeSend: function() {
			//console.log('beforeSend');
			jQuery(row).css({'opacity':'.5'});
		},
		success: function(data, textStatus, xhr) {
			//console.log('success');
			if ( data && typeof(data) == 'object' ) {
				if (data.success && data.reload) {
					window.location.href = geodir_payment_all_js_msg.geodir_checkout_link;
				}
				if (!data.success && data.msg) {
					alert(data.msg);
				}
			}
		},
		error: function(xhr, textStatus, errorThrown) {
			//console.log('error');
			console.log(errorThrown);
		},
		complete: function(xhr, textStatus) {
			//console.log('complete');
			jQuery(row).css({'opacity':'1'});
		}
	});
}

function gd_invoice_details(invoice_id) {
	var obj = jQuery('#gd_payment_invoices');
	var id = jQuery('.gd-inv-row.gd-inv-act').attr('data-id');
	
	if (id) {
		jQuery('.gd-inv-info-' + id).hide();
		jQuery('.gd-inv-info-' + id).removeClass('gd-inv-act');
		jQuery('.gd-inv-row-' + id).removeClass('gd-inv-act');
		
		if (id != invoice_id) {
			jQuery('.gd-inv-info-' + invoice_id, obj).show();
			jQuery('.gd-inv-info-' + invoice_id, obj).addClass('gd-inv-act');
			jQuery('.gd-inv-row-' + invoice_id, obj).addClass('gd-inv-act');
		}
	} else {
		jQuery('.gd-inv-info-' + invoice_id, obj).show();
		jQuery('.gd-inv-info-' + invoice_id, obj).addClass('gd-inv-act');
		jQuery('.gd-inv-row-' + invoice_id, obj).addClass('gd-inv-act');
	}
}