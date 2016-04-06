<img style="display:none" id="io_logo" src="<?php echo plugins_url( 'images/invoiceocean-logo-white.png', dirname(__FILE__)); ?>" />
<input type="hidden" name="action_at" value="api_triggers" id="action_at" />
<input type="hidden" name="api_trigger" value="" id="api_trigger" />
<?php 

	if(class_exists('InvoiceOceanClient')){
			
		$class_methods = get_class_methods('InvoiceOceanClient');
		
		if(!empty($class_methods)){
			array_shift($class_methods);
			$methods[] = '<ul class="class_methods">';
			foreach ($class_methods as $method_name) {
				$methods[] = '<li><input style="width:140px;" class="button" type="button" value="'.strtoupper($method_name).'" id="'.$method_name.'" name="'.$method_name.'" /></li>';
			}
			$methods[] = '</ul><div class="api_console"></div>';
			echo implode('', $methods);
		}else{
			
		}
	
	}else{
		
	}