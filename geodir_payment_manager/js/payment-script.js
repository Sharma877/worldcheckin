/**
 * jQuery.fn.sortElements
 * --------------
 * @author James Padolsey (http://james.padolsey.com)
 * @version 0.11
 * @updated 18-MAR-2010
 * --------------
 * @param Function comparator:
 *   Exactly the same behaviour as [1,2,3].sort(comparator)
 *   
 * @param Function getSortable
 *   A function that should return the element that is
 *   to be sorted. The comparator will run on the
 *   current collection, but you may want the actual
 *   resulting sort to occur on a parent or another
 *   associated element.
 *   
 *   E.g. $('td').sortElements(comparator, function(){
 *      return this.parentNode; 
 *   })
 *   
 *   The <td>'s parent (<tr>) will be sorted instead
 *   of the <td> itself.
 */
 if(jQuery.fn.sortElements){}else{
jQuery.fn.sortElements = (function(){
    
    var sort = [].sort;
    
    return function(comparator, getSortable) {
        
        getSortable = getSortable || function(){return this;};
        
        var placements = this.map(function(){
            
            var sortElement = getSortable.call(this),
                parentNode = sortElement.parentNode,
                
                // Since the element itself will change position, we have
                // to have some way of storing it's original position in
                // the DOM. The easiest way is to have a 'flag' node:
                nextSibling = parentNode.insertBefore(
                    document.createTextNode(''),
                    sortElement.nextSibling
                );
            
            return function() {
                
                if (parentNode === this) {
                    throw new Error(
                        "You can't sort elements if any one is a descendant of another."
                    );
                }
                
                // Insert before flag:
                parentNode.insertBefore(this, nextSibling);
                // Remove flag:
                parentNode.removeChild(nextSibling);
                
            };
            
        });
       
        return sort.call(this, comparator).each(function(i){
            placements[i].call(getSortable.call(this));
        });
        
    };
    
})();
 }

jQuery(document).ready(function(){

	jQuery('.delete_package').click(function(){
		
		var nonce = jQuery(this).attr('nonce');
		
		if(confirm(geodir_payment_all_js_msg.geodir_want_to_delete_price))
		{
			window.location.href=geodir_payment_all_js_msg.geodir_payment_admin_ajax_url+"?action=geodir_payment_manager_ajax&action_del=true&pagetype=delete&id="+jQuery(this).attr('package_id')+"&_wpnonce="+nonce;
			return true;
		}else
		{
			return false;
		}	
	});
	
	
	jQuery("#sub_units_num_times").blur(function(){
		if(jQuery(this).val()=='0' || jQuery(this).val()=='1'){
 		 alert(geodir_payment_all_js_msg.geodir_payment_recur_times_msg);
 		}
	});
	
	
	jQuery('#coupon_submit').click(function(){
	
		var coupon_code = jQuery.trim(jQuery('#coupon_code').val());
		var discount_amount = jQuery.trim(jQuery('#discount_amount').val());
		var post_type = jQuery('#post_type');
		
		var errors = '';
		
		if(coupon_code == ''){
			errors = geodir_payment_all_js_msg.geodir_payment_coupon_code+'\n';
		}
		
		if(jQuery('#post_type').find("option:selected").length == 0){
			errors += geodir_payment_all_js_msg.geodir_payment_select_post_type+'\n';
		}
		
		if(discount_amount == ''){
			errors += geodir_payment_all_js_msg.geodir_payment_enter_discount+'\n';
		}
		
		if(errors != ''){
			alert(errors);return false;
		}
	
	});
	
	
	jQuery('.delete_coupon').click(function(){
		
		var nonce = jQuery(this).attr('nonce');
		
		if(confirm(geodir_payment_all_js_msg.geodir_payment_delete_coupon))
		{
			window.location.href=geodir_payment_all_js_msg.geodir_payment_admin_ajax_url+"?action=geodir_payment_manager_ajax&coupon_del=true&pagetype=delete&id="+jQuery(this).attr('coupon_id')+"&_wpnonce="+nonce;
			return true;
		}else
		{
			return false;
		}

	});
	
	
	jQuery('#allow_coupon_code').click(function(){
																							
		var nonce = jQuery('#allow_coupon_code_nonce').val();
		window.location.href=geodir_payment_all_js_msg.geodir_payment_admin_ajax_url+"?action=geodir_payment_manager_ajax&allow_coupon=true&value="+jQuery('.geodir_allow_coupon_code:checked').val()+"&_wpnonce="+nonce;
		
	});
	
	
	jQuery('select[class="payment_gd_posting_type"]').change(function(){
	
		var post_type = jQuery(this).val();
		var pkg_id = jQuery('input[name="gd_id"]').val();
		var cat = jQuery('input[name="gd_exc_package_cat"]').val();
		var ajax_datas = '1';
		
		jQuery.post(geodir_payment_all_js_msg.geodir_payment_admin_ajax_url+'?action=geodir_payment_manager_ajax', { post_type: post_type, pkg_id:pkg_id, cats:cat, payment_ajax_data:ajax_datas })
		.done(function(data) {
									 
			var data = jQuery.parseJSON(data);
			jQuery('#show_fields').html(data.posttype);
			jQuery('#show_categories').html(data.html_cat);
			jQuery('#gd_downgrade_pkg').html(data.downgrade);
			
		});
	
	});
	
	/* Recurring payment --- */
	jQuery('#payment_sub_active').click( function() {
		if(jQuery('#payment_sub_active').is(':checked')){
			jQuery('.show_recuring').show();
			jQuery('.show_num_days').hide();
		}else{
			jQuery('.show_recuring').hide();
			jQuery('.show_num_days').show();
		}	
	});
	
	if(jQuery('#payment_sub_active').is(':checked')){
		jQuery('.show_recuring').slideDown(500);
		jQuery('.show_num_days').hide();
	}else{
		jQuery('.show_recuring').slideUp(500);
		jQuery('.show_num_days').show();
	}
	
	jQuery("#recurring_range").change(function() {
		var val=$("#recurring_range").val();
					
		if(val=='D')
		{
			var $i, args; 
			for(a=1;a<=90; a++)
			{
				args += "<option value='"+ a +"'>" + a + "</option>";
			}
			jQuery("#rangenumber").html(args); 
			jQuery("#subscription").html("<b>Day(s)</b>"); 
		}
		
		if(val=='W')
		{
			var a, args; 
			for(a=1;a<=52; a++)
			{
				args += "<option value='"+ a +"'>" + a  +"</option>";
			}
			jQuery("#rangenumber").html(args); 
			jQuery("#subscription").html("<b>Week(s)</b>");
		}
		
		if(val=='M')
		{
			var a, args; 
			for(a=1;a<=24; a++)
			{
				args += "<option value='"+ a +"'>" + a +"</option>";
			}
			jQuery("#rangenumber").html(args); 
			jQuery("#subscription").html("<b>Month(s)</b>"); 
		}
		
		if(val=='Y')
		{
			var a, args; 
			for(a=1;a<=5; a++)
			{
				args += "<option value='"+ a +"'>" + a +"</option>";
			}	   
			jQuery("#rangenumber").html(args); 
			jQuery("#subscription").html("<b>Year(s)</b>");
		}
		 
	});
	
	
});

function check_frm()
{
	if(document.getElementById('title').value=='')
	{
		alert(geodir_payment_all_js_msg.geodir_payment_enter_title);
		document.getElementById('title').focus();
		return false;
	}
	return true;
}

function gd_payment_send_invoice(invoice_id, nonce) {
	if (!invoice_id || !nonce) {
		return false;
	}
	
	if (!confirm(geodir_payment_all_js_msg.geodir_send_invoice_confirm)) {
		return false;
	}
	
	var row = jQuery('tr#invoiceid-' + invoice_id);
	
	jQuery.ajax({
		url: geodir_payment_all_js_msg.geodir_payment_admin_ajax_url,
		type: 'POST',
		dataType: 'json',
		data: 'action=geodir_payment_manager_ajax&task=invoice_send&invoice_id=' + invoice_id + '&_wpnonce=' + nonce,
		beforeSend: function() {
			console.log('beforeSend');
			jQuery(row).css({'opacity':'.5'});
		},
		success: function(data, textStatus, xhr) {
			console.log('success');
			if ( data && typeof(data) == 'object' ) {
				if (data.msg) {
					alert(data.msg);
				}
			}
		},
		error: function(xhr, textStatus, errorThrown) {
			console.log('error');
			console.log(errorThrown);
		},
		complete: function(xhr, textStatus) {
			console.log('complete');
			jQuery(row).css({'opacity':'1'});
		}
	});
}