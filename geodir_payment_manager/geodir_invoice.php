<?php
	/*
		Plugin Name: GeoDirectory Invoice
		Plugin URI: http://www.worldcheckin.com
		Description: Worldcheckin invoice manager.
		Version: 1.0
		Author: WCI DEV
		Author URI: https://profiles.wordpress.org/fahadmahmood#content-plugins		
	*/
session_start();
 global $wpdb;
 // $table_name = $wpdb->'ms_1_geodir_countries';
$error=array();
	global $ginvoice;
	
	add_action( 'admin_enqueue_scripts', 'geodir_invoice_scripts' );
	add_action( 'wp_enqueue_scripts', 'geodir_invoice_scripts' );
	
	function geodir_invoice_scripts(){
	
		wp_enqueue_script('geodir-invoice-js', plugins_url( 'js/scripts.js', (__FILE__)), 'jquery', '', true);
		
		wp_enqueue_script('geodir-invoice1', plugins_url( 'js/multiselect.min.js', (__FILE__)), 'jquery', '', true);
		wp_enqueue_script('geodir-invoice', plugins_url( 'js/multi.js', (__FILE__)), 'jquery', '', true);
		wp_enqueue_style('geodir-invoice-css', plugins_url( 'css/styles.css', (__FILE__)));
		
	}
	
	function geodir_admin_invoice_tabs($tabs){
		
		global $ginvoice;
		
		$total_unaction_invoices = '';
		if(function_exists('geodir_unactioned_invoices') && geodir_unactioned_invoices())
		{
		
			$total_unaction_invoices = '<span id="unaproved_reviews">'.geodir_unactioned_invoices().'</span>';
		}
		
		$tabs['ginvoice_fields'] = 
			array( 
			'label' =>__( 'Billings <span id="">'.$total_unaction_invoices.'</span> <small>('.ucwords($ginvoice['api_status']).' Mode)</small>', 'geodirinvoice' ),
			'subtabs' => array(
				array('subtab' => 'geodirinvoice_settings',
					'label' =>__( 'Settings', 'geodirinvoice'),
					'form_action' => admin_url('admin-ajax.php?action=geodir_invoice_ajax_action')),
				array('subtab' => 'geodirinvoice_templates',
					'label' =>__( 'Templates', 'geodirinvoice'),
					'form_action' => admin_url('admin-ajax.php?action=geodir_invoice_ajax_action')),
				array('subtab' => 'geodirinvoice_api',
					'label' =>__( 'Billings API', 'geodirinvoice'),
					'form_action' => admin_url('admin-ajax.php?action=geodir_invoice_ajax_action')),
				array('subtab' => 'geodirinvoice_reports',
					'label' =>__( 'Reports', 'geodirinvoice'),
					'form_action' => admin_url('admin-ajax.php?action=geodir_invoice_ajax_action')),
				array('subtab' => 'geodirinvoice_stats',
					'label' =>__( 'Statistics', 'geodirinvoice'),
					'form_action' => admin_url('admin-ajax.php?action=geodir_invoice_ajax_action')),
				array('subtab' => 'geodirinvoice_ie',
					'label' =>__( 'Import/Export', 'geodirinvoice'),
					'form_action' => admin_url('admin-ajax.php?action=geodir_invoice_ajax_action')
				)
			)
		);
		
		if($ginvoice['api_status']=='test'){
			//pre($tabs['ginvoice_fields']);
			$tabs['ginvoice_fields']['subtabs'][] = array(
														'subtab' => 'geodirinvoice_tt',
														'label' =>__( 'Test Triggers', 'geodirinvoice'),
														'form_action' => admin_url('admin-ajax.php?action=geodir_invoice_ajax_action'));
		}
		//pre($tabs['ginvoice_fields']);
		return $tabs; 
	}

	
	add_action('admin_init', 'geodir_admin_invoice_listing_init');

	if(!function_exists('pre')){
	function pre($data){
			if(isset($_GET['debug'])){
				pree($data);
			}
		}	 
	} 	
	if(!function_exists('pree')){
	function pree($data){
				echo '<pre>';
				print_r($data);
				echo '</pre>';	
		
		}	 
	} 
		
	function geodir_admin_invoice_listing_init() 
	{
		global $ginvoice;
		include('inc/InvoiceOcean.php');
		include('inc/InvoiceOceanClient.php');		
		$ginvoice = get_option('ginvoice');
		
		pre($ginvoice);
		
		if(is_admin()):
			add_filter('geodir_settings_tabs_array','geodir_admin_invoice_tabs' , 3); 
			add_action('geodir_admin_option_form', 'geodir_admin_invoice_tabs_data',4);
			add_action('geodir_before_admin_panel' , 'geodir_display_invoice_messages');
		endif;
			
		add_action('wp_ajax_geodir_invoice_ajax_action', "geodir_invoice_ajax_action");
		
		add_action( 'wp_ajax_nopriv_geodir_invoice_ajax_action', 'geodir_invoice_ajax_action' );			
	}
	
	function geodir_admin_invoice_tabs_data(){
		
		
		
		if(
			(isset($_REQUEST['tab']) && $_REQUEST['tab']=='ginvoice_fields') 
			&& 
			(isset($_REQUEST['subtab']) && $_REQUEST['subtab']!='')
		)// == 'geodirinvoice_settings' )
		{
			global $ginvoice;
			
			switch($_REQUEST['subtab']){
				
				default:
					
					if(isset($_GET['io'])){
						invoice_ocean();
					}
					
					add_action('geodir_admin_option_form', $_REQUEST['subtab'].'_form');
					
					$file = 'templates/'.$_REQUEST['subtab'].'.php';
					echo '<div class="invoice_forms">';
					//include($file);		
					echo '</div>';
								
				break;
				
			}
			
		}
		
	}
/*----------------------Setting tab----------------------------------------*/
	function geodirinvoice_settings_form(){
		 global $wpdb;
  $table_name = $wpdb->prefix.'geodir_countries';
 // echo $table_name;

echo "<table class='form-table' style='width: 600px'><div class='gd-content-heading'><h3>Country Setting</h3></div><tr><td  colspan='2'><h3>Site Countries: </h3></td>";
 $sql= "SELECT * FROM `". $table_name ."`";
	    $result = $wpdb->get_results($sql);
	   //print_r($result);
	   echo "<td  colspan='2'><select id='allcunt'>";
	   foreach($result as $rs){
		   $sql3= 'SELECT * FROM `wp_country_settings`';
	    $result3 = $wpdb->get_results($sql3);
	   
	   
	
	   foreach($result3  as $rs3){ echo  $x = $rs3->site_country_names ;}
	   
	  echo $y =$rs->Country;
	 
		   echo "<option value='". $rs->Country."'"; if ( $y === $x){ echo "selected ='selected'"; } 
		   echo " >";
		   echo $rs->Country ;
		   echo "</option>";
		   }
        echo "</select></td></tr>";
       
     echo "
      <tr><td><h3>Europian Union Countries: </h3> <a href='http://europa.eu/about-eu/countries/member-countries/index_en.htm'>
      (Eu Countries are in use)</a>";   
        $sql1= "SELECT * FROM `". $table_name ."` WHERE `MapReference` LIKE 'Europe ' ";
      
	    $result1 = $wpdb->get_results($sql1);
	   //print_r($result);
	  
	   echo ' <div class="row">
    <div class="col-xs-5">
    <select name="from[]" class="js-multiselect form-control" multiple="multiple" style="width: 186px; height: 164px">  ';
	   foreach($result1 as $rs1){
		   echo "<option value='". $rs1->Country."'>";
		   echo $rs1->Country ;
		   echo "</option>";
		   }
        echo "</select> </div></td>";
      echo '<div class="col-xs-2"><td> 
    <button type="button" id="js_right_Selected_1" class="button"><i class="glyphicon glyphicon-chevron-right">></i></button>
    <button type="button" id="js_left_Selected_1" class="button"><i class="glyphicon glyphicon-chevron-left"><</i></button>
    
  </td>  </div>
    ';  
    echo '<td><div class="col-xs-5"><h3>Chosen Countries</h3><br/>';
  echo ' <br/><select name="to[]" id="js_multiselect_to_1" class="form-control" size="8" multiple="multiple" style="width: 186px; height: 164px">';
  
 $sql3= "SELECT * FROM `wp_country_settings`";
	    $result3 = $wpdb->get_results($sql3);
	   //print_r($result3);
	   
	
	   foreach($result3  as $rs3){
		      $json_object = $rs3->eu_country_names;
	   
	$value_json = json_decode($json_object);
	foreach($value_json as $val_j){
		   echo "<option value='". $val_j."'>";
		   echo $val_j;
		   echo "</option>";
		   }
		   }
       
  echo' </select>';
    echo '</div>
    </div></td></tr><tr><td><input type="button" onclick="sendEnteries();" name="addcont" value="Save" class="button button-primary"></td></tr></tbody</table>';
    echo '<table class="form-table" style="width: 600px">
    <div class="gd-content-heading"><h3>Currency Conversion</h3></div>
      <tbody><tr>
        <td>';
        
        $cur = file_get_contents('https://openexchangerates.org/api/currencies.json?app_id=9619ab9e004e41089ca7db750eb2458b');

$value_cur = json_decode($cur);


 echo '<select name="cur[]" class="form-control" id="code_amount">  ';
	   foreach($value_cur as $currency_code => $currency_name){
		   echo "<option value='".$currency_code."'>";
		   echo $currency_name;
		   echo "</option>";
		   }
        echo "</select> </td>";
        
        echo '</td> <td><input type="text" name="amount" id="amount" value="" required="required"></td> </tr>';
        
       echo '<tr>
        <td><select name="to_code[]" class="form-control" id="to_code">  ';
	   foreach($value_cur as $currency_code => $currency_name){
		   echo "<option value='".$currency_code."'>";
		   echo $currency_name ;
		   echo "</option>";
		   }
        echo "</select> </td>"; 
       echo "<td><input type='text' name='resultamount' id='resultamount' value='' readonly> </td> </tr>";
        $sql4= "SELECT * FROM `wp_vat_settings`";
	    $result4 = $wpdb->get_results($sql4);
	   //print_r($result3);
	   
	
	   foreach($result4  as $rs4){
     //echo "  <tr><td><input type='text' name='text_key' value='".$rs4->vat_api."' id='text_key'></td></tr>";
   echo'  <tr>
        <th width="200">API Key of openexchangerates.org</th>
        <td><input type="text" title="" value="'.$rs4->vat_api.'" name="vat_api"  id="vat_api" placeholder="9619ab9e004e41089ca7db750eb2458b" required="required"></td><td><input type="button" onclick="sendapi();" name="addapi" value="Update Api" class="button button-primary"></td>
    </tr>';
 }
   echo "<tr><td><input type='button' onclick='sendEnteriess();' name='cur_convert' value='Conversion' class='button button-primary'></td></tr></tbody></table>"; 
      
      
//        $sql4= "SELECT * FROM `wp_vat_settings`";
//	    $result4 = $wpdb->get_results($sql4);
//	   //print_r($result3);
//	   
//	
//	   foreach($result4  as $rs4){
//    echo '<table class="form-table" style="width: 600px">
//    <div class="gd-content-heading"><h3>Vat Setting</h3></div>
//      <tbody><tr>
//        <td></td>
//        <td></td>
//        <th>Private Tax active</th>
//        <th>Business Tax active</th>
//        <th>Private fields</th>
//        <th>Business fields</th>                
//      </tr>      
//      <tr>
//        <th width="200">Percentage VAT for National users</th>
//        <td><input type="text" title="" value="'. $rs4->national_vat.'" name="national_vat" id="national_vat" palceholder="Enter Vat" required="required"></td>
//        <td><select name="ntax[]" id="national_tax">
//        <option ';
//        if ($rs4->national_tax=='YES'){ echo "selected ='selected'";}
//        echo 'value="YES">YES</option>
//        <option value="NO"';
//        if ($rs4->national_tax=='NO'){ echo "selected ='selected'";}
//        echo ' >NO</option>
//        <option value="Optional"';
//        if ($rs4->national_tax=='Optional'){ echo "selected ='selected'";}
//        echo '>Optional</option>
//        </select></td>
//        <td><select name="nactive[]" id="national_active">
//        <option value="YES"';
//        if ($rs4->national_tax_active=='YES'){ echo "selected ='selected'";}
//        echo '>YES</option>
//        <option value="NO"';
//        if ($rs4->national_tax_active=='NO'){ echo "selected ='selected'";}
//        echo '>NO</option>
//        <option value="Optional"';
//        if ($rs4->national_tax_active=='Optional'){ echo "selected ='selected'";}
//        echo '>Optional</option>
//        </select></td>
//        
//        <td><select name="nprivate[]" id="national_private">
//        <option  value="TIN"';
//        if ($rs4->national_private_field=='TIN'){ echo "selected ='selected'";}
//        echo '>TIN</option>
//        <option value="Vat"';
//        if ($rs4->national_private_field=='Vat'){ echo "selected ='selected'";}
//        echo '>VAT</option>
//        <option value="Tax_ID"';
//        if ($rs4->national_private_field=='Tax_ID'){ echo "selected ='selected'";}
//        echo '>Tax-ID</option><
//        /select></td>
//        
//        <td><select name="nbusiness" id="national_business">
//        <option value="TIN"';
//        if ($rs4->national_business_field=='TIN'){ echo "selected ='selected'";}
//        echo '>TIN</option>
//        <option  value="Vat"';
//        if ($rs4->national_business_field=='Vat'){ echo "selected ='selected'";}
//        echo '>VAT</option>
//        <option value="Tax_ID"';
//        if ($rs4->national_business_field=='Tax_ID'){ echo "selected ='selected'";}
//        echo '>Tax-ID</option>
//        </select></td>        
//        
//      </tr>
//      
//      <tr>
//        <th width="200">Percentage VAT for European users</th>
//       <td><input type="text" title="" value="'.$rs4->eu_vat.'" name="euvat" id="eu_vat" palceholder="Enter Vat" required="required"></td>
//        <td><select name="eutax[]" id="eu_tax">
//        <option value="YES"';
//        if ($rs4->eu_tax=='YES'){ echo "selected ='selected'";}
//        echo '>YES</option>
//        <option  value="NO"';
//        if ($rs4->eu_tax=='NO'){ echo "selected ='selected'";}
//        echo '>NO</option>
//        <option value="Optional"';
//        if ($rs4->eu_tax=='Optional'){ echo "selected ='selected'";}
//        echo '>Optional</option>
//        </select></td>
//        <td><select name="euactive[]" id="eu_active">
//        <option value="YES"';
//        if ($rs4->eu_tax_active=='YES'){ echo "selected ='selected'";}
//        echo '>YES</option>
//        <option  value="NO"';
//        if ($rs4->eu_tax_active=='NO'){ echo "selected ='selected'";}
//        echo '>NO</option>
//        <option value="Optional"';
//        if ($rs4->eu_tax_active=='Optional'){ echo "selected ='selected'";}
//        echo '>Optional</option>
//        </select></td>
//        
//        <td><select name="euprivate[]" id="eu_private">
//        <option value="TIN"';
//        if ($rs4->eu_private_field=='TIN'){ echo "selected ='selected'";}
//        echo '>TIN</option>
//        <option selected="" value="Vat"';
//        if ($rs4->eu_private_field=='Vat'){ echo "selected ='selected'";}
//        echo '>VAT</option>
//        <option value="Tax_ID"';
//        if ($rs4->eu_private_field=='Tax_ID'){ echo "selected ='selected'";}
//        echo '>Tax-ID</option><
//        /select></td>
//        
//        <td><select name="eubusiness" id="eu_business">
//        <option value="TIN"';
//        if ($rs4->eu_business_field=='TIN'){ echo "selected ='selected'";}
//        echo '>TIN</option>
//        <option selected="" value="Vat"';
//        if ($rs4->eu_business_field=='Vat'){ echo "selected ='selected'";}
//        echo '>VAT</option>
//        <option value="Tax_ID"';
//        if ($rs4->eu_business_field=='Tax_ID'){ echo "selected ='selected'";}
//        echo '>Tax-ID</option>
//        </select></td>           
//        
//      </tr>
//      <tr>
//        <th width="200">Percentage VAT for Non-European users</th>
//       <td><input type="text" title="" value="'.$rs4->non_eu_vat.'" name="non_euvat" id="non_euvat" palceholder="Enter Vat" required="required"></td>
//        <td><select name="non_eutax[]" id="non_eutax">
//        <option value="YES"';
//        if ($rs4->non_eu_tax=='YES'){ echo "selected ='selected'";}
//        echo '>YES</option>
//        <option selected="" value="NO"';
//        if ($rs4->non_eu_tax=='NO'){ echo "selected ='selected'";}
//        echo '>NO</option>
//        <option value="Optional"';
//        if ($rs4->non_eu_tax=='Optional'){ echo "selected ='selected'";}
//        echo '>Optional</option>
//        </select></td>
//        <td><select name="non_euactive[]" id="non_euactive">
//        <option value="YES"';
//        if ($rs4->non_eu_tax_active=='YES'){ echo "selected ='selected'";}
//        echo '>YES</option>
//        <option  value="NO"';
//        if ($rs4->non_eu_tax_active=='NO'){ echo "selected ='selected'";}
//        echo '>NO</option>
//        <option value="Optional"';
//        if ($rs4->non_eu_tax_active=='Optional'){ echo "selected ='selected'";}
//        echo '>Optional</option>
//        </select></td>
//        
//        <td><select name="non_euprivate[]" id="non_euprivate">
//        <option selected="" value="TIN"';
//        if ($rs4->non_eu_private_field=='TIN'){ echo "selected ='selected'";}
//        echo '>TIN</option>
//        <option value="Vat"';
//        if ($rs4->non_eu_private_field=='Vat'){ echo "selected ='selected'";}
//        echo '>VAT</option>
//        <option value="Tax_ID"';
//        if ($rs4->non_eu_private_field=='Tax_ID'){ echo "selected ='selected'";}
//        echo '>Tax-ID</option><
//        /select></td>
//        
//        <td><select name="non_eubusiness" id="non_eubusiness">
//        <option selected="" value="TIN"';
//        if ($rs4->non_eu_business_field=='TIN'){ echo "selected ='selected'";}
//        echo '>TIN</option>
//        <option value="Vat"';
//        if ($rs4->non_eu_business_field=='Vat'){ echo "selected ='selected'";}
//        echo '>VAT</option>
//        <option value="Tax_ID"';
//        if ($rs4->non_eu_business_field=='Tax_ID'){ echo "selected ='selected'";}
//        echo '>Tax-ID</option>
//        </select></td>     
//      </tr>
//
//
//	<tr><td><input type="button" onclick="sendvat();" name="addvat" value="Add Vat" class="button button-primary"></td></tr>
//
//   
//
//      </tbody></table>';
	  
        $sql4= "SELECT * FROM `wp_global_business_vat_Settings`";
	    $result4 = $wpdb->get_row($sql4);
	  
		echo '<table border="1" class="form-table" style="width: 600px; margin-bottom: 10px;">
		<div class="gd-content-heading"><h3>Vat Settings</h3></div><tbody>';
		
			echo '<tr><td><b>Customer</b></td><td><b>Tax Reduction Enabled</b></td><td><b>Validation Fields</b></td></tr>';
			
			//echo '<tr><td>NATIONAL</td>';
			//	
			//				echo '<td><select name="nat_tax_red" id="nat_tax_red">';
			//				
			//				echo '<option value="Yes"';
			//				
			//					if ($result4->national_tr_enabled=='Yes'){ echo "selected ='selected'";}
			//				
			//				echo '>Yes</option><option value="No"';
			//				
			//					if ($result4->national_tr_enabled=='No'){ echo "selected ='selected'";}
			//				
			//				echo '>No</option></select></td>';
			//				
			//				
			//			
			//				echo '<td><select name="nat_validation_fields" id="nat_validation_fields">';
			//				
			//				echo '<option value="VAT-ID"';
			//				
			//					if ($result4->national_tax_validation=='VAT-ID'){ echo "selected ='selected'";}
			//				
			//				echo '>VAT-ID</option><option value="TAX-ID"';
			//				
			//					if ($result4->national_tax_validation=='TAX-ID'){ echo "selected ='selected'";}
			//				
			//				echo '>TAX-ID</option></select></td></tr>';
			
			echo '<tr><td>EUROPEAN</td>';
				
							echo '<td><select name="eur_tax_red" id="eur_tax_red">';
							
							echo '<option value="Yes"';
							
								if ($result4->european_tr_enabled=='Yes'){ echo "selected ='selected'";}
							
							echo '>Yes</option><option value="No"';
							
								if ($result4->european_tr_enabled=='No'){ echo "selected ='selected'";}
							
							echo '>No</option></select></td>';
						
							echo '<td><select name="eur_validation_fields" id="eur_validation_fields">';
							
							echo '<option value="VAT-ID"';
							
								if ($result4->european_tax_validation=='VAT-ID'){ echo "selected ='selected'";}
							
							echo '>VAT-ID</option><option value="TAX-ID"';
							
								if ($result4->european_tax_validation=='TAX-ID'){ echo "selected ='selected'";}
							
							echo '>TAX-ID</option></select></td></tr>';
					
			echo '<tr><td>INTERNATIONAL</td>';
						
							echo '<td><select name="int_tax_red" id="int_tax_red">';
							
							echo '<option value="Yes"';
							
								if ($result4->international_tr_enabled=='Yes'){ echo "selected ='selected'";}
							
							echo '>Yes</option><option value="No"';
							
								if ($result4->international_tr_enabled=='No'){ echo "selected ='selected'";}
							
							echo '>No</option></select></td>';
						
							echo '<td><select name="int_validation_fields" id="int_validation_fields">';
							
							echo '<option value="VAT-ID"';
							
								if ($result4->international_tax_validation=='VAT-ID'){ echo "selected ='selected'";}
							
							echo '>VAT-ID</option><option value="TAX-ID"';
							
								if ($result4->international_tax_validation=='TAX-ID'){ echo "selected ='selected'";}
							
							echo '>TAX-ID</option></select></td></tr>';
							
			echo '<tr><td><input type="button" onclick="sendvat();" name="addvat" value="Save Vat Settings" class="button button-primary"></td></tr>';				
		
		echo "</tbody></table>";
	  
	  
	  
	  
        $sql5= "SELECT * FROM `wp_global_vat_settings`";
	    $result5 = $wpdb->get_row($sql5);

		echo '<table class="form-table" style="width: 600px">
		<div class="gd-content-heading"><h3>Set Global Tax</h3></div><tbody>';
		
			echo '<tr><td>';
			
				echo 'Calculate Taxable Subtotal : <select id="calculate_taxable_subtotal" name="calculate_taxable_subtotal">
				<option value="add"';
				if ($result5->calc_taxable=='add'){ echo "selected ='selected'";}
				echo '>Add To Amount</option>
				
				<option value="subtract"';
				if ($result5->calc_taxable=='subtract'){ echo "selected ='selected'";}
				echo '>Subtract From Amount</option>
				</select>';
			
			echo '</td></tr>';
			
			echo '<tr><td>';
			
				echo '<input type="checkbox" name="use_global_tax" id="use_global_tax"';
				if ($result5->use_global_tax=='true'){ echo "checked ='checked'";}
				echo '> Use global tax.';
			
			echo '</td></tr>';
			
			
			echo '<tr><td>';
			
				echo 'Tax Value : <input type="text" name="tax_value" id="tax_value" value="'.$result5->tax_value.'">% <br /> This will make all new invoices have default Tax value which can be changed for different invoice.';
			
			echo '</td></tr>';
			
			echo '<tr><td><input type="button" onclick="setglobaltax();" name="addvat" value="Set Global Tax" class="button button-primary"></td></tr>';
		
		echo "</tbody></table>"; 
		
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script>
	
	$( document ).ready(function(){
	$('input.mode').on('change', function() {
    $('input.mode').not(this).prop('checked', false);  
		});
	});


</script>




<?php



		$sql6= "SELECT * FROM `wp_global_invoiceocean_api`";
	    $result6 = $wpdb->get_results($sql6);

		echo '<table class="form-table" style="width: 600px">
		<div class="gd-content-heading"><h3>Add Invoice Ocean credentials</h3></div><tbody>';


			echo '<tr><td>';
			echo 'Test Mode  ';			
			echo '<input type="checkbox" name="mode_enabled1" id="mode_enabled1" class="mode"';
			if ($result6[0]->mode_enabled=='1'){ echo "checked ='checked'";
			echo '>';}

			
			echo '</td></tr>';
		
			echo '<tr><td>';
			
				echo 'Test - URL : <input type="text" name="hosturl" id="hosturl" value="'.$result6[0]->hosturl.'">';
			
			echo '</td></tr>';
			
			echo '<tr><td>';
			
				echo 'Test - Api Token : <input type="text" name="apitoken" id="apitoken" value="'.$result6[0]->apitoken.'">';
			
			echo '</td></tr>';
		
			echo '<tr><td>';
			
			echo ' Live Mode  ';
			echo '<input type="checkbox" name="mode_enabled2" id="mode_enabled2" class="mode"';
			if ($result6[1]->mode_enabled=='1'){ echo "checked ='checked'";
			echo '>';}
			
			echo '</td></tr>';
			
			echo '<tr><td>';
			
				echo 'Live - URL : <input type="text" name="hosturl" id="hosturl" value="'.$result6[1]->hosturl.'">';
			
			echo '</td></tr>';
			
			echo '<tr><td>';
			
				echo 'Live - Api Token : <input type="text" name="apitoken" id="apitoken" value="'.$result6[1]->apitoken.'">';
			
			echo '</td></tr>';

			
			echo '<tr><td><input type="button" onclick="invoiceoceanapi();" name="addvat" value="Add Api Credentials" class="button button-primary"></td></tr>';
		
		echo "</tbody></table>"; 
		
   
}

	//}
	/*------------------Billing tab--------------------------------*/
	function geodirinvoice_api_form(){ ?>
	
  <script src="<?php echo get_site_url(); ?>/wp-content/themes/GDF_child/geodirectory/jquery-ui.js"></script>
  <script src="<?php echo get_site_url(); ?>/wp-content/themes/GDF_child/jquery.blockUI.js"></script>	

		<script type="text/javascript">
			jQuery(document).ready(function(){
			
				jQuery('#post_type').change(function(){
			
					if (this.value != "") {
						
                            jQuery.blockUI({ css: {
                                border: 'none', 
                                padding: '15px', 
                                backgroundColor: '#000', 
                                '-webkit-border-radius': '10px', 
                                '-moz-border-radius': '10px', 
                                opacity: .5, 
                                color: '#fff' 
                            } });						
						
						var post_type = this.value;
						
						jQuery.ajax({
							url: ajaxurl,
							data:  { "action":"get_packages", "post_type" : post_type },
							type: 'POST',
							success: function(data){
								
								jQuery('.pk_rem').remove();
								jQuery("#package_data").html("");
								jQuery('#package_data').show();
								jQuery("#package_data").html(data);
								
								jQuery.unblockUI();
							}
						});
					}
					
				});
			});
			
			function get_package_details(id){
				
				if (id != "") {
					
                            jQuery.blockUI({ css: {
                                border: 'none', 
                                padding: '15px', 
                                backgroundColor: '#000', 
                                '-webkit-border-radius': '10px', 
                                '-moz-border-radius': '10px', 
                                opacity: .5, 
                                color: '#fff' 
                            } });						
				
						jQuery.ajax({
							url: ajaxurl,
							data:  { "action":"get_package_details", "package_id" : id },
							type: 'POST',
							success: function(data){
								//jQuery('#package_details').show();
								//jQuery("#package_details").html(data);
								
								jQuery('.pk_rem').remove();
								jQuery('#invoide_data tr:last').after(data);
								
								jQuery.unblockUI();
							}
						});
				}		
			}
		</script>

<?php		
//		
//Array
//(
//    [type] => paid
//    [post_id] => 1960
//    [post_title] => Add Listing: Dummy Invoice Test
//    [post_action] => add
//    [invoice_type] => add_listing
//    [invoice_callback] => add_listing
//    [invoice_data] => a:0:{}
//    [package_id] => 2
//    [package_title] => EVENT (Standard)
//    [amount] => 27.00
//    [alive_days] => 30
//    [expire_date] => 2016-02-28
//    [coupon_code] => 
//    [discount] => 0
//    [tax_amount] => 0.00
//    [paied_amount] => 27
//    [status] => pending
//    [is_current] => 1
//)





	//$data = apply_filters( 'geodir_payment_invoice_params', $data, false ); // false => create
	//
	//if ( isset( $data['id'] ) ) {
	//	unset( $data['id'] );
	//}
	//
	//$date = date_i18n( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
	//$data['date'] = $date;
	//
	//$data = wp_unslash( $data );
	//
	//if ( empty( $data ) ) {
	//	return NULL;
	//}
	//
	//if ( !isset( $data['user_id'] ) ) {
	//	$data['user_id'] = $current_user->data->ID;
	//}
 ?>
 	<style>
	.create_new_invoice > input {
		float: left;
		margin-left: 10px;
		margin-top: 5px;
		width: 50%;
	}
	

	.create_new_invoice {
		float: left;
		margin-bottom: 20px;
		width: 100%;
	}
	
	.add_new_invoice_h3{
		background-color: #e7e7e7;
		border-bottom: 1px solid #dedede;
		border-top: 1px solid #fff;
		color: #6d6d6d;
		font-size: 13px;
		margin: 0;
		padding: 10px;
		text-transform: uppercase;	
	}
	
	#package_data{
		display: none;
	}
	
	#package_details{
		display: none;
	}
	
	.odd > td {
		color: black !important;
	  }
	  tr {
		color: black;
	  }
	  .dataTables_filter {
		display: none;
	  }
	  
	  #period1 label {
		color: #000;
		float: left;
		font-weight: bold;
		width: 100px;
	  }
	  #period1 label select {float:left; padding:5px;}
	  
	  #period2 .small-label label  {
		color: #000;
		float: left;
		font-weight: bold;
		width: 100px;line-height: 30px;
	  }
	  
	  #period2 .small-label input  {
	  float:left; padding:5px;
	  }
	  #period2 .small-label {
		float: left;line-height: 30px;
		margin-right: 20px;
	  }
	  
	  #period2 {
		margin: 10px 0 20px;
	  }
	  #period1 {
		margin: 10px 0 20px;
	  }


	</style>
	<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.11/css/jquery.dataTables.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.1.2/css/buttons.dataTables.min.css">
	<script type="text/javascript" src="//cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.1.2/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.1.2/js/buttons.html5.min.js"></script>
	<script type="text/javascript" language="javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
	<script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.1.2/js/buttons.html5.min.js"></script>
	<script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.1.2/js/buttons.flash.min.js"></script>
	<script type="text/javascript" language="javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>


	<script type="text/javascript">

	$(document).ready(function() {
    // Setup - add a text input to each footer cell
    $('#myinvoice tfoot th p').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );
 
    // DataTable
    var table = $('#myinvoice').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'csv', 'excel', 'pdf'
        ]
    });
	
 
    // Apply the search
    table.columns().every( function () {
        var that = this;
 
        $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
				}
			} );
		} );
		$('.dt-button.buttons-csv.buttons-html5 > span').html('Export as CSV');
		
		$('.dt-button.buttons-excel.buttons-flash > span').html('Export as Excel');
		
		$('.dt-button.buttons-pdf.buttons-html5 > span').html('Export as PDF');
		
		$('#period').change(function(){
		/* setting currently changed option value to option variable */
		var option = $(this).find('option:selected').val();
		/* setting input box value to selected option value */
		//$('#showoption').val(option);
		//alert(option);
		//alert(window.location.href + "&" + option);
		window.location.assign(window.location.href + "&timeperiod=" + option)
		});

			
	} );
	
			function customdatefilter() {
			var from = $('#date_from').val();
			var to = $('#date_to').val();

			//alert(window.location.href + "&from=" + from + "&to=" + to);
			window.location.assign(window.location.href + "&from=" + from + "&to=" + to)
			
		}



	</script>
	
	<div class="small-label" style="" id="period1">
		<label>Date Filter</label>
				<div class="ui-select">
   			  	  <select class="form-control" id="period" name="period">
					<option value="all" <?php if(($_GET['timeperiod']) == "all") echo 'selected="selected"';?>>all</option>
					<option value="this_month" <?php if(($_GET['timeperiod']) === 'this_month') echo 'selected="selected"';?>>this month</option>
					<option value="last_month" <?php if(($_GET['timeperiod']) === 'last_month') echo 'selected="selected"';?>>last month</option>
					<option value="this_year" <?php if(($_GET['timeperiod']) === 'this_year') echo 'selected="selected"';?>>this year</option>
					<option value="last_year" <?php if(($_GET['timeperiod']) === 'last_year') echo 'selected="selected"';?>>last year</option>
					<option value="more" <?php if(($_GET['timeperiod']) === 'more') echo 'selected="selected"';?>>more...</option></select>
				</div>
   			</div>
	<link rel="stylesheet" href="<?php echo plugins_url('geodir_invoice/css/colorbox.css');?>" />
			<script type="text/javascript" src="<?php echo plugins_url('geodir_invoice/js/jquery.colorbox.js');?>"></script>
<script>
			$(document).ready(function(){
				$(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
			});
		</script>

			
			<?php
			
			
			global $wpdb;
			$sql7= "SELECT * FROM `wp_global_invoiceocean_api` WHERE `mode_enabled` = '1'";
			$result7 = $wpdb->get_row($sql7);
			
			$hostname = $result7->hosturl;
			$apitoken = $result7->apitoken;
			
			
			if(!$_GET["timeperiod"]){
			$url= "$hostname/invoices.json?api_token=$apitoken";		
			$ch = curl_init();		
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);		
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
			curl_setopt($ch, CURLOPT_URL,$url);		
			$result=curl_exec($ch);		
			curl_close($ch);		
			$finalresult = json_decode($result, true);
			
			$param = base64_encode($url);
			//add_menu_page( 'GD KBS INVOICE', 'Menu INVOICE', 'gd_kbs_inv', 'geodir_invoice/gd_show_inv.php', 'gd_kbs_inv_1');
			?>


			<table id="myinvoice">
				<thead>
				<tr>
					<th>Invoice Number</th>
					<th>Date</th>
					<th>Package</th>
					<th>Buyer Name</th>
					<th>Buyer Email</th>
					<th>Buyer Tax ID</th>
					<th>Price</th>
					<th>Vat</th>
					<th>Total</th>	
				</tr>
				</thead>
				<tfoot>
					<tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th><p>Total</p></th>	
					</tr>
				</tfoot>
				
				<tbody>
				
					<?php 
					$kbs_i = 1;
					foreach($finalresult as $fr){ 
						$newID = str_replace('/','-AA-',$fr["number"]);	
					?>
					<tr>
						<td><a class='iframe' href="<?php echo plugins_url('geodir_invoice/gd_show_inv.php?inv_id='.$newID.'&p='.$param);?>" id="inv-<?php echo $fr["number"]; ?>"><?php echo $fr["number"]; ?></a></td>
						<td><?php echo $fr["issue_date"]; ?></td>
						<td><?php echo $fr["product_cache"]; ?></td>
						<td><?php echo $fr["buyer_name"]; ?></td>
						<td><?php echo $fr["buyer_email"]; ?></td>
						<td><?php echo $fr["buyer_tax_no"]; ?></td>
						<td><?php echo $fr["price_net"]; ?></td>
						<td><?php echo $fr["price_tax"]; ?></td>
						<td><?php echo $fr["price_gross"]; ?></td>
				</tr>	
					<?php
					$kbs_i++;
					}
					?>

				</tbody>
				
			</table>
		
			<?php } elseif($_GET["timeperiod"] != 'all' && $_GET["timeperiod"] != 'more'){
				$timeperiod = $_GET["timeperiod"];
				
				$url="$hostname/invoices.json?period=$timeperiod&api_token=$apitoken";		
				$ch = curl_init();		
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);		
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
				curl_setopt($ch, CURLOPT_URL,$url);		
				$result=curl_exec($ch);		
				curl_close($ch);		
				$finalresult = json_decode($result, true);	
				?>


			<table id="myinvoice">
				<thead>
				<tr>
					<th>Invoice Number</th>
					<th>Date</th>
					<th>Package</th>
					<th>Buyer Name</th>
					<th>Buyer Email</th>
					<th>Buyer Tax ID</th>
					<th>Price</th>
					<th>Vat</th>
					<th>Total</th>	
				</tr>
				</thead>
				<tfoot>
					<tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th><p>Total</p></th>	
					</tr>
				</tfoot>
				
				<tbody>
				
					<?php foreach($finalresult as $key){ ?>
					<tr>
						<td><?php echo $key["number"]; ?></td>
						<td><?php echo $key["issue_date"]; ?></td>
						<td><?php echo $key["product_cache"]; ?></td>
						<td><?php echo $key["buyer_name"]; ?></td>
						<td><?php echo $key["buyer_email"]; ?></td>
						<td><?php echo $key["buyer_tax_no"]; ?></td>
						<td><?php echo $key["price_net"]; ?></td>
						<td><?php echo $key["price_tax"]; ?></td>
						<td><?php echo $key["price_gross"]; ?></td>
				</tr>	
					<?php
					}
					?>

				</tbody>
				
			</table>
			
			
			<?php } elseif($_GET["timeperiod"] === 'all'){
				
				$url="$hostname/invoices.json?&api_token=$apitoken";		
				$ch = curl_init();		
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);		
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
				curl_setopt($ch, CURLOPT_URL,$url);		
				$result=curl_exec($ch);		
				curl_close($ch);		
				$finalresult = json_decode($result, true);
				//
				//echo "<pre>";
				//print_r($finalresult);
				//echo "</pre>";
				?>


			<table id="myinvoice">
				<thead>
				<tr>
					<th>Invoice Number</th>
					<th>Date</th>
					<th>Package</th>
					<th>Buyer Name</th>
					<th>Buyer Email</th>
					<th>Buyer Tax ID</th>
					<th>Price</th>
					<th>Vat</th>
					<th>Total</th>	
				</tr>
				</thead>
				<tfoot>
					<tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th><p>Total</p></th>	
					</tr>
				</tfoot>
				
				<tbody>
				
					<?php foreach($finalresult as $key){ ?>
					<tr>
						<td><?php echo $key["number"]; ?></td>
						<td><?php echo $key["issue_date"]; ?></td>
						<td><?php echo $key["product_cache"]; ?></td>
						<td><?php echo $key["buyer_name"]; ?></td>
						<td><?php echo $key["buyer_email"]; ?></td>
						<td><?php echo $key["buyer_tax_no"]; ?></td>
						<td><?php echo $key["price_net"]; ?></td>
						<td><?php echo $key["price_tax"]; ?></td>
						<td><?php echo $key["price_gross"]; ?></td>
				</tr>	
					<?php
					}
					?>

				</tbody>
				
			</table>
			
			<?php } elseif($_GET["timeperiod"] === 'more'){ ?>
				
				<div style="" id="period2">
					<div class="small-label">
						<label>Date from :</label>
						<input type="text" class="form-control hasDatepicker" value="<?php if($_GET['from']){ echo $_GET['from'];}else{ echo date("Y-m-d");}?>" id="date_from" name="date_from">
						
								<script type="text/javascript">
								$(document).ready(function() {
								  $("#date_from").datepicker({dateFormat: 'yy-mm-dd',
								dayNamesMin: new Array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'),
								monthNames: new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'),
								regional: ["pl"]
							  });
								});
								</script>
		  
					</div>
					<div class="small-label">
						<label>Date to :</label>
						<input type="text" class="form-control hasDatepicker" value="<?php if($_GET['to']){ echo $_GET['to'];}else { echo date("Y-m-d");}?>" id="date_to" name="date_to">
						
								<script type="text/javascript">
								$(document).ready(function() {
								  $("#date_to").datepicker({dateFormat: 'yy-mm-dd',
								dayNamesMin: new Array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'),
								monthNames: new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'),
								regional: ["pl"]
							  });
								});
								</script>
		  
					</div>
					
					<input class="btn button-primary" type="button" value="Search" onclick="customdatefilter()">
				</div>
				
				<?php if($_GET['from'] && $_GET['to']){
					
					$from = $_GET['from']; $to = $_GET['to'];
					
				$url="$hostname/invoices.json?period=more&date_from=.'$from'.&date_to=.'$to'.&api_token=$apitoken";		
				$ch = curl_init();		
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);		
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
				curl_setopt($ch, CURLOPT_URL,$url);		
				$result=curl_exec($ch);		
				curl_close($ch);		
				$finalresult = json_decode($result, true);	
				?>
				<table id="myinvoice">
				<thead>
				<tr>
					<th>Invoice Number</th>
					<th>Date</th>
					<th>Package</th>
					<th>Buyer Name</th>
					<th>Buyer Email</th>
					<th>Buyer Tax ID</th>
					<th>Price</th>
					<th>Vat</th>
					<th>Total</th>	
				</tr>
				</thead>
				<tfoot>
					<tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th><p>Total</p></th>	
					</tr>
				</tfoot>
				
				<tbody>
				
					<?php foreach($finalresult as $key){ 
					
					?>
					<tr>
						<td><?php echo $key["number"]; ?></td>
						<td><?php echo $key["issue_date"]; ?></td>
						<td><?php echo $key["product_cache"]; ?></td>
						<td><?php echo $key["buyer_name"]; ?></td>
						<td><?php echo $key["buyer_email"]; ?></td>
						<td><?php echo $key["buyer_tax_no"]; ?></td>
						<td><?php echo $key["price_net"]; ?></td>
						<td><?php echo $key["price_tax"]; ?></td>
						<td><?php echo $key["price_gross"]; ?></td>
				</tr>	
					<?php
					}
					?>

				</tbody>
				
			</table>
			
				
				<?php } ?>

				

				
			<?php } ?>
			
			

<?php

	}
	
	function httpGet($url)
	{
		$ch = curl_init();  
	 
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	//  curl_setopt($ch,CURLOPT_HEADER, false); 
	 
		$output=curl_exec($ch);
	 
		curl_close($ch);
		return $output;
	}	
	
/*----------------------template tab----------------------------------------*/	
	function geodirinvoice_templates_form(){
	global $wpdb;
	  $tname = $table_name; 
	  $id = 1; 
	
	 
     $qr = "SELECT * FROM `wp_email_template`;";
	 $result_temp = $wpdb->get_row($qr);
     $et = $result_temp->email_template;
      $it = $result_temp->invoice_template;
      $euit = $result_temp->eu_invoice_template;
        $non_euit = $result_temp->non_invoice_template;
        $tax_popup = $result_temp->tax_popup_template;
        $rev_charge = $result_temp->rev_charge_template;
         $vat_doc = $result_temp->vat_doc_template;
  
    $etemplate= 'email_template';
	  $intemplate= 'invoice_template';
      $euintemplate= 'eu_invoice_template';
$non_intemplate= 'non_invoice_template';
$taxtemplate= 'tax_popup_template';
$revtemplate= 'rev_charge_template';
$vattemplate= 'vat_doc_template';
		   
		echo'<table class="form-table" style="width: 846px"><tbody><div class="gd-content-heading"><h3>Template Setting</h3></div><tr><th>Email Template</th><td style="width: 704px !important; float: left; padding: 0px">	';
				
				
				wp_editor($et, $etemplate); 
				echo '</td></tr>
				<tr><th>Invoice Format For Local</th><td style="width: 704px !important; float: left; padding: 0px">';
				wp_editor($it, $intemplate); 
			echo '</td>
			<tr><th>Invoice Format For Europian</th><td style="width: 704px !important; float: left; padding: 0px">	';
				
				
				wp_editor($euit, $euintemplate); 
				echo '</td></tr>
				<tr><th>Invoice Format For Non Europian</th><td style="width: 704px !important; float: left; padding: 0px">	';
				
				
				wp_editor($non_euit, $non_intemplate); 
				echo '</td></tr>
				<tr><th>Tax IDs Popup Text</th><td style="width: 704px !important; float: left; padding: 0px">	';
				
				
				wp_editor($tax_popup, $taxtemplate); 
				echo '</td></tr>
				
				<tr><th>Reverse Charge Details (C)</th><td style="width: 704px !important; float: left; padding: 0px">	';
				
				
				wp_editor($rev_charge, $revtemplate); 
				echo '</td></tr>
				  <tr><th>VAT doc Upload details (C)</th><td style="width: 704px !important; float: left; padding: 0px">	';
				
				
				wp_editor($vat_doc, $vattemplate); 
				echo '</td></tr>
				<tr><td><input type="button" id="mail_template" onclick="addmailtemplate();" name="mail_template" value="Save" class="button button-primary"/>
			</td></tr></tbody></table>';

	}
	/*----------------------credit tab----------------------------------------*/	
	function geodirinvoice_credits_form(){
	global $wpdb;
		echo "<table style='text-align: center;
    width: 100%;'><tbody><tr><th>Id</th><th>User</th><th>Credit Amount</th><th>Credit Amount Info</th><th>Credit Amount Date</th><th>Status</th><th>Action</th></tr>";
     $cr = "SELECT * FROM `wp_credit_settings`;";
	    $credit = $wpdb->get_results($cr);

    foreach($credit as $credits){
		$cr_id =$credits->id;
    echo "<tr id='row_".$cr_id."'><td>".$cr_id."</td><td>".$credits->user_name."</td><td>".$credits->credit_amount."</td><td>".$credits->credit_amount_info."</td><td>".$credits->credit_amount_date."</td><td>".$credits->status."</td><td><button type='Button' id='cred_edit".$credits->id."' onclick='crededit(".$cr_id.");'>Edit</button>/<button type='Button' id='cred_delete".$credits->id."' onclick='creddelete(".$cr_id.");'>Delete</button></td></tr>
  
   <tr id='row_click_".$cr_id."' style='display:none'>
   <td>".$cr_id."</td>
   <td><input type='text' name='Usrname_".$cr_id."' id ='Usrname_".$cr_id."' value='".$credits->user_name."'></td>
   <td><input type='text' name='Usramount_".$cr_id."'  id ='Usramount_".$cr_id."' value='".$credits->credit_amount."'></td>
   <td><input type='text' name='Usrinfo_".$cr_id."' id ='Usrinfo_".$cr_id."' value='".$credits->credit_amount_info."'></td>
   <td><input type='text' name='Usrdate_".$cr_id."'  id ='Usrdate_".$cr_id."' value='".$credits->credit_amount_date."'></td>
   <td><input type='text' name='Usrstatus_".$cr_id."' id ='Usrstatus_".$cr_id."' value='".$credits->status."'></td>
   <td><button type='Button' id='cred_update".$credits->id."' onclick='credupdate(".$cr_id.");'>Update</button>
   </tr> 
    ";
    }
    echo "</tbody></table>";
		}
		/*----------------------refund tab----------------------------------------*/	
	function geodirinvoice_refunds_form(){
		
		global $wpdb;
		echo "<table style='text-align: center;
    width: 100%;'><tbody><tr><th>Id</th><th>User</th><th>Refund Amount</th><th>Refund Amount Info</th><th>Refund Amount Date</th><th>Status</th><th>Action</th></tr>";
     $rf = "SELECT * FROM `wp_refund_settings`;";
	    $refund = $wpdb->get_results($rf);

    foreach($refund as $refunds){
		$cr_id =$refunds->id;
    echo "<tr id='row_".$cr_id."'><td>".$cr_id."</td><td>".$refunds->user_name."</td><td>".$refunds->refund_amount."</td><td>".$refunds->refund_amount_info."</td><td>".$refunds->refund_amount_date."</td><td>".$refunds->status."</td><td><button type='Button' id='refund_edit".$refunds->id."' onclick='refundedit(".$cr_id.");'>Edit</button>/<button type='Button' id='refund_delete".$refunds->id."' onclick='refunddelete(".$cr_id.");'>Delete</button></td></tr>
  
   <tr id='row_click_".$cr_id."' style='display:none'>
   <td>".$cr_id."</td>
   <td><input type='text' name='Usrname_".$cr_id."' id ='Uname_".$cr_id."' value='".$refunds->user_name."'></td>
   <td><input type='text' name='Usramount_".$cr_id."'  id ='Uamount_".$cr_id."' value='".$refunds->refund_amount."'></td>
   <td><input type='text' name='Usrinfo_".$cr_id."' id ='Uinfo_".$cr_id."' value='".$refunds->refund_amount_info."'></td>
   <td><input type='text' name='Usrdate_".$cr_id."'  id ='Udate_".$cr_id."' value='".$refunds->refund_amount_date."'></td>
   <td><input type='text' name='Usrstatus_".$cr_id."' id ='Ustatus_".$cr_id."' value='".$refunds->status."'></td>
   <td><button type='Button' id='refund_update".$refunds->id."' onclick='refundupdate(".$cr_id.");'>Update</button>
   </tr> ";
    }
    echo "</tbody></table>";
		}
	
	if (!function_exists('geodir_auto_update_from_submit_handler')) {

		function geodir_auto_update_from_submit_handler()
		{
	
			if (isset($_REQUEST['geodir_invoice_update_settings'])) {
	
	
	
	
				$msg = __('Your settings have been saved.', 'geodirectory');
	
				$msg = urlencode($msg);
	
				$location = admin_url() . "admin.php?page=geodirectory&tab=ginvoice_fields&adl_success=" . $msg;
				wp_redirect($location);
				exit;
	
			}
	
		}
	}
	
	
	function geodir_invoice_ajax_action()
	{
		$ginvoice = $_POST['ginvoice'];
		$action = isset($_POST['action_at'])?$_POST['action_at']:'';
		$ginvoice = is_array($ginvoice)?$ginvoice:array();
		
		$ginvoice_db = get_option('ginvoice', $ginvoice);
		$ginvoice_db = is_array($ginvoice_db)?$ginvoice_db:array();
		//pree($ginvoice_db);
		//pree($ginvoice);
		
		$ginvoice  = array_merge($ginvoice_db, $ginvoice);
		//pree($ginvoice);
		//exit;
		update_option('ginvoice', $ginvoice);
	
		$msg = __('Your settings have been saved.', 'geodirectory');
		
		$msg = urlencode($msg);
		
		
		$location = $_SERVER['HTTP_REFERER']."&invoice_success=".$msg; //admin_url()."admin.php?page=geodirectory&tab=ginvoice_fields&subtab=geodirinvoice_settings&invoice_success=".$msg;
		
		$resp = array('msg'=>$msg, 'results'=>'');
		
		if(function_exists($action)){
			$resp['results'] = $action();
		}else{
			$resp['results'] .= '<br />Invalid Action';
		}
		
		if(!is_ajax())
		wp_redirect($location);
		else
		echo json_encode($resp);
		
		exit;
	
		
		
		
		
	}	
if ( ! function_exists( 'is_ajax' ) ) {
	function is_ajax(){
		return ((!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || isset($_GET['debug_ajax']));
	}
}	
	function is_ajax_call(){
		return (defined('DOING_AJAX') && DOING_AJAX);		
	}
			
	function geodir_display_invoice_messages(){
	
		if(isset($_REQUEST['invoice_success']) && $_REQUEST['invoice_success'] != '')
		{
				echo '<div id="message" class="updated fade"><p><strong>' . $_REQUEST['invoice_success'] . '</strong></p></div>';			
						
		}
		
		if(isset($_REQUEST['invoice_error']) && $_REQUEST['invoice_error'] != '')
		{
				echo '<div id="claim_message_error" class="updated fade"><p><strong>' . $_REQUEST['invoice_error'] . '</strong></p></div>';			
					
		}
	}
	
	function api_triggers(){
		global $ginvoice;
		if(isset($_POST['api_trigger']) && $_POST['api_trigger']!='' && $ginvoice['api_status']=='test')
		return invoice_ocean($_POST['api_trigger']);
		else
		return 'Trigger Failed';
	}
		
	function invoice_ocean($api_action){
		
		global $ginvoice;
		
		switch($ginvoice['api_status']){

			case 'test':
				$api_token = $ginvoice['credentials']['test']['api_token'];
			default:
			
			break;
			
			case 'live':
				$api_token = $ginvoice['credentials']['live']['api_token'];
			break;
			
		}
		
		
		$username = end(explode('/', $api_token));

		$invoice_id = 5954157;
		$client_id = 2409454;
		
		$io = new InvoiceOceanClient($username, $api_token);
		$client = array(
			'name'          => 'Chris Schalenborgh - '.rand(0, 1000).' - '.date('i'),
			'tax_no'        => date('i'),
			'bank'          => 'My Bank - '.date('i'),
			'bank_account'  => '001-123456-78',
			'city'          => 'Maasmechelen',
			'country'       => 'BE',
			'email'         => 'chris'.date('i').'@schalenborgh.be',
			'person'        => '',
			'post_code'     => '1234',
			'phone'         => '+32.123456789',
			'street'        => 'Street',
			'street_no'     => '123'
		);
		
		switch($api_action){
			
			case 'addClient':
					
				
				
				$result = $io->addClient($client);
				
				
					
			break;
			
			case 'getClients':
										
				
				$result = $io->getClients();
						
			break;
			
			case 'getClient':
										
				
				$result = $io->getClient($client_id);
						
			break;			
			
			case 'updateClient':
										
				
				$result = $io->updateClient($client_id, $client);
						
			break;	
			
			case 'getInvoice':
										
				
				$result = $io->getInvoice($invoice_id);
						
			break;	
			
			case 'addInvoice':
										
				
				$result = $io->addInvoice($invoice);
						
			break;		
			
			case 'updateInvoice':
										
				
				$result = $io->updateInvoice($invoice_id, $invoice);
						
			break;	
			
			case 'deleteInvoice':
										
				
				$result = $io->deleteInvoice($invoice_id);
						
			break;	
			
			case 'sendInvoice':
										
				
				$result = $io->sendInvoice($invoice_id);
						
			break;								
		}
	
		return $result;	
	}
	
	
	

	
