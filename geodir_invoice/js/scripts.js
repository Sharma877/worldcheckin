// JavaScript Document
jQuery(document).ready(function($){

	$('#mainform.geodirinvoice_tt').on('click', 'input[type="button"]', function(){
		$('div.api_console').empty();
		$('input[name="api_trigger"]').val($(this).attr('name'));
		var url = $('#mainform.geodirinvoice_tt').attr('action');
		//console.log(url);
		var params = $('#mainform.geodirinvoice_tt').serialize();
		//console.log(params);
		$('#io_logo').clone().appendTo('div.api_console').show();
		$.post(url, params, function(resp){
			/*resp = $.parseJSON(resp);
			$.each(resp, function(i, v){
				$('div.api_console').append(i+' => '+v+' <br>');
			});*/
			$('div.api_console').append('<br>'+resp);
		
		});
		
	});
	
});