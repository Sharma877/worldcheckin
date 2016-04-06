// JavaScript Document
    jQuery(document).ready(function($) {
		//alert('hello');
    $('.js-multiselect').multiselect({
		
    right: '#js_multiselect_to_1',
    rightAll: '#js_right_All_1',
    rightSelected: '#js_right_Selected_1',
    leftSelected: '#js_left_Selected_1',
    leftAll: '#js_left_All_1'
    });
    });

function sendEnteries(){
	

var select_entries = jQuery('#allcunt').val();
var assignedRoleId = new Array();
$('#js_multiselect_to_1 option').each(function(){
        assignedRoleId.push(this.value);
       
});

$.post( "../insert_entries.php", { entries: assignedRoleId , euentry: select_entries })
.done(function( data ) {
alert( "Your countries has been added." );
});


}
function sendEnteriess(){
	var from_code  = jQuery('#code_amount').val();
	
var amount  = jQuery('#amount').val();
var key = jQuery('#vat_api').val();

var to_code  = jQuery('#to_code').val();
if(!amount){alert('Please enter conversion amount'); return false;}
if(!key){alert('Please enter api key'); return false;}
else {
	 jQuery.ajax({url: "https://openexchangerates.org/api/convert/"+amount+"/"+from_code+"/"+to_code+"?app_id="+key, success: function(result){
       jQuery("#resultamount").html(result);
    }});
}
}

function sendvat(){
	
        //var national_tr_enabled  = jQuery('#nat_tax_red').val();
        var european_tr_enabled  = jQuery('#eur_tax_red').val();
        var international_tr_enabled = jQuery('#int_tax_red').val();
        
        //var national_tax_validation = jQuery('#nat_validation_fields').val();
        var european_tax_validation = jQuery('#eur_validation_fields').val();
        var international_tax_validation = jQuery('#int_validation_fields').val();
        
        
        $.post( "../insert_vat.php", { 'national_tr_enabled': national_tr_enabled, 'european_tr_enabled': european_tr_enabled, 'international_tr_enabled': international_tr_enabled, 'national_tax_validation': national_tax_validation, 'european_tax_validation': european_tax_validation, 'international_tax_validation': international_tax_validation })
        .done(function( data ) {
                alert( "Your vat settings has been updated." );
        });
















//var ntax  = jQuery('#national_tax').val();
//var nactive  = jQuery('#national_active').val();
//var nprivate  = jQuery('#national_private').val();
//var nbusiness  = jQuery('#national_business').val();
//
//var euvat  = jQuery('#eu_vat').val();
//
//
//var eutax  = jQuery('#eu_tax').val();
//var euactive  = jQuery('#eu_active').val();
//var euprivate  = jQuery('#eu_private').val();
//var eubusiness  = jQuery('#eu_business').val();
//
//var nonvat  = jQuery('#non_euvat').val();
//
//
//var nontax  = jQuery('#non_eutax').val();
//var nonactive  = jQuery('#non_euactive').val();
//var nonprivate  = jQuery('#non_euprivate').val();
//var nonbusiness  = jQuery('#non_eubusiness').val();
//
//
//
//if(!euvat){alert('Please enter Percentage VAT for European users'); return false;}
//else if(!nvat){alert('Please enter Percentage VAT for National users'); return false;}
//else if(!nonvat){alert('Please enter Percentage VAT for Non-European users'); return false;}
//else {
//
//
//
//$.post( "../insert_vat.php", { n_vat: nvat, n_tax: ntax, n_active: nactive, n_private: nprivate, n_business: nbusiness, eu_vat: euvat, eu_tax: eutax, eu_active: euactive, eu_private: euprivate, eu_business: eubusiness, non_vat: nonvat, non_tax: nontax, non_active: nonactive, non_private: nonprivate, non_business: nonbusiness})
//.done(function( data ) {
//alert( "Your vat has been added." );
//});
//}
}

function setglobaltax(){
        
  var taxable_subtotal = jQuery('#calculate_taxable_subtotal').val();
  var global_tax_checked = $('#use_global_tax:checkbox:checked').length > 0;
  var tax_value = jQuery('#tax_value').val();
  
                $.post( "../insert_vat.php", { 'taxable_subtotal': taxable_subtotal, 'global_tax_checked' : global_tax_checked, 'tax_value' : tax_value })
                        .done(function( data ) {
                                alert( "Your tax values has been saved." );
                        }
                );
}

function sendapi(){
	

var vat_api  = jQuery('#vat_api').val();
if(!vat_api){alert('Please enter API Key of openexchangerates.org'); return false;}
else{
$.post( "../insert_vat.php", { vat_api: vat_api })
.done(function( data ) {
	
	
alert( "Your api has been added." );
}); }
	}


function addmailtemplate(){
	var etemp  = jQuery('#email_template').val();
	var intemp  = jQuery('#invoice_template').val();
	var euintemp  = jQuery('#eu_invoice_template').val();
	var non_intemp  = jQuery('#non_invoice_template').val();
	var taxtemp  = jQuery('#tax_popup_template').val();
	var revtemp  = jQuery('#rev_charge_template').val();
	var vattemp  = jQuery('#vat_doc_template').val();

$.post( "../insert_template.php", { email_temp: etemp, invoice_temp: intemp, eu_invoice_temp: euintemp, non_invoice_temp: non_intemp, tax_temp: taxtemp, rev_temp: revtemp, vat_temp: vattemp, })
.done(function( data ) {
alert( "Your vat has been added." );
});
	}
	
function createnewInvoice(){
        
        var buyer_name = jQuery('#buyer_name').val();
        var buyer_tax_no = jQuery('#buyer_tax_no').val();
        var product_name = jQuery('#product_name').val();
        var tax_applied = jQuery('#tax_applied').val();
        var total_price_gross = jQuery('#total_price_gross').val();
        
        var api_token  = jQuery('#api_token').val();
        //alert(api_token);
        var acount_name  = jQuery('#acount_name').val();
        //alert(acount_name);
        
        var sell_date = jQuery('#sell_date').val();
        var issue_date = jQuery('#issue_date').val();
        var payment_to = jQuery('#payment_to').val();        
        
        
        if(!api_token){ alert('Please enter API token'); return false; }
        if(!acount_name){ alert('Please enter acount name'); return false; }
        if(!buyer_name){ alert('Please enter buyer name'); return false; }
        if(!product_name){ alert('Please enter product name'); return false; }
        if(!tax_applied){ alert('Please enter tax applied'); return false; }
        if(!total_price_gross){ alert('Please enter total gross price'); return false; }
        if(!buyer_tax_no){ alert('Please enter tax number'); return false; }
        if(!sell_date){ alert('Please select sell date'); return false; }
        if(!issue_date){ alert('Please select issue date'); return false; }
        if(!payment_to){ alert('Please select payment to date'); return false; }
        else{
                
                json_params = {
                "api_token": api_token,
                "invoice": {
                    "kind":"vat", 
                    "number": null, 
                    "sell_date": sell_date, 
                    "issue_date": issue_date, 
                    "payment_to": payment_to,
                    "buyer_name": buyer_name,
                    "buyer_tax_no": buyer_tax_no,
                    "positions":[
                        { "name": product_name, "tax": tax_applied, "total_price_gross": total_price_gross, "quantity":1 }
                    ]		
                }}
                //alert(JSON.stringify(json_params))
                endpoint = acount_name+'/invoices.json'
        
                $.ajax({
                  type: "POST",
                  url: endpoint,
                  data: json_params,
                  dataType: 'json',
                  success: function(data) { alert('invoice created! ' + data['number']); window.location.href = ""; },
                });
        }
}
	
	
    function showcreateInvoice(){
        jQuery('.create_new_invoice').show();
    }
    
	function createInvoice()
	{
var api_token  = jQuery('#api_token').val();
//alert(api_token);
var acount_name  = jQuery('#acount_name').val();
//alert(acount_name);
if(!api_token){alert('Please enter API token'); return false;}
if(!acount_name){alert('Please enter acount name'); return false;}
else{
		
		json_params = {
		"api_token": api_token,
		"invoice": {
			"kind":"vat", 
			"number": null, 
			"sell_date": "2016-01-08", 
			"issue_date": "2016-01-08", 
			"payment_to": "2016-01-15",
			"buyer_name": "Client1 SA",
			"buyer_tax_no": "5252445767",
			"positions":[
				{"name":"Produkt A1", "tax":23, "total_price_gross":10.23, "quantity":1},
				{"name":"Produkt A2", "tax":0, "total_price_gross":50, "quantity":2}
			]		
		}}
		//alert(JSON.stringify(json_params))
		endpoint = acount_name+'/invoices.json'

		$.ajax({
		  type: "POST",
		  url: endpoint,
		  data: json_params,
		  dataType: 'json',
		  success: function(data) { alert('invoice created! ' + data['number'])},
		});

}


	}

function addApi(){
	

var api_token  = jQuery('#api_token').val();

var acount_name  = jQuery('#acount_name').val();

 if(!api_token){alert('Please enter API token'); return false;}
if(!acount_name){alert('Please enter acount name'); return false;}

 if(!/^(https?|ftp):\/\//i.test(acount_name)) {
	 
var val = 'https://'+acount_name; 
$.post( "../insert_ocean_api.php", { 'apitoken': api_token, 'acountname': acount_name })
.done(function( data ) {
	
	
alert( "Your api has been added." );
});
}
else{

$.post( "../insert_ocean_api.php", { 'apitoken': api_token, 'acountname': acount_name })
.done(function( data ) {
	
	
alert( "Your api has been added." );
}); }
	}
	
function crededit(id)
{
var cr_id = id;	
$("#row_"+cr_id).hide();
$("#row_click_"+cr_id).show();
	}
	
function credupdate(id){
	var cr_id= id;
	var Usrname  = jQuery('#Usrname_'+cr_id).val();
	var Usramount = jQuery('#Usramount_'+cr_id).val();
	var Usrinfo  = jQuery('#Usrinfo_'+cr_id).val();
	var Usrdate  = jQuery('#Usrdate_'+cr_id).val();
	var Usrstatus  = jQuery('#Usrstatus_'+cr_id).val();
	

$.post( "../insert_credit.php", { id: cr_id, Uname: Usrname, Uamount: Usramount, Uinfo: Usrinfo, Udate: Usrdate, Ustatus: Usrstatus})
.done(function( data ) {
	alert("Credit has been updated.");
location.reload();


});
	
	}



function creddelete(id){
	var cr_id= id;
	
	

$.post( "../delete_credit.php", { id: cr_id})
.done(function( data ) {
	alert("Credit has been deleted.");
location.reload();


});
}

function refundedit(id)
{
var cr_id = id;	
$("#row_"+cr_id).hide();
$("#row_click_"+cr_id).show();
	}


function refunddelete(id){
	var cr_id= id;
	
	

$.post( "../delete_refund.php", { id: cr_id})
.done(function( data ) {
	alert("Refund has been deleted.");
location.reload();


});
}



	
function refundupdate(id){
	var cr_id= id;
	var Usrname  = jQuery('#Uname_'+cr_id).val();
	var Usramount = jQuery('#Uamount_'+cr_id).val();
	var Usrinfo  = jQuery('#Uinfo_'+cr_id).val();
	var Usrdate  = jQuery('#Udate_'+cr_id).val();
	var Usrstatus  = jQuery('#Ustatus_'+cr_id).val();
	

$.post( "../insert_refund.php", { id: cr_id, Uname: Usrname, Uamount: Usramount, Uinfo: Usrinfo, Udate: Usrdate, Ustatus: Usrstatus})
.done(function( data ) {
	alert("Refund data has been updated.");
location.reload();


});
	
	}
    
    
function invoiceoceanapi(){
        if ($("#mode_enabled1").is(":checked")) {
                var testmode = "1";
                var livemode = "0";
                $.post( "../add_invoiceocean_api.php", { 'testmode': testmode, 'livemode': livemode })
                        .done(function( data ) {
                                alert( "Test Mode Enabled" );
                        }
                );
        } else if($("#mode_enabled2").is(":checked")){
                var testmode = "0";
                var livemode = "1";
                $.post( "../add_invoiceocean_api.php", { 'testmode': testmode, 'livemode': livemode  })
                        .done(function( data ) {
                                alert( "Live Mode Enabled" );
                        }
                );        
        }

        
        
        
        }    

