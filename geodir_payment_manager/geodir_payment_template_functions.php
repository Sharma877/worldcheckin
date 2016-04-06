<?php
function geodir_package_price_list()
{
?>
<?php global $wpdb; ?>

<div class="gd-content-heading active">
	
<h3><?php _e('Geo Directory Manage Price', 'geodir_payments'); ?></h3>
	<p style="padding-left:15px;"><a href="<?php echo admin_url().'admin.php?page=geodirectory&tab=paymentmanager_fields&subtab=geodir_payment_manager&gd_pagetype=addeditprice'?>"><strong><?php _e('Add Price', 'geodir_payments'); ?></strong></a> </p>
<table style=" width:100%" cellpadding="5" class="widefat post fixed" id="gd_price_table" >
  <thead>
    <tr>
      <th width="80" align="left" id="gdtable_package_id" style="min-width:90px; cursor: pointer;" char-type="int"><strong><?php _e('Package ID', 'geodir_payments'); ?></strong></th>
      <th width="135" align="left" id="gdtable_title" style="cursor: pointer;"><strong><?php _e('Title', 'geodir_payments'); ?></strong></th>
      <th width="135" align="left" id="gdtable_post_type" style="cursor: pointer;"><strong><?php _e('Post Type', 'geodir_payments'); ?></strong></th>
      <th width="75" align="left"><strong><?php _e('Price', 'geodir_payments'); ?> (<?php if(get_option('geodir_currencysym')){echo  stripslashes(get_option('geodir_currencysym'));}else{echo '$';}?>)</strong></th>
      <th width="75" align="left"><strong><?php _e('Number Of Days', 'geodir_payments'); ?></strong></th>
      <th width="75" align="left"><strong><?php _e('Status', 'geodir_payments'); ?></strong></th>
      <th width="75" align="center" id="gdtable_display_order" style="cursor:pointer;" char-type="int"><strong><?php _e('Display Order', 'geodir_payments'); ?></strong></th>
      <th width="90" align="left"><strong><?php _e('Is Featured', 'geodir_payments'); ?></strong></th>
      <th width="60" align="left"><strong><?php _e('Action', 'geodir_payments'); ?></strong></th>
      <th align="left">&nbsp;</th>
    </tr>
	<?php
	$post_types = geodir_get_posttypes();
	$post_types_length = count($post_types);
	$format = array_fill(0, $post_types_length, '%s');
	$format = implode(',', $format);
	
	$pricesql = $wpdb->prepare("select * from ".GEODIR_PRICE_TABLE." WHERE post_type IN ($format)", $post_types);
	$priceinfo = $wpdb->get_results($pricesql);
	
	if( $priceinfo ) {	
		foreach( $priceinfo as $priceinfoObj ) {
			$number_of_days = $priceinfoObj->days;
			if ( $priceinfoObj->sub_active ) {
				$sub_num_trial_days = $priceinfoObj->sub_num_trial_days;
				$sub_num_trial_units = isset( $priceinfoObj->sub_num_trial_units ) && in_array( $priceinfoObj->sub_num_trial_units, array( 'D', 'W', 'M', 'Y' ) ) ? $priceinfoObj->sub_num_trial_units : 'D';
				
				$number_of_days = $sub_num_trial_days > 0 ? $sub_num_trial_days .' '. $sub_num_trial_units . '(r)' : $priceinfoObj->sub_units_num .' '. $priceinfoObj->sub_units . '(r)';
			}
	?>
    <tr>
      <td><?php echo $priceinfoObj->pid;?></td>
      <td><?php echo $priceinfoObj->title;?></td>
      <td><?php echo $priceinfoObj->post_type;?></td>
      <td><?php echo $priceinfoObj->amount;?></td>
      <td><?php echo $number_of_days;?></td>
      <td><?php if($priceinfoObj->status==1) _e("Active", 'geodir_payments'); else _e("Inactive", 'geodir_payments');?></td>
      <td align="center"><?php echo (isset($priceinfoObj->display_order) ? (int)$priceinfoObj->display_order : 0);?></td>
      <td><?php if($priceinfoObj->is_featured==1) _e("Yes", 'geodir_payments');?></td>
      <td><?php $nonce = wp_create_nonce( 'package_action_'.$priceinfoObj->pid ); ?>
        <a href="<?php echo admin_url().'admin.php?page=geodirectory&tab=paymentmanager_fields&subtab=geodir_payment_manager&gd_pagetype=addeditprice&id='.$priceinfoObj->pid;?>"> <img src="<?php echo plugins_url('',__FILE__); ?>/images/edit.png" alt="<?php _e('Edit Location', 'geodir_payments'); ?>" title="<?php _e('Edit Package', 'geodir_payments'); ?>"/> </a> &nbsp;&nbsp;
        <?php if( !$priceinfoObj->is_default ) { ?>
        <a class="delete_package" nonce="<?php echo $nonce;?>" package_id="<?php echo $priceinfoObj->pid;?>" href="javascript:void(0);"><img src="<?php echo plugins_url('',__FILE__); ?>/images/delete.png" alt="<?php _e('Delete Location', 'geodir_payments'); ?>" title="<?php _e('Delete Package', 'geodir_payments'); ?>" /></a>
        <?php } ?>
      </td>
      <td>&nbsp;</td>
    </tr>
    <?php
		}
	}
	?>
  </thead>
</table>
<script>
var table = jQuery('#gd_price_table');
jQuery('#gdtable_package_id strong, #gdtable_title strong, #gdtable_post_type strong, #gdtable_display_order strong').append(' <i class="fa fa-sort"></i>');

jQuery('#gdtable_package_id, #gdtable_title, #gdtable_post_type, #gdtable_display_order')
    .wrapInner('<span title="sort this column"/>')
    .each(function(){

        var th = jQuery(this),
            thIndex = th.index(),
            inverse = false;

        th.click(function(){

            table.find('td').filter(function(){

                return jQuery(this).index() === thIndex;

            }).sortElements(function(a, b){

                if( jQuery.text([a]) == jQuery.text([b]) )
                    return 0;
				
				var charType = jQuery(th).attr('char-type');	
				if (charType=='int') {
					var aa = parseInt(jQuery.text([a]));
					var bb = parseInt($.text([b]));
					return aa > bb ?
						inverse ? -1 : 1
						: inverse ? 1 : -1;
				}
				
				return jQuery.text([a]) > $.text([b]) ?
                    inverse ? -1 : 1
                    : inverse ? 1 : -1;

            }, function(){

                // parentNode is the element we want to move
                return this.parentNode; 

            });

            inverse = !inverse;

        });

    });
</script>
											
</div>

<?php
}
/* END Of Package Price table in backend */

function geodir_payment_get_sub_num_trial_units( $default = 'D', $options_html = true  ) {
	$options = array();
	$options['D'] = __( 'Day(s)', 'geodir_payments' );
	$options['W'] = __( 'Week(s)', 'geodir_payments' );
	$options['M'] = __( 'Month(s)', 'geodir_payments' );
	$options['Y'] = __( 'Years(s)', 'geodir_payments' );
	
	$return = $options;
	if ( $options_html ) {
		$return = '';
		foreach ( $options as $value => $label ) {
			$selected = $value == $default ? 'selected="selected"' : '';
			$return .= '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
		}
	}
	
	return $return;
}
/* Start of Package Price add/edit form */
function geodir_package_price_form()
{
	global $wpdb, $price_db_table_name;
	$priceinfo = array();
	if(isset($_REQUEST['id']) && $_REQUEST['id']!='')
	{
		$pid = $_REQUEST['id'];

		$pricesql = $wpdb->prepare("select * from ".GEODIR_PRICE_TABLE." where pid=%d",array($pid));
		$priceinfo = $wpdb->get_results($pricesql);
	
	}
	
	$sub_num_trial_units = isset( $priceinfo[0]->sub_num_trial_units ) && !empty( $priceinfo[0]->sub_num_trial_units ) ? $priceinfo[0]->sub_num_trial_units : 'D';
	$sub_num_trial_units = in_array( $sub_num_trial_units, array( 'D', 'W', 'M', 'Y' ) ) ? $sub_num_trial_units : 'D';
	$sub_num_trial_units_options = geodir_payment_get_sub_num_trial_units( $sub_num_trial_units );
	$has_upgrades = isset($priceinfo[0]->has_upgrades) && (int)$priceinfo[0]->has_upgrades == 1 ? 1 : 0;
	$disable_coupon = isset($priceinfo[0]->disable_coupon) && (int)$priceinfo[0]->disable_coupon == 1 ? 1 : 0;
	?>
<div class="gd-content-heading active">
<h3>
  <?php if(isset($_REQUEST['id']) && $_REQUEST['id']!=''){ _e('Edit Price', 'geodir_payments'); }else{ _e('Add Price', 'geodir_payments'); }?>
</h3>

<?php
	$nonce = wp_create_nonce( 'package_add_update' );
?>

<input type="hidden" name="package_add_update_nonce" value="<?php echo $nonce; ?>" />
<input type="hidden" name="gd_add_price" value="addprice">
<input type="hidden" name="gd_id" value="<?php if(isset($_REQUEST['id'])){ echo $_REQUEST['id'];}?>">
<input type="hidden" name="gd_exc_package_cat" value="<?php if(isset($priceinfo[0]->cat)) { echo $priceinfo[0]->cat;}else{ echo '';} ?>">
<table class="form-table">
  <tbody>
    <tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php _e('Price title', 'geodir_payments');?></th>
      <td class="forminp"><div class="gtd-formfield">
          <input type="text" style="min-width:200px;" name="gd_title" id="title" value="<?php if(isset($priceinfo[0]->title)){ echo $priceinfo[0]->title;}?>">
        </div></td>
    </tr>
    <tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php _e('Post type', 'geodir_payments');?></th>
      <td class="forminp"><div class="gtd-formfield">
          <select style="min-width:200px;" class="payment_gd_posting_type" name="gd_posting_type" >
            <?php
																$post_types = geodir_get_posttypes();
																
																if(!empty($post_types))
																{
																	foreach($post_types as $post_type)
																	{
																		?>
            <option value="<?php echo $post_type;?>" <?php if(isset($priceinfo[0]->post_type) && $priceinfo[0]->post_type == $post_type){ echo 'selected="selected"';}?> ><?php echo $post_type;?></option>
            <?php																	
																	}
																}
?>
          </select>
        </div></td>
    </tr>
    <tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php _e('Post fields', 'geodir_payments');?></th>
      <td class="forminp"><div class="gtd-formfield" id="show_fields">
          <?php 
				isset($priceinfo[0]->post_type) ? $post_type = $priceinfo[0]->post_type : $post_type='gd_place';
								
								$request_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
								
								$post_type_array = geodir_fields_list_by_posttype($post_type, $request_id);
								echo $post_type_array['posttype'];
							?>
        </div></td>
    </tr>
    <tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php _e('Price amount', 'geodir_payments');?>
        (
        <?php if(get_option('geodir_currencysym')){
					echo  stripslashes(get_option('geodir_currencysym'));}else{echo '$';}?>
        )</th>
      <td class="forminp"><div class="gtd-formfield">
          <input style="min-width:200px;" type="text" name="gd_amount" value="<?php if(isset($priceinfo[0]->amount)){ echo $priceinfo[0]->amount;}?>">
        </div></td>
    </tr>
    <tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php _e('Recurring payment?', 'geodir_payments');?></th>
      <td class="forminp">
         <div class="gtd-formfield">
          <input type="checkbox" name="gd_sub_active" id="payment_sub_active" value="1" <?php if(isset($priceinfo[0]->sub_active) && $priceinfo[0]->sub_active != ''){echo 'checked="checked"';}?>>
          <label>
          <?php 
		  $rec_pay_arr = apply_filters( 'geodir_subscription_supported_by', array('PayPal') );
		  if(count($rec_pay_arr)>1){
		  $last_element = array_pop($rec_pay_arr);
		  $rec_pay = implode(',',$rec_pay_arr). __(' and ', 'geodir_payments').$last_element;
		  }else{
			$rec_pay  =$rec_pay_arr[0];
		  }
		  
		  echo sprintf(__('(Only supported by %s)', 'geodir_payments'),$rec_pay);?>
          </label>
        </div>
      </td>
    </tr>
    <tr valign="top" class="show_num_days single_select_page" <?php if(isset($priceinfo[0]->sub_active) && $priceinfo[0]->sub_active != ''){echo 'style="display:none"';}?> > 
      <th class="titledesc" scope="row"><?php _e('Number of Days', 'geodir_payments');?></th>
      <td class="forminp">
        <div class=" single_select_page"  >
         <input type="text" name="gd_days"  id="days" value="<?php if(isset($priceinfo[0]->days)){ echo $priceinfo[0]->days;}?>">
         					<br /><?php _e('(set to 0 to never expire)', 'geodir_payments');?>
        </div>
      </td>
    </tr>
    <tr valign="top" class="show_recuring single_select_page" <?php if(!isset($priceinfo[0]->sub_active) || $priceinfo[0]->sub_active == ''){echo 'style="display:none"';}?> > 
      <th class="titledesc" scope="row"></th>
      <td class="forminp">
         
         <div class=" single_select_page"  >
		   <table cellspacing="0px" cellpadding="0px" style=" border:1px solid #ccc;">  
                   
             <tr valign="top" class="show_recuring single_select_page" <?php if(!isset($priceinfo[0]->sub_active) || $priceinfo[0]->sub_active == ''){echo 'style="display:none"';}?>>
                    <th style="border-bottom:solid 1px #CCCCCC;border-right:solid 1px #CCCCCC;"><b> <?php _e('Offer free trial', 'geodir_payments');?></b></th>
                    <th style="border-bottom:solid 1px #CCCCCC;"><b><?php _e('Recurring payment option', 'geodir_payments');?></b></th>
                    </tr>
             <tr  valign="top" class="show_recuring single_select_page" <?php if(!isset($priceinfo[0]->sub_active) || $priceinfo[0]->sub_active = ''){echo 'style="display:none"';}?>>
                    <td  style="border-right:solid 1px #CCCCCC;" ><input type="checkbox" name="fordaysckbox" id="active_offer" value="1" <?php if(isset($priceinfo[0]->sub_num_trial_days) && $priceinfo[0]->sub_num_trial_days >0){echo 'checked="checked"';}?> >
          
<?php _e('Offer free trail for', 'geodir_payments');?> <input type="text"  style="width:27px;" palceholder="0" name="sub_num_trial_days"  id="sub_num_trial_days"   value="<?php if(isset($priceinfo[0]->sub_num_trial_days)){ echo $priceinfo[0]->sub_num_trial_days;}?>" /> <select id="gd_sub_num_trial_units" name="gd_sub_num_trial_units" ><?php echo $sub_num_trial_units_options; ?></select><div class="clear"></div><?php _e( '(Allowed Range: Days range 1-90 || Weeks range 1-52 || Months range 1-24 || Years range 1-5)', 'geodir_payments' ); ?>
					</td> <td  width="550"> <?php _e('Renew', 'geodir_payments');?>   <select id="recurring_range" name="gd_sub_units" >
		
			<option value="D" <?php if(isset($priceinfo[0]->sub_units) && $priceinfo[0]->sub_units=='D'){ echo 'selected="selected"';}?> ><?php _e("Daily", 'geodir_payments');?></option>
			
			<option value="W" <?php if(isset($priceinfo[0]->sub_units) && $priceinfo[0]->sub_units=='W'){ echo 'selected="selected"';}?> ><?php _e("Weekly", 'geodir_payments');?></option>
			
			<option value="M" <?php if(isset($priceinfo[0]->sub_units) && $priceinfo[0]->sub_units=='M'){ echo 'selected="selected"';}?> ><?php _e("Monthly", 'geodir_payments');?></option>
			
			<option value="Y" <?php if(isset($priceinfo[0]->sub_units) && $priceinfo[0]->sub_units=='Y'){ echo 'selected="selected"';}?> ><?php _e("Yearly", 'geodir_payments');?></option>
			
			</select>
                    <br /><?php _e("Every", 'geodir_payments');?> &nbsp;
<select id="rangenumber"  name="gd_sub_units_num">
<?php  $i=0;while($i<91){$i++;?>
<option value="<?php echo $i;?>" <?php if(isset($priceinfo[0]->sub_units_num) && $priceinfo[0]->sub_units_num==$i){ echo 'selected="selected"';}?> ><?php echo $i;?></option><?php }  ?></select><br />
<samp style="width:10px; height:10px;" id="subscription"> <?php
									if(!isset($priceinfo[0]->sub_units)){ echo ' <b>Day(s)</b>';}
                if(isset($priceinfo[0]->sub_units) && $priceinfo[0]->sub_units=='D'){ echo '<b>'.__("Day(s)", 'geodir_payments').'</b>';}
                 if(isset($priceinfo[0]->sub_units) && $priceinfo[0]->sub_units=='W'){ echo '<b>'.__("Week(s)", 'geodir_payments').'</b>';}
                 if(isset($priceinfo[0]->sub_units) && $priceinfo[0]->sub_units=='M'){ echo '<b>'.__("Month(s)", 'geodir_payments').'</b>';}
                 if(isset($priceinfo[0]->sub_units) && $priceinfo[0]->sub_units=='Y'){ echo '<b>'.__("year(s)", 'geodir_payments').'</b>';}
 ?>
</samp> 
&nbsp; for <input style="width:40px;"  type="text" name="sub_units_num_times"  id="sub_units_num_times" value="<?php if(isset($priceinfo[0]->sub_units_num_times)){ echo $priceinfo[0]->sub_units_num_times;}?>"  />&nbsp; <?php _e('time(s), (min:2, max:52, blank for no limit)', 'geodir_payments');?> <br />
            <?php _e('(Allowed Range: Days range 1-90 || Weeks range 2-52 || Months range 2-24 || Years range 2-5)', 'geodir_payments');?> </td></tr>
           
        </table> 
		 </div>		
			 
	</td>
  </tr>
  
 </tbody>
</table>
      
<table class="form-table"><tbody><tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php _e('Status', 'geodir_payments');?></th>
      <td class="forminp"><div class="gtd-formfield">
          <select style="min-width:200px;" name="gd_status" >
            <option value="1" <?php if(isset($priceinfo[0]->status) && $priceinfo[0]->status=='1'){ echo 'selected="selected"';}?> >
            <?php _e("Active", 'geodir_payments');?>
            </option>
            <option value="0" <?php if(!isset($priceinfo[0]->status) || $priceinfo[0]->status=='0'){ echo 'selected="selected"';}?> >
            <?php _e("Inactive", 'geodir_payments');?>
            </option>
          </select>
        </div></td>
    </tr>
    <tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php _e('Is featured', 'geodir_payments');?></th>
      <td class="forminp"><div class="gtd-formfield">
          <select style="min-width:200px;" name="gd_is_featured" >
            <option value="0" <?php if(!isset($priceinfo[0]->is_featured) || $priceinfo[0]->is_featured=='0'){ echo 'selected="selected"';}?> >
            <?php _e("No", 'geodir_payments');?>
            </option>
            <option value="1" <?php if(isset($priceinfo[0]->is_featured) && $priceinfo[0]->is_featured=='1'){ echo 'selected="selected"';}?> >
            <?php _e("Yes", 'geodir_payments');?>
            </option>
          </select>
        </div></td>
    </tr>
    <tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php _e('Is default', 'geodir_payments');?></th>
      <td class="forminp"><div class="gtd-formfield">
          <select style="min-width:200px;" name="gd_is_default" >
            <option value="0" <?php if(!isset($priceinfo[0]->is_default) || $priceinfo[0]->is_default=='0'){ echo 'selected="selected"';}?> >
            <?php _e("No", 'geodir_payments');?>
            </option>
            <option value="1" <?php if(isset($priceinfo[0]->is_default) && $priceinfo[0]->is_default=='1'){ echo 'selected="selected"';}?> >
            <?php _e("Yes", 'geodir_payments');?>
            </option>
          </select>
        </div></td>
    </tr>
	<tr valign="top" class="show_ordering single_select_page"> 
      <th class="titledesc" scope="row"><?php _e('Display Order', 'geodir_payments');?></th>
      <td class="forminp">
        <div class="single_select_page">
         <input type="text" name="gd_display_order"  id="display_order" value="<?php if(isset($priceinfo[0]->display_order)) { echo (int)$priceinfo[0]->display_order; } else  { echo 0;}?>">
		 <br /><?php _e('(display sort order on front end package listing)', 'geodir_payments');?>
        </div>
      </td>
    </tr>
    <tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php _e('Exclude categories', 'geodir_payments');?></th>
      <td class="forminp"><div class="gtd-formfield">
          <?php
              if(!isset($priceinfo[0]->post_type) || $priceinfo[0]->post_type=='')
							{ 
								_e('You can only exclude categories once saved.', 'geodir_payments');
							}
							else
							{
								/*if($priceinfo[0]->cat)
								{
									$catarr = explode(',',$priceinfo[0]->cat);   
								}*/
									?>
          <div id="show_categories">
            <?php
									$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
									$post_type_array = geodir_fields_list_by_posttype($post_type, $_REQUEST['id'], $priceinfo[0]->cat);
									echo $post_type_array['html_cat'];
									?>
          </div>
          <br />
          <?php _e('Select multiple categories to exclude by holding down "Ctrl" key. <br />(if removing a parent category, you should remove its child categories.', 'geodir_payments');?>
          <br />
          <b>
          <?php _e('  (It is not recommended to exclude categories from live <br /> packages as users will not be able to remove that category from the frontend.)', 'geodir_payments');?>
          </b>
          <?php 
							} 
							?>
        </div></td>
    </tr>
    <tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php _e('Expire, Downgrade to', 'geodir_payments');?></th>
      <td class="forminp"><div class="gtd-formfield" id="gd_downgrade_pkg">
			<?php
				$request_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
				$select_dpkg = isset($priceinfo[0]->downgrade_pkg) ? $priceinfo[0]->downgrade_pkg : '';
				$post_type_array = geodir_fields_list_by_posttype($post_type, $request_id, $select_dpkg);
				echo $post_type_array['downgrade'];
			?>
          
        </div></td>
    </tr>
    <tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php _e('Title to be display while add listing', 'geodir_payments');?></th>
      <td class="forminp"><div class="gtd-formfield">
          <textarea name="gd_title_desc" cols="40" rows="5" id="title_desc"><?php if(isset($priceinfo[0]->title_desc)){ echo stripslashes($priceinfo[0]->title_desc);}?></textarea>
          <br />
          <?php _e('Keep blank to reset default content.', 'geodir_payments');?>
        </div></td>
    </tr>
    <tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php _e('Image limit', 'geodir_payments');?></th>
      <td class="forminp"><div class="gtd-formfield">
          <input style="min-width:200px;" type="text" name="gd_image_limit" value="<?php if(isset($priceinfo[0]->image_limit)){ echo $priceinfo[0]->image_limit;}?>">
          <br />
          <?php _e('(Leave blank for unlimited)', 'geodir_payments');?>
        </div></td>
    </tr>
    <tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php _e('Category limit', 'geodir_payments');?></th>
      <td class="forminp"><div class="gtd-formfield">
          <input style="min-width:200px;" type="text" name="gd_cat_limit" value="<?php if(isset($priceinfo[0]->cat_limit)){echo $priceinfo[0]->cat_limit;}?>">
          <br />
          <?php _e('(Leave blank for unlimited, can not be 0(ZERO))', 'geodir_payments');?>
        </div></td>
    </tr>
	<?php
	$use_desc_limit = isset($priceinfo[0]->use_desc_limit) && $priceinfo[0]->use_desc_limit==1 ? 1 : 0;
	$desc_limit = isset($priceinfo[0]->desc_limit) && (int)$priceinfo[0]->desc_limit>0 ? (int)$priceinfo[0]->desc_limit : 0;
	$use_tag_limit = isset($priceinfo[0]->use_tag_limit) && $priceinfo[0]->use_tag_limit==1 ? 1 : 0;
	$tag_limit = isset($priceinfo[0]->tag_limit) && (int)$priceinfo[0]->tag_limit>0 ? (int)$priceinfo[0]->tag_limit : 0;
	?>
	<tr valign="top" class="single_select_page">
	  <th class="titledesc" scope="row"><?php _e('Apply description limit?', 'geodir_payments');?></th>
	  <td class="forminp">
		<div class="gtd-formfield">
			<select style="min-width:100px;" name="gd_use_desc_limit" id="gd_use_desc_limit">
			  <option value="0" <?php if($use_desc_limit!='1'){ echo 'selected="selected"';}?>><?php _e("No", 'geodir_payments');?></option>
			  <option value="1" <?php if($use_desc_limit=='1'){ echo 'selected="selected"';}?>><?php _e("Yes", 'geodir_payments');?></option>
			</select>
			<br /><?php _e('("Yes" to apply description limit)', 'geodir_payments');?>
		</div>
	</td>
	</tr>
	<tr valign="top" class="single_select_page" id="use_desc_limit_on" <?php if($use_desc_limit!='1'){ echo 'style="display:none"';}?>>
	  <th class="titledesc" scope="row" style="padding-top:1px"><?php _e('Description limit', 'geodir_payments');?></th>
	  <td class="forminp" style="padding-top:1px">
		<div class="gtd-formfield">
			<input style="max-width:100px;" type="text" name="gd_desc_limit" value="<?php echo (int)$desc_limit;?>" />
			<br /><?php _e('(Characters limit for listing description, ex: 140)', 'geodir_payments');?>
		</div>
	  </td>
	</tr>
	<tr valign="top" class="single_select_page">
	  <th class="titledesc" scope="row"><?php _e('Apply tags limit?', 'geodir_payments');?></th>
	  <td class="forminp">
		<div class="gtd-formfield">
			<select style="min-width:100px;" name="gd_use_tag_limit" id="gd_use_tag_limit">
			  <option value="0" <?php if($use_tag_limit!='1'){ echo 'selected="selected"';}?>><?php _e("No", 'geodir_payments');?></option>
			  <option value="1" <?php if($use_tag_limit=='1'){ echo 'selected="selected"';}?>><?php _e("Yes", 'geodir_payments');?></option>
			</select><br /><?php _e('(If set to NO the default limit of 40 will be used. Set to Yes to increase/decrease)', 'geodir_payments');?>
		</div>
	</td>
	</tr>
	<tr valign="top" class="single_select_page" id="use_tag_limit_on" <?php if($use_tag_limit!='1'){ echo 'style="display:none"';}?>>
	  <th class="titledesc" scope="row" style="padding-top:1px"><?php _e('Tags limit', 'geodir_payments');?></th>
	  <td class="forminp" style="padding-top:1px">
		<div class="gtd-formfield" style="display:inline-block">
			<input style="max-width:100px;" type="text" name="gd_tag_limit" value="<?php echo (int)$tag_limit;?>" />
		<br /><?php _e('(Characters limit for listing tags, ex: 40)', 'geodir_payments');?>
		</div>
	  </td>
	</tr>
    <tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php _e('Google analytics', 'geodir_payments');?></th>
      <td class="forminp"><div class="gtd-formfield">
          <select style="min-width:100px;" name="google_analytics" >
            <option value="0" <?php if(!isset($priceinfo[0]->google_analytics) || $priceinfo[0]->google_analytics=='0'){ echo 'selected="selected"';}?> >
            <?php _e("No", 'geodir_payments');?>
            </option>
            <option value="1" <?php if(isset($priceinfo[0]->google_analytics) && $priceinfo[0]->google_analytics=='1'){ echo 'selected="selected"';}?> >
            <?php _e("Yes", 'geodir_payments');?>
            </option>
          </select>
        </div></td>
    </tr>
		
		 <tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php echo SEND_TO_FRIEND;?></th>
      <td class="forminp"><div class="gtd-formfield">
          <select style="min-width:100px;" name="geodir_sendtofriend" >
            <option value="0" <?php if(!isset($priceinfo[0]->sendtofriend) || $priceinfo[0]->sendtofriend=='0'){ echo 'selected="selected"';}?> >
            <?php _e("No", 'geodir_payments');?>
            </option>
            <option value="1" <?php if(isset($priceinfo[0]->sendtofriend) && $priceinfo[0]->sendtofriend=='1'){ echo 'selected="selected"';}?> >
            <?php _e("Yes", 'geodir_payments');?>
            </option>
          </select>
        </div></td>
    </tr>
	<?php $hide_related_tab = isset( $priceinfo[0]->hide_related_tab ) && (int)$priceinfo[0]->hide_related_tab == 1 ? 1 : 0; ?>
	<tr valign="top" class="single_select_page">
		<th scope="row" class="titledesc"><?php _e( 'Hide related listing tab', 'geodir_payments' ); ?></th>
		<td class="forminp">
			<div class="gtd-formfield">
				<select name="geodir_hide_related_tab" style="min-width:100px;">
					<option value="0" <?php selected( (int)$hide_related_tab, 0 ); ?>><?php _e( 'No', 'geodir_payments' ); ?></option>
					<option value="1" <?php selected( (int)$hide_related_tab, 1 ); ?>><?php _e( 'Yes', 'geodir_payments' ); ?></option>
				</select>
			</div>
			<span class="description"><?php _e( 'Select "Yes" to hide related listing tab on listing detail page.', 'geodir_payments' ); ?></span>
		</td>
	</tr>
	<tr valign="top" class="single_select_page">
		<th scope="row" class="titledesc"><?php _e( 'Has Upgrades?', 'geodir_payments' ); ?></th>
		<td class="forminp">
			<div class="gtd-formfield">
				<select name="geodir_has_upgrades" style="min-width:100px;">
					<option value="0" <?php selected( (int)$has_upgrades, 0 ); ?>><?php _e( 'No', 'geodir_payments' ); ?></option>
					<option value="1" <?php selected( (int)$has_upgrades, 1 ); ?>><?php _e( 'Yes', 'geodir_payments' ); ?></option>
				</select>
			</div>
			<span class="description"><?php _e( 'If set No, the upgrade link doesn\'t show for listings that belongs to this package.', 'geodir_payments' ); ?></span>
		</td>
	</tr>
	<tr valign="top" class="single_select_page">
		<th scope="row" class="titledesc"><?php _e( 'Disable Coupon Use?', 'geodir_payments' ); ?></th>
		<td class="forminp">
			<div class="gtd-formfield">
				<select name="geodir_disable_coupon" style="min-width:100px;">
					<option value="0" <?php selected( (int)$disable_coupon, 0 ); ?>><?php _e( 'No', 'geodir_payments' ); ?></option>
					<option value="1" <?php selected( (int)$disable_coupon, 1 ); ?>><?php _e( 'Yes', 'geodir_payments' ); ?></option>
				</select>
			</div>
			<span class="description"><?php _e( 'Set "Yes" to disable coupon usage for this price package.', 'geodir_payments' ); ?></span>
		</td>
	</tr>
		
		<?php do_action('geodir_payment_package_extra_fields', $priceinfo); /* EVENT-MANAGER */ ?>
		
  </tbody>
</table>
<script type="text/javascript">
jQuery(function(){
	jQuery('#gd_use_desc_limit').change(function(){
		if (jQuery(this).val()=='1') {
			jQuery('#use_desc_limit_on').fadeIn();
		} else {
			jQuery('#use_desc_limit_on').fadeOut();
		}
	});
	jQuery('#gd_use_tag_limit').change(function(){
		if (jQuery(this).val()=='1') {
			jQuery('#use_tag_limit_on').fadeIn();
		} else {
			jQuery('#use_tag_limit_on').fadeOut();
		}
	});
})
</script>
<p class="submit" style="margin-top:10px; padding-left:15px;">
  <input type="submit" class="button-primary" name="submit" value="<?php _e('Submit', 'geodir_payments');?>" onclick="return check_frm();">
  &nbsp;
  <input type="button" class="button-primary" name="gd_cancel" value="<?php _e('Cancel', 'geodir_payments');?>" onClick="window.location.href='<?php echo admin_url()?>admin.php?page=geodirectory&tab=paymentmanager_fields&subtab=geodir_payment_manager'" >
</p>
</form>
</div>

<?php
}
/* end of package price add/edit form in backend*/

function geodir_payment_gateways_list()
{
global $wpdb;


// UPDATE FOR GOOGLE WALLET
if(!get_option( 'geodir_google_wallet_3' )){
	delete_option('payment_method_googlechkout');
	geodir_payment_activation_script();
	update_option('geodir_google_wallet_3',1);
}

$paymentsql = $wpdb->prepare("select * from $wpdb->options where option_name like %s",array('payment_method_%'));

$paymentinfo = $wpdb->get_results($paymentsql);
?>
 <div class="gd-content-heading active">  
     
	<h3><?php _e('Geo Directory Manage Payment Options', 'geodir_payments')?></h3>
         
	<table style=" width:100%"  class="widefat post fixed" >
			<thead>
					<tr>
							<th width="250"><strong><?php _e('Method Name', 'geodir_payments');?></strong></th>
							
							<th width="130"><strong><?php _e('Is Active', 'geodir_payments');?></strong></th>
							
							<th width="130" align="center"><strong><?php _e('Sort Order', 'geodir_payments');?></strong></th>
							
							<th width="130" align="center"><strong><?php _e('Action', 'geodir_payments');?></strong></th>
							
							<th width="120" align="center"><strong><?php _e('Settings', 'geodir_payments');?></strong></th>
							
							<th>&nbsp;</th>
					</tr>
			<?php
			if($paymentinfo)
			{
foreach($paymentinfo as $paymentinfoObj)
{
$paymentInfo = unserialize($paymentinfoObj->option_value);

$option_id = $paymentinfoObj->option_id;

$paymentInfo['option_id'] = $option_id;

$paymentOptionArray[$paymentInfo['display_order']][] = $paymentInfo;
}
ksort($paymentOptionArray);

foreach($paymentOptionArray as $key=>$paymentInfoval)
{
for($i=0;$i<count($paymentInfoval);$i++)
{
$paymentInfo = $paymentInfoval[$i];

$option_id = $paymentInfo['option_id'];

$nonce = wp_create_nonce( 'payment_options_status_update_'.$option_id );

?>
<tr>
											<td><?php echo $paymentInfo['name'];?></td>
											
											<td><?php if($paymentInfo['isactive']){ _e("Yes", 'geodir_payments');}else{	_e("No", 'geodir_payments');}?></td>
											
											<td><?php echo $paymentInfo['display_order'];?></td>
											
											<td><?php if($paymentInfo['isactive']==1)
											{
											echo '<a href="'.admin_url().'admin-ajax.php?action=geodir_payment_manager_ajax&gdaction=change_status&status=0&id='.$option_id.'&_wpnonce='.$nonce.'">'.__('Deactivate', 'geodir_payments').'</a>';
											}else
											{
											echo '<a href="'.admin_url().'admin-ajax.php?action=geodir_payment_manager_ajax&gdaction=change_status&status=1&id='.$option_id.'&_wpnonce='.$nonce.'">'.__('Activate', 'geodir_payments').'</a>';
											}
											?></td>
											
											<td><?php
											echo '<a href="'.admin_url().'admin.php?page=geodirectory&tab=paymentmanager_fields&subtab=geodir_payment_options&gd_payact=gd_setting&id='.$option_id.'">'.__('Settings', 'geodir_payments').'</a>';
											?></td>
											
											<td>&nbsp;</td>
</tr>
<?php
}
}
			}
			?>
			</thead>
	</table>
</div>
<?php
}
/* end of payment gateways list in backend */

/* Payment gateway setting form  */
function geodir_payment_gateway_setting_form()
{
global $wpdb;

if(isset($_GET['status']) && $_GET['status']!= '')
{
	$option_value['isactive'] = $_GET['status'];
}

	$paymentupdsql = $wpdb->prepare("select option_name, option_value from $wpdb->options where option_id=%d", array($_GET['id']));
	
	$paymentupdinfo = $wpdb->get_results($paymentupdsql);
	if($paymentupdinfo)
	{
		foreach($paymentupdinfo as $paymentupdinfoObj)
		{
			$option_name = $paymentupdinfoObj->option_name;
			$option_value = unserialize($paymentupdinfoObj->option_value);
			$paymentOpts = $option_value['payOpts'];
		}
	}

?>
  
<div class="gd-content-heading active">	
	<h3><?php echo $option_value['name'];?> <?php _e('Settings', 'geodir_payments'); ?></h3>
       
<?php
	$nonce = wp_create_nonce( 'payment_options_status_update_'.$_REQUEST['id'] );
?>

	<input type="hidden" name="update_payment_settings_nonce" value="<?php echo $nonce; ?>" />
	<input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>" />
	<input type="hidden" name="paymentsetting" value="update_setting" />

<table class="form-table">
	<tbody>
			<tr valign="top" class="single_select_page">
					<th class="titledesc" scope="row"><?php _e('Payment Method', 'geodir_payments'); ?></th>
					<td class="forminp">
					 <div class="gtd-formfield">
						 <input type="text" name="payment_method" style=" width: 429px;" id="payment_method" value="<?php echo $option_value['name'];?>" size="50" />
					</div>       
					</td>
			</tr>
			
			<tr valign="top" class="single_select_page">
					<th class="titledesc" scope="row"><?php _e('Is Active', 'geodir_payments'); ?></th>
					<td class="forminp">
					 <div class="gtd-formfield">
						 <select name="payment_isactive" style=" width: 429px;" id="payment_isactive">
									<option value="1" <?php if($option_value['isactive']==1){ echo 'selected="selected"'; } ?>><?php _e('Activate', 'geodir_payments');?></option>
									<option value="0" <?php if($option_value['isactive']=='0' || $option_value['isactive']==''){ echo 'selected="selected"'; } ?>><?php _e('Deactivate', 'geodir_payments');?></option>
							</select>
					</div>       
					</td>
			</tr>
			
			
			<tr valign="top" class="single_select_page">
					<th class="titledesc" scope="row"><?php _e('Display Order', 'geodir_payments'); ?></th>
					<td class="forminp">
					 <div class="gtd-formfield">
						 <input type="text" name="display_order" style=" width: 429px;" id="display_order" value="<?php echo $option_value['display_order'];?>" size="50"  />
					</div>       
					</td>
			</tr>
			
			<!-- PAYMENT MODE SETTINGS -->
			<tr valign="top" class="single_select_page">
					<th class="titledesc" scope="row"><?php _e('Mode', 'geodir_payments'); ?></th>
					<td class="forminp">
					 <div class="gtd-formfield">
						<select id="payment_mode" style=" width: 429px;" name="payment_mode">
							<option value="live" <?php if("live" == $option_value['payment_mode']){echo 'selected="selected"';}?>><?php _e('Live Mode', 'geodir_payments');?></option>
							<option value="sandbox" <?php if("sandbox" == $option_value['payment_mode']){echo 'selected="selected"';}?>><?php _e('Test Mode (Sandbox)', 'geodir_payments');?></option>
						</select>
					</div>       
					</td>
			</tr>
			
			
			<?php
			for($i=0;$i<count($paymentOpts);$i++)
			{
				$payOpts = $paymentOpts[$i];
	?>
				<tr valign="top" class="single_select_page">
					<th class="titledesc" scope="row"><?php echo $payOpts['title'];?></th>
					<td class="forminp">
					 <div class="gtd-formfield">
					 	<?php
						if($payOpts['field_type'] == 'select'){
							
							?>
							<select name="<?php echo $payOpts['fieldname'];?>" style=" width: 429px;" id="<?php echo $payOpts['fieldname'];?>">
								<?php
								foreach($payOpts['option_values'] as $opts => $val){
								
									?><option <?php if($payOpts['value'] == $val){echo 'selected="selected"';}?> value="<?php echo $val;?>"><?php echo $opts;?></option><?php
								}
								?>
							</select>
							<?php
						
						}elseif($payOpts['field_type'] == 'text'){
						
							?>
						 <input type="text" style=" width: 429px;" name="<?php echo $payOpts['fieldname'];?>" id="<?php echo $payOpts['fieldname'];?>" value="<?php echo $payOpts['value'];?>" size="50"  /><br /><?php echo $payOpts['description'];
						
						}
						?>
					</div>       
					</td>
				</tr>
	<?php
			}
			?>
			
	</tbody>
</table>


<p class="submit" style="margin-top:10px; padding-left:15px;">
<input class="button-primary" type="submit" name="submit" value="<?php _e('Submit', 'geodir_payments'); ?>" onclick="return chk_form();" />&nbsp;
<input class="button-primary" type="button" name="cancel" value="<?php _e('Cancel', 'geodir_payments'); ?>" onclick="window.location.href='<?php echo admin_url()."admin.php?page=geodirectory&tab=paymentmanager_fields&subtab=geodir_payment_options"; ?>'"  />

<?php $nonce = wp_create_nonce( 'payment_trouble_shoot'.$option_name ); ?>

<input class="button-primary geodir_payment_trouble_shoot" type="button" name="Trouble Shot" value="<?php _e('Trouble Shoot', 'geodir_payments'); ?>" onclick="confirm:if (window.confirm('<?php _e('Do you wish to reset all payment settings?', 'geodir_payments'); ?>')) { window.location.href='<?php echo geodir_payment_manager_ajaxurl(); ?>&payaction=trouble_shoot&nonce=<?php echo $nonce;?>&pay_method=<?php echo $option_name;?>'; }"  />

</p>
	

	</form>
	</div>
<?php
}
/* end of payment gateway setting form in backend */

/**
 * Get invoice list for invoices page.
 *
 * @since 1.2.6
 *
 * @param array $args Invoice list query arags.
 * @return array Invoice array.
 */
function geodir_payment_get_invoice_list( $args = array() ) {
	$per_page = isset( $_REQUEST['per_page'] ) ? absint( $_REQUEST['per_page'] ) : 0;
	$invoice_id = isset( $_REQUEST['invoice_id'] ) ? (int)$_REQUEST['invoice_id'] : '';
	$per_page = $per_page > 0 ? $per_page : 10;
	
	$pagination_args = wp_parse_args( 
										$args, 
										array(
											'per_page' => $per_page,
											'invoice_id' => $invoice_id,
										)
									);
	$rows = geodir_payment_get_invoices( $pagination_args );
	
	return $rows;
}

/**
 * Get invoices using given arguments.
 *
 * @since 1.2.6
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param array $args Query args.
 * @return array invoice array.
 */
function geodir_payment_get_invoices( $args ) {
	global $wpdb;

	$where = '';
	
	if ( !empty( $args['invoice_id'] ) && $args['invoice_id'] > 0 ) {
		$where .= "AND id = '" . $args['invoice_id'] . "' ";
	}
	
	$sql = "SELECT COUNT(*) FROM " . INVOICE_TABLE . " WHERE 1=1 " . $where;
	$total_items = $wpdb->get_var( $sql );
	
	if ( !empty( $args['count'] ) ) {
		return $total_items;
	}
	
	$total_pages = ( $total_items > 0 && isset( $args['per_page'] ) && $args['per_page'] > 0 ) ? ceil( $total_items / $args['per_page'] ) : 0;
	$args['total_pages'] = $total_pages;
	
	$pagenum = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 0;
	
	if ( isset( $args['total_pages'] ) && $pagenum > $args['total_pages'] ) {
		$pagenum = $args['total_pages'];
	}
	
	$pagenum = max( 1, $pagenum );
	$args['total_items'] = $total_items;
	$args['pagenum'] = $pagenum;
	
	$limits = '';
	if ( isset( $args['per_page'] ) && $args['per_page'] > 0 ) {
		$offset = ( $pagenum - 1 ) * $args['per_page'];
		if ( $offset > 0 ) {
			$limits = 'LIMIT ' . $offset . ',' . $args['per_page'];
		} else {
			$limits = 'LIMIT ' . $args['per_page'];
		}
	}
	
	$sql = "SELECT * FROM " . INVOICE_TABLE . " WHERE 1=1 " . $where . " ORDER BY id DESC " . $limits;

	$items = $wpdb->get_results( $sql );
	$result = array();
	$result['items'] = $items;
	$result['total_items'] = $total_items;
	$result['total_pages'] = $total_pages;
	$result['pagenum'] = $pagenum;	
	$result['pagination'] = geodir_payment_admin_pagination( $args );

	return $result;
}

/**
 * Admin payment invoice pagination.
 *
 * @since 1.2.6
 *
 * @param array $args Pagination arguments.
 * @param string $which Pagination position.
 * @return string Pagination HTML.
 */
function geodir_payment_admin_pagination( $args, $which = 'bottom' ) {
	if ( empty( $args ) || empty( $args['total_items'] ) ) {
		return;
	}

	$total_items = $args['total_items'];
	$total_pages = $args['total_pages'];
	$infinite_scroll = false;
	if ( isset( $args['infinite_scroll'] ) ) {
		$infinite_scroll = $args['infinite_scroll'];
	}

	$output = '<span class="displaying-num">' . sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

	$current = $args['pagenum'];

	$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

	$current_url = esc_url( remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url ), '', '' );

	$page_links = array();

	$disable_first = $disable_last = '';
	if ( $current == 1 ) {
		$disable_first = ' disabled';
	}
	if ( $current == $total_pages ) {
		$disable_last = ' disabled';
	}
	$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
		'first-page' . $disable_first,
		esc_attr__( 'Go to the first page', 'geodir_payments' ),
		esc_url( remove_query_arg( 'paged', $current_url ) ),
		'&laquo;'
	);

	$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
		'prev-page' . $disable_first,
		esc_attr__( 'Go to the previous page', 'geodir_payments' ),
		esc_url( add_query_arg( 'paged', max( 1, $current-1 ), $current_url ) ),
		'&lsaquo;'
	);

	$html_current_page = $current;
	
	$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
	$page_links[] = '<span class="paging-input">' . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . '</span>';

	$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
		'next-page' . $disable_last,
		esc_attr__( 'Go to the next page', 'geodir_payments' ),
		esc_url( add_query_arg( 'paged', min( $total_pages, $current+1 ), $current_url ) ),
		'&rsaquo;'
	);

	$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
		'last-page' . $disable_last,
		esc_attr__( 'Go to the last page', 'geodir_payments' ),
		esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
		'&raquo;'
	);

	$pagination_links_class = 'pagination-links';
	if ( ! empty( $infinite_scroll ) ) {
		$pagination_links_class = ' hide-if-js';
	}
	$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

	if ( $total_pages ) {
		$page_class = $total_pages < 2 ? ' one-page' : '';
	} else {
		$page_class = ' no-pages';
	}
	$pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

	return $pagination;
}

/* Start Payment Invoice list */
function geodir_payment_invoice_list() {
    global $wpdb;
	
	$invoices_list = geodir_payment_get_invoice_list();
	
	$payment_statuses = geodir_payment_all_payment_status( false );
	$nonce = wp_create_nonce( 'invoice_status_update_nonce' );
?>
<div class="gd-content-heading active gd-payment-invoice-list">
    <h3><?php echo PAYMENT_MANAGE_INVOICE; ?></h3>
	<table style=" width:100%" cellpadding="5" class="widefat post fixed">
		<thead>
			<tr>
				<th width="135" align="left"><strong><?php echo GD_INVOICE_LISTING; ?></strong></th>
				<th width="50" align="left"><strong><?php echo GD_INVOICE_TYPE; ?></strong></th>
				<th width="200" align="left"><strong><?php echo GD_INVOICE_PKG_INFO ; ?></strong></th>
				<th width="70" align="left"><strong><?php echo GD_INVOICE_COUPON ; ?></strong></th>
				<th width="200" align="left"><strong><?php echo GD_PAYMENT_INFORMATION; ?></strong></th>
				<th width="60" align="left"><strong><?php echo PAYMENT_STATUS; ?></strong></th>
				<th align="left"><strong><i class="fa fa-times"></i></strong></th>
			</tr>
		</thead>
		<tbody>
			<?php 
		    if ( !empty( $invoices_list ) && isset( $invoices_list['items'] ) && !empty( $invoices_list['items'] ) ) { 
			    $items = $invoices_list['items'];
				foreach ( $items as $item ) {
					$invoice_id = $item->id;
					$invoice_title = $item->post_title;
					$invoice_title = apply_filters( 'geodir_payment_admin_list_invoice_title', $invoice_title, $item );
					$post_id = $item->post_id;
					$status = $item->status;
					$paid_amt ='';
					$paid_amt = $item->paied_amount;
					
					if ( in_array( geodir_strtolower( $status ), array( 'paid', 'active', 'subscription-payment', 'free' ) ) ) {
						$status = 'confirmed';
					} else if ( in_array( geodir_strtolower( $status ), array( 'unpaid' ) ) ) {
						$status = 'pending';
					}
					
					$incomplete = $status == 'pending' && empty($item->paymentmethod) ? true : false;
					
					if ( (isset($type) && ($type=='Paid' || $type=='Subscription-Payment')) && $status == 'paid' ) {
						$total = $total + $paid_amt;
					}
					
					$send_invoice = $item->HTML != '' ? ' | ' : '';
					$send_invoice .= '<a href="javascript:void(0)" onclick="gd_payment_send_invoice(' . $invoice_id . ', \'' . wp_create_nonce( 'gd_nonce_send_invoice_' . $invoice_id ) . '\')">' . __( 'Send Invoice', 'geodir_payments' ) . '</a>';
					
					ob_start();
					?>
					<a href="<?php echo get_permalink($post_id); ?>"><?php _e('front', 'geodir_payments'); ?></a> |  <?php edit_post_link( 'back', '', '', $post_id ); ?>
					<?php
					$invoice_links = ob_get_clean();
					$invoice_links = apply_filters( 'geodir_payment_admin_list_invoice_links', $invoice_links, $item );
					$class = 'gd-invtr-' . $status;
					if ($incomplete) {
						$class .= ' gd-invtr-incomplete';
					}
			?>
			<tr id='invoiceid-<?php echo $invoice_id ;?>' class="<?php echo $class;?>">
			    <td><?php echo ucfirst($invoice_title); ?><br /><?php echo $invoice_links;?></td>
				<td><?php echo ucfirst($item->type); ?></td>
				<td><i class="fa fa-clock-o"></i> <?php echo $item->date;?><br />
					<label><?php echo PAYMENT_META_ID;?></label> <?php echo $item->package_id;?><br />
					<label><?php echo PAYMENT_META_AMOUNT ;?></label> <?php echo geodir_payment_price( $item->amount );?><br/>
					<label><?php echo PAYMENT_META_ALIVE_DAYS;?></label> <?php echo $item->alive_days;?></td>
				<td><?php echo ($item->coupon_code) ? $item->coupon_code : PAYMENT_META_NA;?></td>
				<td>
					<?php echo GD_INVOICE_DISCOUNT ; ?>:&nbsp;
					<?php echo ($item->discount) ? geodir_payment_price( $item->discount ) : '0';?><br />
					<?php echo GD_INVOICE_PAY_AMOUNT ; ?>:&nbsp;
					<?php echo ($paid_amt) ? geodir_payment_price( $paid_amt ) : '0'; ?><br />
					<?php echo GD_INVOICE_PAY_METHOD ; ?>:&nbsp;
					<?php echo ($item->paymentmethod) ? geodir_payment_method_title($item->paymentmethod) : PAYMENT_META_NA;?>
					<br />
					<?php if ($item->HTML != '') { ?>
					<a href="javascript:void(0);" class="geodir_invoice_detail_link" data-invoiceid='<?php echo $invoice_id ;?>' ><?php _e('View Invoice', 'geodir_payments');?></a>
					<?php } ?>
					<?php echo $send_invoice;?>
				</td>
				<td>
					<select id="status" onchange="if(confirm('<?php _e('Are you sure?', 'geodir_payments');?>')) { window.location.href='<?php echo admin_url().'admin-ajax.php?action=geodir_payment_manager_ajax&invoice_action=invoice&invoiceid='.$invoice_id; ?>&_wpnonce=<?php echo $nonce; ?>&inv_status='+this.value; }">
					<?php 
					foreach ( $payment_statuses as $status_key => $status_name ) { 
						if ($incomplete && $status_key == 'pending') {
							$status_name = __('Incomplete', 'geodir_payments');
						}
					?>
						<option value="<?php echo $status_key;?>" <?php selected( $status, $status_key );?>><?php echo $status_name;?></option>
					<?php } ?>
					</select>
				</td>
				<td>
					<span class="geodir_invoice_delete_link" data-invoiceid='<?php echo $invoice_id ;?>' title="<?php _e('Delete Invoice', 'geodir_payments');?>" style="color:#F00;cursor: pointer;"><i class="fa fa-times"></i></span>
				</td>
			</tr>
			<?php if( $item->HTML !='') { ?>
			<tr id="geodir_invoice_row_<?php echo $invoice_id ;?>" class="geodir_invoice_row"  style="display:none;">
				<td colspan="6"  width="100%"><p><?php echo $item->HTML ; ?></p></td>
			</tr>
			<?php } ?>
			    <?php
				}
			}
			?>
		</tbody>
	</table>
	<?php if ( !empty($invoices_list) && empty($_REQUEST['invoice_id']) && !empty( $invoices_list['pagination'] ) ) { ?>
	<div class="tablenav bottom">
		<div class="alignleft actions bulkactions"></div>
		<?php echo $invoices_list['pagination']; ?>
		<br class="clear" />
	</div>
	<?php } ?>
</div>
<script>
jQuery('.geodir_invoice_detail_link').click(function() {
    var invoiceid = jQuery(this).data("invoiceid");
    if (jQuery('#geodir_invoice_row_' + invoiceid).is(':visible')) 
		jQuery('#geodir_invoice_row_' + invoiceid).hide();
    else 
		jQuery('#geodir_invoice_row_' + invoiceid).show();
});
jQuery('.geodir_invoice_delete_link').click(function() {
    var invoiceid = jQuery(this).data("invoiceid");
    if (confirm('<?php _e('Are you sure you want to delete this invoice ? ', 'geodir_payments');?>')) {
        geodir_del_invoice(invoiceid);
    }
});

function geodir_del_invoice(id) {
    if (!id) {
        return;
    }
    var data = {
        'action': 'geodir_del_invoice',
        'invoice_id': id
    };
    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    jQuery.post(ajaxurl, data, function(response) {
        if (response) {
            jQuery('#invoiceid-' + id).css('background-color', 'red');
            jQuery('#invoiceid-' + id).fadeOut("slow");
        } else {
            alert('<?php _e('Something went wrong.', 'geodir_payments');?>');
        }
    });
}
</script>
<?php	
}
/* End Payment invoice list in backend */


/* Start Payment Coupon list in backend */
function geodir_payment_coupon_list()
{
?>
<?php global $wpdb; ?>

<div class="gd-content-heading active gd-payment-coupon-list">

	<h3><?php _e('Geo Directory Manage Coupons', 'geodir_payments'); ?></h3>
	
	<p style="padding-left:15px;"><?php _e('Allow coupon option on submit Add Listing page :', 'geodir_payments'); ?>
	<?php $geodir_allow_coupon_code = get_option('geodir_allow_coupon_code'); ?>
	
	<?php
	$nonce = wp_create_nonce( 'allow_coupon_code_nonce' );
	?>
	<input type="hidden" id="allow_coupon_code_nonce" name="allow_coupon_code_nonce" value="<?php echo $nonce;?>" />
	<input type="radio" class="geodir_allow_coupon_code" name="geodir_allow_coupon_code" value="1" <?php if($geodir_allow_coupon_code){ echo 'checked="checked"';} ?> /><?php _e('Yes', 'geodir_payments' );?>
	<input type="radio" class="geodir_allow_coupon_code" name="geodir_allow_coupon_code" value="0" <?php if(!$geodir_allow_coupon_code){ echo 'checked="checked"';} ?> /><?php _e('No', 'geodir_payments' );?>
	
	<input type="button" id="allow_coupon_code" class="button-primary" name="submit" value="<?php _e('Update', 'geodir_payments');?>" >
	</p>
	                               
						<p style="padding-left:15px;"><a href="<?php echo admin_url().'admin.php?page=geodirectory&tab=paymentmanager_fields&subtab=geodir_coupon_manager&gd_pagetype=addeditcoupon'?>"><strong><?php _e('Add Coupon', 'geodir_payments'); ?></strong></a>
						
						
						</p>
	<table style=" width:100%" cellpadding="5" class="widefat post fixed" >
							
							<thead>
								<tr>
									<th width="13%" align="left"><strong><?php _e('Coupon Code', 'geodir_payments'); ?></strong></th>
									<th width="12%" align="left"><strong><?php _e('Discount Type', 'geodir_payments'); ?></strong></th>
									<th width="10%" align="left"><strong><?php _e('Discount Amount', 'geodir_payments'); ?></strong></th>
									<th width="20%" align="left"><strong><?php _e('Post Types', 'geodir_payments'); ?></strong></th>
									<th width="13%" align="left"><strong><?php _e('Recurring', 'geodir_payments'); ?></strong></th>
									<th width="13%" style="text-align:center"><strong><?php _e('Usage / Limit', 'geodir_payments'); ?></strong></th>
									<th width="9%" align="left" ><strong><?php _e('Status', 'geodir_payments'); ?></strong></th>
									<th width="9%" align="left"><strong><?php _e('Action', 'geodir_payments'); ?></strong></th>
								</tr>
							<?php
							$couponsql = "select * from ".COUPON_TABLE;
							
							$couponinfo = $wpdb->get_results($couponsql);

							if ($couponinfo) {	
								foreach ($couponinfo as $couponinfoObj) {
									$post_types = str_replace(",", ", ", $couponinfoObj->post_types);
									$usage_count = isset($couponinfoObj->usage_count) ? absint($couponinfoObj->usage_count) : 0;
									$usage_limit = isset($couponinfoObj->usage_limit) ? trim($couponinfoObj->usage_limit) : '';
									if ($usage_limit > 0 && $usage_count > $usage_limit) {
										$usage_count = $usage_limit;
									}
									$usage_limit = $usage_limit == '' ? '&infin;' : $usage_limit;
							?>
									<tr>
											<td><?php echo $couponinfoObj->coupon_code;?></td>
											
											<td><?php if($couponinfoObj->discount_type=='per') _e("Percentage", 'geodir_payments'); else _e("Amount", 'geodir_payments');?></td>
											
											<td><?php echo $couponinfoObj->discount_amount;?></td>
											
											<td><?php echo $post_types;?></td>
                                        
											<td><?php if($couponinfoObj->recurring=='0'){_e("All payments", 'geodir_payments');}
											elseif($couponinfoObj->recurring=='1'){_e("First payment only", 'geodir_payments');}
											?></td>
											<td style="text-align:center"><?php echo $usage_count . ' / ' . $usage_limit;?></td>
											<td><?php if($couponinfoObj->status==1) _e("Active", 'geodir_payments'); else _e("Inactive", 'geodir_payments');?></td>
											
											<td>
											<?php
												$nonce = wp_create_nonce( 'coupon_code_delete_'.$couponinfoObj->cid);
											?>
											
											<a href="<?php echo admin_url().'admin.php?page=geodirectory&tab=paymentmanager_fields&subtab=geodir_coupon_manager&gd_pagetype=addeditcoupon&id='.$couponinfoObj->cid;?>">
											<img src="<?php echo plugins_url('',__FILE__); ?>/images/edit.png" alt="<?php _e('Edit Coupon', 'geodir_payments'); ?>" title="<?php _e('Edit Coupon', 'geodir_payments'); ?>"/>
											</a> 
											&nbsp;&nbsp;
											<a class="delete_coupon" nonce="<?php echo $nonce;?>" coupon_id="<?php echo $couponinfoObj->cid;?>" href="javascript:void(0);"><img src="<?php echo plugins_url('',__FILE__); ?>/images/delete.png" alt="<?php _e('Delete Coupon', 'geodir_payments'); ?>" title="<?php _e('Delete Coupon', 'geodir_payments'); ?>" /></a>
											</td>
									</tr>
								<?php
								}
							}
							?>
						</thead>
					</table>
	</div>
	
<?php
}


/* Start Payment Coupon add/edit in backend */
function geodir_payment_coupon_form() {
	global $wpdb;
	
	$usage_limit = '';
	
	if (isset($_REQUEST['id']) && $_REQUEST['id'] != '') {
		$cid = $_REQUEST['id'];
		
		$couponsql = $wpdb->prepare("select * from ".COUPON_TABLE." where cid=%d", array($cid));
		$couponinfo = $wpdb->get_row($couponsql);
		
		$usage_limit = isset($couponinfo->usage_limit) ? trim($couponinfo->usage_limit) : '';
	}
?>
<div class="gd-content-heading active">

<h3>
  <?php if(isset($_REQUEST['id']) && $_REQUEST['id']!=''){ _e('Edit Coupon', 'geodir_payments'); }else{ _e('Add Coupon', 'geodir_payments'); }?>
</h3>

<?php
	$nonce = wp_create_nonce( 'coupon_add_update' );
?>

<input type="hidden" name="coupon_add_update_nonce" value="<?php echo $nonce; ?>" />
<input type="hidden" name="gd_add_coupon" value="addprice">
<input type="hidden" name="gd_id" value="<?php if(isset($_REQUEST['id'])){ echo $_REQUEST['id'];}?>">
<table class="form-table">
  <tbody>
    <tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php _e('Coupon Code', 'geodir_payments');?></th>
      <td class="forminp"><div class="gtd-formfield">
          <input type="text" style="min-width:200px;" name="coupon_code" id="coupon_code" value="<?php if(isset($couponinfo->coupon_code)){ echo $couponinfo->coupon_code;}?>">
        </div></td>
    </tr>
    <tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php _e('Post type', 'geodir_payments');?></th>
      <td class="forminp"><div class="gtd-formfield">
					
					<?php 
					$get_post_types = array();
					
					if(isset($couponinfo->post_types) && $couponinfo->post_types != '')
						$get_post_types = explode(',',$couponinfo->post_types);
					?>
					
          <select multiple="multiple" style="min-width:200px;" id="post_type" name="post_type[]" >
            <?php
							$post_types = geodir_get_posttypes();
							
							if(!empty($post_types))
							{
								foreach($post_types as $post_type)
								{
									?>
									<option value="<?php echo $post_type;?>" <?php if(in_array($post_type,$get_post_types)){ echo 'selected="selected"';}?> ><?php echo $post_type;?></option>
									<?php																	
								}
							}
						?>
          </select>
        </div></td>
    </tr>
    
		<tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php _e('Discount Type', 'geodir_payments');?></th>
      <td class="forminp"><div class="gtd-formfield">
          <input type="radio" style="min-width:20px;" name="discount_type" <?php if((isset($couponinfo->discount_type) && $couponinfo->discount_type=='per') || (!isset($couponinfo->discount_type) || $couponinfo->discount_type=='')){ echo 'checked="checked"';}?> id="discount_type" value="per"><?php _e('Percentage(%)', 'geodir_payments');?>
					<input type="radio" style="min-width:20px;" name="discount_type" <?php if(isset($couponinfo->discount_type) && $couponinfo->discount_type=='amt'){ echo 'checked="checked"';}?>  id="discount_type" value="amt"><?php _e('Amount', 'geodir_payments');?>
        </div></td>
    </tr>
   
	 <tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php _e('Discount Amount ($)', 'geodir_payments');?></th>
      <td class="forminp"><div class="gtd-formfield">
          <input type="text" style="min-width:200px;" name="discount_amount" id="discount_amount" value="<?php if(isset($couponinfo->discount_amount)){echo $couponinfo->discount_amount;}?>">
        </div></td>
    </tr>
		
        <tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php _e('Recurring', 'geodir_payments');?></th>
      <td class="forminp"><div class="gtd-formfield">
          <select style="min-width:200px;" name="gd_recurring" >
            <option value="0" <?php if(!isset($couponinfo->recurring) || $couponinfo->recurring=='0'){ echo 'selected="selected"';}?> >
            <?php _e("All payments", 'geodir_payments');?>
            </option>
            <option value="1" <?php if(isset($couponinfo->recurring) && $couponinfo->recurring=='1'){ echo 'selected="selected"';}?> >
            <?php _e("First payment only", 'geodir_payments');?>
            </option>

          </select><small><?php _e('If applied to a recurring price package, how should it apply.', 'geodir_payments');?></small>
        </div></td>
    </tr>
	<tr valign="top" class="single_select_page">
		<th class="titledesc" scope="row"><?php _e('Usage Limit', 'geodir_payments');?></th>
		<td class="forminp">
			<div class="gtd-formfield">
				<input type="number" name="usage_limit" id="usage_limit" placeholder="<?php esc_attr_e('Unlimited usage', 'geodir_payments');?>" step="1" min="0" style="min-width:200px;" value="<?php echo $usage_limit;?>" />
				<span class="description"><?php _e('Leave blank for unlimited usage of coupon.', 'geodir_payments');?></span>
			</div>
		</td>
	</tr>
	<tr valign="top" class="single_select_page">
      <th class="titledesc" scope="row"><?php _e('Status', 'geodir_payments');?></th>
      <td class="forminp"><div class="gtd-formfield">
          <select style="min-width:200px;" name="gd_status" >
            <option value="1" <?php if(isset($couponinfo->status) && $couponinfo->status=='1'){ echo 'selected="selected"';}?> >
            <?php _e("Active", 'geodir_payments');?>
            </option>
            <option value="0" <?php if(!isset($couponinfo->status) || $couponinfo->status=='0'){ echo 'selected="selected"';}?> >
            <?php _e("Inactive", 'geodir_payments');?>
            </option>
          </select>
        </div></td>
    </tr>
 
 </tbody>
</table>
      
<p class="submit" style="margin-top:10px; padding-left:15px;">
  <input type="submit" id="coupon_submit" class="button-primary" name="submit" value="<?php _e('Submit', 'geodir_payments');?>" >
  &nbsp;
  <input type="button" class="button-primary" name="gd_cancel" value="<?php _e('Cancel', 'geodir_payments');?>" onClick="window.location.href='<?php echo admin_url()?>admin.php?page=geodirectory&tab=paymentmanager_fields&subtab=geodir_coupon_manager'" >
</p>
</form>
</div>

<?php
}


function geodir_payment_option_form($tab_name)
{
	
	switch ($tab_name)
	{
		case 'geodir_payment_general_options' :
		
			geodir_admin_fields( geodir_payment_general_options() );?>
			
			<p class="submit">
				
			<input name="save" class="button-primary" type="submit" value="<?php _e( 'Save changes', 'geodir_payments' ); ?>" />
			<input type="hidden" name="subtab" value="geodir_payment_general_options" id="last_tab" />
			</p>
			</div>
			
		<?php
		break;
		case 'payment_notifications' :
		
			geodir_admin_fields( geodir_payment_notifications() ); ?>
			
			<p class="submit">
				
			<input name="save" class="button-primary" type="submit" value="<?php _e( 'Save changes', 'geodir_payments' ); ?>" />
			<input type="hidden" name="subtab" value="payment_notifications" id="last_tab" />
			</p>
			</div>
			
		<?php break;
		
	}// end of switch
}

function geodir_payment_checkout_page_content() {	
	$cart = geodir_payment_get_cart();
/***************************** Code edited by kindlebit 25th-jan-2016 **************************************/
		global $wpdb;
		
		$get_post_type = $wpdb->get_row( "SELECT `post_type` FROM `".$wpdb->base_prefix."posts` WHERE `ID` = '".$cart->post_id."' AND `post_author` = '".$cart->user_id."'" );
		$post_type = $get_post_type->post_type;
		
	
		$get_data = $wpdb->get_row( "SELECT * FROM `".$wpdb->base_prefix."geodir_".$post_type."_detail` WHERE `post_id` = '".$cart->post_id."'" );

		$invoice_id = $wpdb->get_row("SELECT `id` FROM `".$wpdb->base_prefix."geodir_invoice` WHERE `post_id` = '".$cart->post_id."'");
		
		
		
		/***************** code edited by kindlebit on 25th-jan-2016 ********************/
		
		/******************** Update price on the invoice page ************************/
		$total = "";
		$tax = "";
		$tax_price = "";
		$initial_price = "";
		
		if(isset($invoice_id->id)){
			
			   $invoice_table = $wpdb->base_prefix.'geodir_invoice';
			
			   $get_invoice_details = $wpdb->get_row( "SELECT `post_id` FROM `$invoice_table` WHERE id = '".$invoice_id->id."'" );
			   
			   $get_post_type = $wpdb->get_row( "SELECT `post_type` FROM `".$wpdb->base_prefix."posts` WHERE id = '".$get_invoice_details->post_id."'" );
		
			   $get_invoice_data = $wpdb->get_row( "SELECT * FROM `".$wpdb->base_prefix."geodir_".$get_post_type->post_type."_detail` WHERE `post_id` = '".$get_invoice_details->post_id."'" );
			   
				$cart_data = json_decode($get_invoice_data->geodir_taxdata);
				
				$total = $cart_data->Final_List_Price;
				
				if($cart->discount > 0){
				  
				 $total = $total - $cart->discount;
				  
				}
				

				
				$tax = $cart_data->Tax_Applied;
				$tax_price = $cart_data->Tax_Amount_Added_on_List_Price;
				$initial_price = $cart_data->List_Price;
			
					  $wpdb->update( 
						  $invoice_table, 
						  array( 
							  'paied_amount' => $total,	// string
							  'tax_amount' => $tax_price,	// integer (number)
							  'amount' => $initial_price
						  ), 
						  array( 'ID' => $invoice_id->id )
					  );
					  
				?>
				
<script type='text/javascript'>

(function()
{
  if( window.localStorage )
  {
    if( !localStorage.getItem( 'firstLoad' ) )
    {
      localStorage[ 'firstLoad' ] = true;
      window.location.reload();
    }  
    else
      localStorage.removeItem( 'firstLoad' );
  }
})();

</script>				
				
				<?php
		}
		
		/******************** Update price on the invoice page ************************/	
			
		/***************** code edited by kindlebit on 25th-jan-2016 ********************/			
		
		if($get_data->geodir_usertype == "business-user" && $get_data->geodir_validatedoc != 1){ ?>
		  
			<script type="text/javascript">
			  jQuery(document).ready(function(){
			
				  jQuery('.gd-cart-payments').html('');
				  jQuery('.gd-cart-payments').html('<p>You can do the payment after Doc validation.</p>');
				
			  });
			</script>		  

		  
		<?php }
/***************************** Code edited by kindlebit 25th-jan-2016 **************************************/	
	
	
	if ( !empty( $cart ) ) {	
		wp_enqueue_style( 'gd_payment-cart-style', plugins_url( '', __FILE__ ) . '/css/gd-cart.css', array(), GEODIRPAYMENT_VERSION );
		
		add_action( 'wp_footer', 'geodir_payment_localize_all_js_msg' );
		
		wp_register_script( 'gd_payment-cart-js', plugins_url( '',__FILE__ ) . '/js/gd-cart.js', array(), GEODIRPAYMENT_VERSION );
		wp_enqueue_script( 'gd_payment-cart-js' );
		
		$recurring_pkg = geodir_payment_invoice_is_recurring_pkg( $cart );
		$payment_methods = geodir_payment_get_methods( $recurring_pkg );
		
		$form_action = get_page_link( geodir_payment_checkout_page_id() );
		
		$post_type = geodir_payment_cart_post_type( $cart->id );

		$item_name = $cart->post_title;
		$coupon_code = trim( $cart->coupon_code );
		$coupon_code = geodir_is_valid_coupon( $post_type, $coupon_code ) ? $coupon_code : '';
		
		$payment_method = $cart->paymentmethod;
		
		$amount = $cart->amount;
		$tax_amount = $cart->tax_amount;
		$discount = $cart->discount;
		$paied_amount = $cart->paied_amount;
		
		$amount_display = $cart->amount_display;
		$tax_amount_display = $cart->tax_amount_display;
		$discount_display = $cart->discount_display;
		$paied_amount_display = $cart->paied_amount_display;
		
		$coupon_allowed = geodir_payment_allow_coupon_usage(array('cart_id' => $cart->id));
	?>
	<div class="gd-checkout-box clearfix" id="gd_checkout_box">

		<form id="gd_checkout_form" action="<?php echo esc_attr( $form_action );?>" method="post" enctype="multipart/form-data">
		<input type="hidden" id="gd_cart_nonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'gd_cart_nonce' ); ?>" />
		<input type="hidden" name="geodir_ajax" value="checkout" />
		<?php do_action( 'geodir_checkout_before_form_fields' ); ?>
		<div class="gd-cart-info clearfix">
			<table class="gd-cart-tbl">
				<thead>
					<tr>
						<td><?php _e( 'Item' , 'geodir_payments' );?></td>
						<td class="gd-cart-price"><?php _e( 'Price', 'geodir_payments' );?></td>
					</tr>
				</thead>
				<tbody>
					<tr class="gd-cart-item gd-cart-amount">
						<td class="gd-item-name"><?php echo $item_name ;?></td>
						<td class="gd-cart-price"><?php echo $amount_display ;?></td>
					</tr>
				</tbody>
				<tfoot>
					<?php if ( $tax_amount > 0 || $coupon_allowed ) { ?>
					<tr class="gd-cart-subtotal gd-cart-bold">
						<td class="gd-item-name"><?php _e( 'Sub-Total:', 'geodir_payments' );?></td>
						<td class="gd-cart-price"><?php echo $amount_display ;?></td>
					</tr>
					<?php } ?>
					<?php if ( $tax_amount > 0 ) { ?>
					<tr class="gd-cart-tax">
						<td class="gd-item-name"><?php _e( 'Tax:', 'geodir_payments' );?></td>
						<td class="gd-cart-price"><?php echo $tax_amount_display ;?></td>
					</tr>
					<?php } ?>
					<?php if ( $coupon_allowed ) { ?>
					<tr class="gd-cart-discount">
						<td class="gd-item-name"><div class="gd-cart-coupon"><input type="text" id="gd_coupon" value="<?php echo esc_attr( $coupon_code );?>" /> <input type="button" id="gd_coupon_btn" value="<?php esc_attr_e( 'Update Coupon', 'geodir_payments' );?>" class="button btn-secondary" /></div><?php echo wp_sprintf( __( 'Discount%s:' , 'geodir_payments' ), ( $coupon_code != '' ? ' ( ' . $coupon_code . ' )' : '' ) );?></td>
						<td class="gd-cart-price"><?php echo $discount_display ;?></td>
					</tr>
					<?php } ?>
					<tr class="gd-cart-total gd-cart-gry gd-cart-bold">
						<td class="gd-item-name"><?php _e( 'Total:', 'geodir_payments' );?></td>
						<td class="gd-cart-price"><?php echo $paied_amount_display ;?></td>
					</tr>
				</tfoot>
			</table>
		</div>
		<div class="gd-cart-payments clearfix">
			<?php if ( $paied_amount > 0 ) { ?>
			<div class="geodir_form_row clearfix gdc-pmethod-caption clearfix">
				<label><?php _e( 'Select Payment Method', 'geodir_payments' );?></label>
			</div>
			<ul class="gd-cart-pmethods clearfix">
			<?php 
				if ( !empty( $payment_methods ) ) {
					$i = 0;
					foreach ( $payment_methods as $method ) {
						$i++;						
						$name = $method['key'];
						$title = stripslashes_deep( __( $method['name'], 'geodir_payments' ) );
						
						if ( $i == 1 && $payment_method == '' ) {
							$payment_method = $name;
						}
						
						$button_title = '';
						$button_title = apply_filters( 'geodir_payment_cart_button_text', $button_title, $name );
						?>
						<li class="gd-pmethod-<?php echo $name;?>"><input type="radio" data-btn-txt="<?php echo esc_attr( $button_title );?>" value="<?php echo $name;?>" name="gd_payment_method" class="input-radio" id="gd_pmethod_<?php echo $name;?>" <?php checked( $payment_method, $name );?> /><label for="gd_pmethod_<?php echo $name;?>"><?php echo $title;?></label>
						<div class="gd-payment-box gd_pmethod_<?php echo $name;?>" style="display:none;">
						<?php do_action( 'geodir_payment_method_fields', $name );?>
						</li>
						<?php
					}
				} else {
					?>
					<li><?php _e( 'No payment method available!', 'geodir_payments' );?></li>
					<?php
				}
			?>
			</ul>
			<div class="gd-checkout-actions clearfix">
				<input type="submit" value="<?php esc_attr_e( 'Pay & Publish' , 'geodir_payments' );?>" data-btn-txt="<?php esc_attr_e( 'Pay & Publish' , 'geodir_payments' );?>" id="gd_checkout_paynow" name="gd_checkout_paynow" class="button btn-primary" />
			</div>
			<?php } else { ?>
			<div class="gd-checkout-actions gd-checkout-action-publish clearfix">
				<input type="hidden" value="<?php echo wp_create_nonce( 'gd_checkout_publish' . $cart->id );?>" name="gd_checkout_publish" />
				<input type="submit" value="<?php esc_attr_e( 'Publish' , 'geodir_payments' );?>" id="gd_checkout_paynow" name="gd_checkout_paynow" class="button btn-primary" />
			</div>
			<?php } ?>
		</div>
		<?php do_action( 'geodir_checkout_after_form_fields' ); ?>
		</form>
	</div>
	<?php
	} else {
		?>
		<div class="gd-checkout-box gd-empty-cart clearfix" id="gd_checkout_box"><label><?php _e( 'Oops! No data found.', 'geodir_payments' );?></label></div>
		<?php
	}
}
add_action( 'geodir_checkout_page_content', 'geodir_payment_checkout_page_content' );

function geodir_payment_invoices_page_content( $is_ajax = false ) {		
	$user_id = get_current_user_id();
	
	if ( !$user_id ) {
		wp_redirect( geodir_login_url());
		exit();
	}
	
	if ( !$is_ajax ) {
		geodir_payment_add_invoice_scripts();
	}
	
	$pageno = isset($_REQUEST['pageno']) ? (int)$_REQUEST['pageno'] : 1;
	
	$per_page = 10;
	$per_page = apply_filters('geodir_payment_user_invoices_per_page', $per_page);
	
	$args = array();
	$args['filter'] = array( 'user_id' => $user_id );
	$args['per_page'] = $per_page;
	$args['pageno'] = $pageno;
	$args['order_by'] = 'date_updated DESC';
	$args['count_only'] = true;
	
	$total_invoices = geodir_payment_user_invoices( $args );
	
	$args['count_only'] = false;
	$args['total'] = $total_invoices;
	$invoices = $total_invoices ? geodir_payment_user_invoices( $args ) : array();
	
	ob_start();
	?>
	<?php if ( !$is_ajax ) { ?>
	<div class="entry-content">
		<div id="gd_payment_invoices" class="gd-payment-invoices">
			<?php } ?>
			<!-----Added by Anuj ---->
				<link rel="stylesheet" href="<?php echo plugins_url('geodir_invoice/css/colorbox.css');?>" />
				<script src="https://code.jquery.com/jquery-2.2.2.js" integrity="sha256-4/zUCqiq0kqxhZIyp4G0Gk+AOtCJsY1TA00k5ClsZYE="		   crossorigin="anonymous"></script>
				<script type="text/javascript" src="<?php echo plugins_url('geodir_invoice/js/jquery.colorbox.js');?>"></script>
				<script type="text/javascript">
					$(document).ready(function(){
						$(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
						$(".inline").colorbox({inline:true, width:"50%"});
					});
				</script>

			<!-----Added end by Anuj-->
			
			<table class="gdp-invoices-table">
				<thead>
					<tr>
						<th class="gd-inv-no"><?php _e( '#', 'geodir_payments' ); ?></th>
						<th class="gd-inv-item"><?php _e( 'Item', 'geodir_payments' ); ?></th>
						<th class="gd-inv-amount"><?php _e( 'Amount', 'geodir_payments' ); ?></th>
						<th class="gd-inv-status"><?php _e( 'Status', 'geodir_payments' ); ?></th>
						<th class="gd-inv-date"><?php _e( 'Date', 'geodir_payments' ); ?></th>
					</tr>
				</thead>
		
				<tbody>
					<?php if ( !empty( $invoices ) ) { ?>
						<?php 
						foreach ( $invoices as $invoice ) { 
							$invoice_id = $invoice->id;
							$date = $invoice->date_updated != '0000-00-00 00:00:00' ? $invoice->date_updated : $invoice->date;
							$date = $date != '0000-00-00 00:00:00' ? $date : '';
							$date_display = $date != '' ? date_i18n( geodir_default_date_format(), strtotime( $date ) ) : '';
							
							$amount = geodir_payment_price( $invoice->paied_amount );
							
							$inv_status = $invoice->status;
							if ( in_array( geodir_strtolower( $inv_status ), array( 'paid', 'active', 'subscription-payment', 'free' ) ) ) {
								$inv_status = 'confirmed';
							} else if ( in_array( geodir_strtolower( $inv_status ), array( 'unpaid' ) ) ) {
								$inv_status = 'pending';
							}
							$incomplete = $inv_status == 'pending' && empty($invoice->paymentmethod) ? true : false;
							
							$status_text = geodir_payment_status_name( $inv_status );
							if ($incomplete && $inv_status == 'pending') {
								$status_text = __('Incomplete', 'geodir_payments');
							}
							
							$payment_method_title = geodir_payment_method_title( $invoice->paymentmethod );
							$payment_method_text = $payment_method_title != '' ? '<small class="gd-inv-meta">' . wp_sprintf( 'Via %s', $payment_method_title ). '</small>' : '';
							
							$title_meta 	= geodir_payment_invoice_info_title_meta( $invoice );
							$status_meta 	= geodir_payment_invoice_info_status_meta( $invoice );
							
							$invoice_details = geodir_payment_invoice_view_details( $invoice );
							
							$invoice_nonce = wp_create_nonce( 'gd_invoice_nonce_' . $invoice_id );
						?>
							<tr class="gd-inv-row gd-inv-row-<?php echo $invoice_id; ?> gd-inv-<?php echo $inv_status; ?>" data-id="<?php echo $invoice_id; ?>" data-status="<?php echo $inv_status; ?>">
								<td class="gd-inv-no"><?php echo $invoice_id; ?><input type="hidden" class="gd-inv-nonce" value="<?php echo $invoice_nonce; ?>" /></td>
								<td class="gd-inv-item"><?php echo $invoice->post_title; ?><small class="gd-inv-meta anuj"><?php echo $title_meta; ?></small>&nbsp;&nbsp;
								<!--small class="gd-inv-meta anuj">
									<a class='inline' style="text-decoration:none;" href="#inline_content-<?php echo $invoice_id; ?>">View Invoice</a>
								</small-->
								</td>
								<td class="gd-inv-amount"><?php echo $amount . $payment_method_text; ?></td>
								<td class="gd-inv-status"><?php echo $status_text; ?><small class="gd-inv-meta"><?php echo $status_meta; ?></small></td>
								<td class="gd-inv-date" title="<?php esc_attr_e( $date );?>"><?php echo $date_display;?></td>
							</tr>
							<tr class="gd-inv-row gd-inv-info gd-inv-info-<?php echo $invoice_id; ?>" data-status="<?php echo $invoice->status; ?>" style="display:none">
								<td colspan="5"><?php echo $invoice_details;?></td>
							</tr>
							<tr>
								<td>
							<!-----------------Invoice Content Start 22-03-2016 - KBS - @nuj ------------------------>
							<div style='display:none'>
								<div id="inline_content-<?php echo $invoice_id;?>" style='padding:10px; background:#fff;'>
									
									<?php require('kbs-gd-inv.php');?>
								</div>
							</div>
							<?php
							
							?>
							<!----------------------END Data------------------->
							</td>
							</tr>
						<?php 
						} ?>
					<?php } else { ?>
						<tr>
							<td colspan="5"><?php _e( 'You have not made any purchase yet.', 'geodir_payments' ); ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			
			<?php if ( $total_invoices > 0 ) { ?>
			<div class="gd-inv-pagination"><?php echo geodir_payment_invoices_pagination( $total_invoices, $per_page, $pageno) ;?></div>
			<p class="gdp-invoices-loading" style="display:none;"><i class="fa fa-cog fa-spin"></i></p>
			<?php } ?>
			<?php if ( !$is_ajax ) { ?>
		</div>
	</div>
	<?php } ?>
	<?php
	$content = ob_get_clean();
	
	if ( $is_ajax ) {
		return $content;
	} else {
		echo $content;
	}
}

function geodir_payment_invoice_detail_page_content( $invoice_id = NULL ) {
  
  
		/***************** code edited by kindlebit on 25th-jan-2016 ********************/
		
		/******************** Update price on the invoice page ************************/
		global $wpdb;
		$_SESSION['tax_Cal_data'] = $html_data;
		$total = "";
		$tax = "";
		$tax_price = "";
		$initial_price = "";
		
		if(isset($_GET['invoice_id'])){
			
			   $invoice_table = $wpdb->base_prefix.'geodir_invoice';
			
			   $get_invoice_details = $wpdb->get_row( "SELECT `discount`,`post_id` FROM `$invoice_table` WHERE id = '".$_GET['invoice_id']."'" );
			   
			   $get_post_type = $wpdb->get_row( "SELECT `post_type` FROM `".$wpdb->base_prefix."posts` WHERE id = '".$get_invoice_details->post_id."'" );
		
			   $get_invoice_data = $wpdb->get_row( "SELECT * FROM `".$wpdb->base_prefix."geodir_".$get_post_type->post_type."_detail` WHERE `post_id` = '".$get_invoice_details->post_id."'" );
			   
				$cart_data = json_decode($get_invoice_data->geodir_taxdata);
				
				$total = $cart_data->Final_List_Price;
				$tax = $cart_data->Tax_Applied;
				$tax_price = $cart_data->Tax_Amount_Added_on_List_Price;
				$initial_price = $cart_data->List_Price;


				if($get_invoice_details->discount > 0){
				  
				 $total = $total - $get_invoice_details->discount;
				  
				}
				
					  $wpdb->update( 
						  $invoice_table, 
						  array( 
							  'paied_amount' => $total,	// string
							  'tax_amount' => $tax_price,	// integer (number)
							  'amount' => $initial_price
						  ), 
						  array( 'ID' => $_GET['invoice_id'] )
					  );
		}
		
		/******************** Update price on the invoice page ************************/	
			
		/***************** code edited by kindlebit on 25th-jan-2016 ********************/	  
  
  
  
	$user_id = get_current_user_id();
	
	if ( !$user_id ) {
		wp_redirect( geodir_login_url() );
		exit();
	}
	
	$invoice_info 	= geodir_get_invoice( $invoice_id );
	$is_owner 		= geodir_payment_check_invoice_owner( $invoice_info, $user_id );
	
	if ( !$is_owner || empty( $invoice_info ) ) {
		wp_redirect( geodir_payment_invoices_page_link() );
		exit();
	}
	
	geodir_payment_add_invoice_scripts();
	
	$item_name = $invoice_info->post_title;
	$coupon_code = trim( $invoice_info->coupon_code );
	
	$payment_method = $invoice_info->paymentmethod;
	
	$invoice_type = $invoice_info->invoice_type;
	$post_id = $invoice_info->post_id;
	$amount = $invoice_info->amount;
	$tax_amount = $invoice_info->tax_amount;
	$discount = $invoice_info->discount;
	$paied_amount = $invoice_info->paied_amount;
	$date = $invoice_info->date;
	$date_updated = $invoice_info->date_updated;
	
	$amount_display = geodir_payment_price($amount);
	$tax_amount_display = geodir_payment_price($tax_amount);
	$discount_display = geodir_payment_price($discount);
	$paied_amount_display = geodir_payment_price($paied_amount);
	
	$coupon_allowed = get_option( 'geodir_allow_coupon_code' );
	
	$pay_for_invoice = geodir_payment_allow_pay_for_invoice( $invoice_info );
	
	$invoice_details = geodir_payment_invoice_view_details( $invoice_info );	
	$invoice_nonce = wp_create_nonce( 'gd_invoice_nonce_' . $invoice_id );	
	
	$date = $date_updated != '0000-00-00 00:00:00' ? $date_updated : $date;
	$date = $date != '0000-00-00 00:00:00' ? $date : '';
	$date_display = $date != '' ? date_i18n( geodir_default_date_format(), strtotime( $date ) ) : '';
	
	$dat_format = geodir_default_date_format() . ' ' . get_option( 'time_format' );
	$date_updated_display = $date != '' ? date_i18n( $dat_format, strtotime( $date ) ) : '';
	
	$payment_method_display = geodir_payment_method_title( $payment_method );
	
	$inv_status = $invoice_info->status;
	if ( in_array( geodir_strtolower( $inv_status ), array( 'paid', 'active', 'subscription-payment', 'free' ) ) ) {
		$inv_status = 'confirmed';
	} else if ( in_array( geodir_strtolower( $inv_status ), array( 'unpaid' ) ) ) {
		$inv_status = 'pending';
	}
	
	$status_display = geodir_payment_status_name( $inv_status );
	$invoice_type_name = geodir_payment_invoice_type_name( $invoice_type );
	
	$incomplete = $inv_status == 'pending' && empty($invoice_info->paymentmethod) ? true : false;
	if ($incomplete && $inv_status == 'pending') {
		$status_display = __('Incomplete', 'geodir_payments');
	}
	
	$listing_display = '';
	$package_display = '';
	if ( ( $invoice_type == 'add_listing' || $invoice_type == '' || $invoice_type == 'upgrade_listing' || $invoice_type == 'renew_listing' || $invoice_type == 'claim_listing' ) && $post_id > 0 ) {
		$post_status = get_post_status( $post_id );
		$listing_display = get_the_title( $post_id );
		
		if ( $post_status == 'publish' || $post_status == 'private' ) {
			$listing_display = '<a href="' . get_permalink( $post_id ) . '" target="_blank">' . $listing_display . '</a>';
		}
		
		$package_id = $invoice_info->package_id;
		$package_display = $invoice_info->package_title;
	}
	//echo "<pre>"; print_r($invoice_info);echo "</pre>";
	$transaction_details = trim($invoice_info->HTML) != '' ? trim($invoice_info->HTML) : NULL;
	
	if ( !$invoice_info->paied_amount > 0 ) {
		$payment_method_display = __( 'Instant Publish', 'geodir_payments' );
	}
	ob_start();
	
	/******************************/
		$get_post_type = $wpdb->get_row( "SELECT `post_author`,`post_type` FROM `".$wpdb->base_prefix."posts` WHERE `ID` = '".$post_id."'" );
		
		$post_type = $get_post_type->post_type;		
		
		$get_data = $wpdb->get_row( "SELECT * FROM `".$wpdb->base_prefix."geodir_".$post_type."_detail` WHERE `post_id` = '".$post_id."'" );
		
		
	
	/************************/
	?>
	
	<style>
		#gd_pmt_invoice_detail > a {  background: #31baeb none repeat scroll 0 0;  color: #fff;  float: right;  padding: 6px;  position: relative;
  text-decoration: none;  top: -43px;}
		#gd_pmt_invoice_detail > a:hover { background: #119acb none repeat scroll 0 0;  padding: 6px;  font-size: inherit;}
	</style>
	
	<div class="entry-content gd-pmt-invoice-detail gd-pmt-invoice-<?php echo $inv_status;?>" id="gd_pmt_invoice_detail">
	
	<a href="<?php echo plugins_url('geodir_payment_manager/kbs-inv-pdf.php?kbs_gdinvoice_id='.$invoice_id.'&cur_user_id='.get_current_user_id());?>" >Click Here to download</a>
	
		<h4><?php _e( 'Invoice Details:' , 'geodir_payments' );?></h4>
		<?php do_action( 'geodir_payment_invoice_before_order_details', $invoice_info ); ?>
		<ul class="gd-order-details">
			<li class="gd-pmt-order"><?php _e( 'Invoice:' , 'geodir_payments' );?><strong>#<?php echo $invoice_id;?></strong></li>
			<li class="gd-pmt-date" title="<?php esc_attr_e( $date );?>"><?php _e( 'Date:' , 'geodir_payments' );?><strong><?php echo $date_display;?></strong></li>
			<li class="gd-pmt-total"><?php _e( 'Total:' , 'geodir_payments' );?><strong><?php echo $paied_amount_display;?></strong></li>
			<li class="gd-pmt-method"><?php _e( 'Payment Method:' , 'geodir_payments' );?><strong><?php echo $payment_method_display;?></strong></li>
			<li class="gd-pmt-status"><?php _e( 'Status:' , 'geodir_payments' );?><strong><?php echo $status_display;?></strong></li>
		</ul>
		<?php do_action( 'geodir_payment_invoice_after_order_details', $invoice_info ); ?>
		<h4><?php _e( 'Item Details:' , 'geodir_payments' );?></h4>
		<?php do_action( 'geodir_payment_invoice_before_cart_details', $invoice_info ); ?>
		<div class="gd-invoice-detail-info clearfix">
			<?php //echo "<pre>";print_r($invoice_info);echo "</pre>"?>
			<table class="gd-cart-tbl">
				<thead>
					<tr>
						<td><?php _e( 'Item' , 'geodir_payments' );?></td>
						<td class="gd-cart-price"><?php _e( 'Price', 'geodir_payments' );?></td>
					</tr>
				</thead>
				<tbody>
					<tr class="gd-cart-item gd-cart-amount">
						<td class="gd-item-name"><?php echo $item_name ;?></td>
						<td class="gd-cart-price"><?php echo $amount_display ;?></td>
					</tr>					
				</tbody>
				<tfoot>
					<?php if ( $tax_amount > 0 || $discount > 0 ) { ?>
					<tr class="gd-cart-subtotal gd-cart-bold">
						<td class="gd-item-name"><?php _e( 'Sub-Total:', 'geodir_payments' );?></td>
						<td class="gd-cart-price"><?php echo $amount_display ;?></td>
					</tr>
					<?php } ?>
					<?php if ( $tax_amount > 0 ) { ?>
					<tr class="gd-cart-tax">
						<td class="gd-item-name"><?php _e( 'Tax:', 'geodir_payments' );?></td>
						<td class="gd-cart-price"><?php echo $tax_amount_display ;?></td>
					</tr>
					<?php } ?>
					<?php if ( $coupon_allowed || $discount > 0 ) { ?>
					<tr class="gd-cart-discount">
						<td class="gd-item-name"><?php echo wp_sprintf( __( 'Discount%s:' , 'geodir_payments' ), ( $coupon_code != '' ? ' ( ' . $coupon_code . ' )' : '' ) );?></td>
						<td class="gd-cart-price"><?php echo $discount_display ;?></td>
					</tr>
					<?php } ?>
					<tr class="gd-cart-total gd-cart-gry gd-cart-bold">
						<td class="gd-item-name"><?php _e( 'Total:', 'geodir_payments' );?></td>
						<td class="gd-cart-price"><?php echo $paied_amount_display ;?></td>
					</tr>
				</tfoot>
			</table>
		</div>
		<?php do_action( 'geodir_payment_invoice_after_cart_details', $invoice_info ); ?>
		<?php do_action( 'geodir_payment_invoice_before_custom_details', $invoice_info ); ?>
		<div class="gd-pmt-custom-detail clearfix <?php echo $transaction_details ? 'gd-pmt-custom-trans' : '';?>">
			<div class="gd-pmt-listing-detail clearfix">
				<h4><?php _e( 'Listing Details:' , 'geodir_payments' );?></h4>
				<div class="gd-inv-detail">
					<label class="gd-inv-lbl"><?php _e( 'Type:', 'geodir_payments' );?> </label>
					<span class="gd-inv-val"><?php echo $invoice_type_name;?></span>
				</div>
				<?php do_action( 'geodir_payment_invoice_before_listing_details', $invoice_info ); ?>
				<?php if ( $listing_display ) { ?>
				<div class="gd-inv-detail">
					<label class="gd-inv-lbl"><?php _e( 'Listing ID:', 'geodir_payments' );?> </label>
					<span class="gd-inv-val"><?php echo $post_id;?></span>
				</div>
				<div class="gd-inv-detail">
					<label class="gd-inv-lbl"><?php _e( 'Listing Title:', 'geodir_payments' );?> </label>
					<span class="gd-inv-val"><?php echo $listing_display;?></span>
				</div>
				<?php } if ( $package_display ) { ?>
				<div class="gd-inv-detail">
					<label class="gd-inv-lbl"><?php _e( 'Package ID:', 'geodir_payments' );?> </label>
					<span class="gd-inv-val"><?php echo $package_id;?></span>
				</div>
				<div class="gd-inv-detail">
					<label class="gd-inv-lbl"><?php _e( 'Package:', 'geodir_payments' );?> </label>
					<span class="gd-inv-val"><?php echo $package_display;?></span>
				</div>
				<?php } if ( $date_updated_display ) { ?>
				<div class="gd-inv-detail">
					<label class="gd-inv-lbl"><?php _e( 'Last Updated:', 'geodir_payments' );?> </label>
					<span class="gd-inv-val" title="<?php echo $date;?>"><?php echo $date_updated_display;?></span>
				</div>
				<?php } ?>
				<?php do_action( 'geodir_payment_invoice_after_listing_details', $invoice_info ); ?>
			</div>
			<?php if ( $transaction_details ) { ?>
			<div class="gd-pmt-trans-detail clearfix"><h4><?php _e( 'Transaction Details:' , 'geodir_payments' );?></h4><span class="gd-trans-text"><?php echo $transaction_details;?></span></div>
			<?php } ?>
		</div>
		
		
		<?php
		/***************************** Code edited by kindlebit 25th-jan-2016 **************************************/
		
		if($get_data->geodir_usertype == "business-user" && $get_data->geodir_validatedoc != 1){ ?>
		  
			<style>
			.button.btn-primary {
			  display: none;
			}
			</style>
		  
		 <?php } ?>
		
		<div style="margin-top: 15px;" class="gd-pmt-custom-detail clearfix <?php echo $transaction_details ? 'gd-pmt-custom-trans' : '';?>">
			  <div class="gd-pmt-listing-detail clearfix">
				  <h4>User Details:</h4>
					  <div class="gd-inv-detail">
						  <label class="gd-inv-lbl">First Name: </label>
						  <span class="gd-inv-val"><?php echo $get_data->geodir_billingfirstname; ?></span>
					  </div>
					  
					  <div class="gd-inv-detail">
						  <label class="gd-inv-lbl">Last Name: </label>
						  <span class="gd-inv-val"><?php echo $get_data->geodir_billinglastname; ?></span>
					  </div>
					  
					  <?php if($get_data->geodir_usertype == "business-user"){ ?>
					  
					  <div class="gd-inv-detail">
						  <label class="gd-inv-lbl">Company Name: </label>
						  <span class="gd-inv-val"><?php echo $get_data->geodir_companyname; ?></span>
					  </div>
					  
					  <div class="gd-inv-detail">
						  <label class="gd-inv-lbl">User Nationality: </label>
						  <span class="gd-inv-val"><?php echo $get_data->geodir_nationalitycheck; ?></span>
					  </div>
					  
					  <div class="gd-inv-detail">
						  <label class="gd-inv-lbl">Company Vat : </label>
						  <span class="gd-inv-val"><?php echo $get_data->geodir_vatid; ?></span>
					  </div>
					  
					  <div class="gd-inv-detail">
						  <label class="gd-inv-lbl">Company City: </label>
						  <span class="gd-inv-val"><?php echo $get_data->geodir_companycity; ?></span>
					  </div>					  
					  
					  <div class="gd-inv-detail">
						  <label class="gd-inv-lbl">Company Address: </label>
						  <span class="gd-inv-val"><?php echo $get_data->geodir_companyaddress; ?></span>
					  </div>						
					  
					  <?php }else{ ?>
					  
					  <div class="gd-inv-detail">
						  <label class="gd-inv-lbl">Billing City: </label>
						  <span class="gd-inv-val"><?php echo $get_data->geodir_billingcity; ?></span>
					  </div>					  
					  
					  <div class="gd-inv-detail">
						  <label class="gd-inv-lbl">Billing Address: </label>
						  <span class="gd-inv-val"><?php echo $get_data->geodir_billingaddress; ?></span>
					  </div>						
					  
					  <?php } ?>
					  
					  <div class="gd-inv-detail">
						  <label class="gd-inv-lbl">Customer Id: </label>
						  <span class="gd-inv-val"><?php echo $get_post_type->post_author; ?></span>
					  </div>					  
					  <?php
						$message = "";
						switch($get_data->geodir_nationalitycheck){
							case "International User":
							$message = 'Note on the transfer of tax liability eg by specifying "tax liability of the beneficiary" 
(=REVERSE CHARGE MEACHNISM | Para § 13b UStG > The Recipient of the service is liable for VAT payments, because of “not within provider country taxable supply”. Accepted from: '.$get_data->geodir_billingfirstname." | " .date("d-m-Y H:i:s");
							break;	
							case "Local User":
							$message = "";
							break;
							case "European User":
							$message = 'Note on the transfer of tax liability eg by specifying "tax liability of the beneficiary" (=REVERSE CHARGE MEACHNISM | Para § 13b UStG > The Recipient of the service is liable for VAT payments, because of “not within provider country taxable supply”.  '.$get_data->geodir_billingfirstname." | " .date("d-m-Y H:i:s");
							break;
						}
					  ?>
			  </div>
			   <p style="clear:both;"><?php echo $message;?></p>
		</div>
		
		<?php 
		/***************************** Code edited by kindlebit 25th-jan-2016 **************************************/
		
		do_action( 'geodir_payment_invoice_after_custom_details', $invoice_info ); ?>
		<div class="gd-checkout-actions clearfix">
			<input class="gd-pmt-btnall" type="button" onclick="window.location.href='<?php echo esc_url(geodir_payment_invoices_page_link());?>'" class="button btn-primary" value="<?php esc_attr_e( 'View All Invoices' , 'geodir_payments' );?>" />
			<?php if ( $pay_for_invoice ) { ?>
			<input type="hidden" class="gd-inv-nonce" value="<?php echo $invoice_nonce; ?>" />
			<input type="button" onclick="<?php echo 'gd_invoice_paynow(' . (int)$invoice_id . ', jQuery(\'#gd_pmt_invoice_detail\'));';?>" value="<?php esc_attr_e( 'Pay For Invoice' , 'geodir_payments' );?>" class="button btn-primary" />
			<?php } ?>
		</div>
	</div>
	<?php
	$content = ob_get_clean();
	
	echo $content;
}

function geodir_payment_user_invoices( $args ) {
	global $wpdb;
	$where = array();
	if ( !empty( $args['filter'] ) ) {
		$where = '';
		foreach ( $args['filter'] as $key => $value ) {
			$where[] = $wpdb->prepare( $key . " = %s", array( $value ) );
		}
	}
	$where = !empty( $where ) ? implode( " AND ", $where ) : '';
	
	$where = apply_filters( 'geodir_payment_user_invoices_where', $where, $args );
	$where = $where != '' ? " WHERE " . $where : '';
	
	$order_by = !empty( $args['order_by'] ) ? $args['order_by'] : 'id DESC';
	$order_by = apply_filters( 'geodir_payment_user_invoices_order_by', $order_by, $args );
	$order_by = $order_by != '' ? " ORDER BY " . $order_by : '';
	
	$limit = '';
	$fiedls = '*';
	if ( !empty( $args['count_only'] ) ) {
		$query = "SELECT COUNT(id) FROM " . INVOICE_TABLE . " ". $where;
		$result = (int)$wpdb->get_var($query);
	} else {
		$limit = !empty($args['per_page']) ? $args['per_page'] : 10;
		
		$page = !empty($args['pageno']) ? absint($args['pageno']) : 1;
		if ( !$page ) {
			$page = 1;
		}
		
		if ( $args['total'] < absint( $page * $limit ) ) {
			$page = ceil( $args['total'] / $limit );
		}
		
		$limit = (int)$limit > 0 ? " LIMIT " . absint( ( $page - 1 ) * (int)$limit ) . ", " . (int)$limit : "";
		
		$query = "SELECT *, IF(date_updated != '0000-00-00 00:00:00', date_updated, date) AS date_updated FROM " . INVOICE_TABLE . " ". $where . " " . $order_by . " " . $limit;
		$result = $wpdb->get_results($query);
	}
	
	return $result;
}

function geodir_payment_invoices_pagination($total_items, $per_page, $pageno, $before = '', $after = '', $prelabel = '', $nxtlabel = '', $pages_to_show = 5, $always_show = false) {
    if (empty($prelabel)) {
        $prelabel = '<strong>&laquo;</strong>';
    }

    if (empty($nxtlabel)) {
        $nxtlabel = '<strong>&raquo;</strong>';
    }

    $half_pages_to_show = round($pages_to_show / 2);

	$numitems = $total_items;

	$max_page = ceil($numitems / $per_page);

	if (empty($pageno)) {
		$pageno = 1;
	}

	ob_start();
	if ($max_page > 1 || $always_show) {
		// Extra pagination info
		$geodir_pagination_more_info = get_option('geodir_pagination_advance_info');
		$start_no = ( $pageno - 1 ) * $per_page + 1;
		$end_no = min($pageno * $per_page, $numitems);
		
		if ($geodir_pagination_more_info != '') {
			$pagination_info = '<div class="gd-pagination-details">' . wp_sprintf(__('Showing items %d-%d of %d', 'geodir_payments'), $start_no, $end_no, $numitems) . '</div>';
			
			if ($geodir_pagination_more_info == 'before') {
				$before = $before . $pagination_info;
			} else if ($geodir_pagination_more_info == 'after') {
				$after = $pagination_info . $after;
			}
		}
			
		echo "$before <div class='Navi geodir-ajax-pagination'>";		
		if ($pageno > 1) {
			echo '<a class="gd-page-sc-fst" href="javascript:void(0);" onclick="gd_invoice_gopage(this, 1);">&laquo;</a>&nbsp;';
		}
		
		if (($pageno - 1) > 0) {
			echo '<a class="gd-page-sc-prev" href="javascript:void(0);" onclick="gd_invoice_gopage(this, ' . (int)($pageno - 1) . ');">' . $prelabel . '</a>&nbsp;';
		}
		
		for ($i = $pageno - $half_pages_to_show; $i <= $pageno + $half_pages_to_show; $i++) {
			if ($i >= 1 && $i <= $max_page) {
				if ($i == $pageno) {
					echo "<strong class='on' class='gd-page-sc-act'>$i</strong>";
				} else {
					echo ' <a class="gd-page-sc-no" href="javascript:void(0);" onclick="gd_invoice_gopage(this, ' . (int)$i . ');">' . $i . '</a> ';
				}
			}
		}
		
		if (($pageno + 1) <= $max_page) {
			echo '&nbsp;<a class="gd-page-sc-nxt" href="javascript:void(0);" onclick="gd_invoice_gopage(this, ' . (int)($pageno + 1) . ');">' . $nxtlabel . '</a>';
		}
		
		if ($pageno < $max_page) {
			echo '&nbsp;<a class="gd-page-sc-lst" href="javascript:void(0);" onclick="gd_invoice_gopage(this, ' . (int)$max_page . ');">&raquo;</a>';
		}
		echo "</div> $after";
	}
	$output = ob_get_contents();
    ob_end_clean();

    return trim($output);
}

function geodir_payment_invoices_list_page_link( $dashboard_links ) {
	$user_id = get_current_user_id();
	
	if ( $user_id ) {
		$invoices_link = geodir_payment_invoices_page_link();
		
		$dashboard_links .= '<li><i class="fa fa-shopping-cart"></i>&nbsp;&nbsp;<a class="gd-invoice-link" href="' . $invoices_link . '">' . __( 'My Invoice History', 'geodir_payments' ) . '</a></li>';
	}
	
	return $dashboard_links;
}

function geodir_payment_invoice_view_details( $invoice ) {
	$invoice_info = !empty( $invoice ) && is_object( $invoice ) ? $invoice : geodir_get_invoice( $invoice );
	
	if ( !geodir_payment_invoice_is_valid( $invoice_info ) ) {
		return NULL;
	}
	
	
	$invoice_id = $invoice_info->id;
	$post_id = $invoice_info->post_id;
	$invoice_type = $invoice_info->invoice_type;
	$inv_status = $invoice_info->status;
	$date = $invoice_info->date;
	$date_updated = $invoice_info->date_updated;
	$discount = $invoice_info->discount;
	$coupon_code = $invoice_info->coupon_code;
	$tax_amount = $invoice_info->tax_amount;
	
	$amount = geodir_payment_price( $invoice_info->paied_amount );
	$payment_method = geodir_payment_method_title( $invoice_info->paymentmethod );
	
	$dat_format = geodir_default_date_format() . ' ' . get_option( 'time_format' );
	$date_display = $date != '' && $date != '0000-00-00 00:00:00' ? date_i18n( $dat_format, strtotime( $date ) ) : '';
	$date_updated_display = $date != '' && $date_updated != $date ? date_i18n( $dat_format, strtotime( $date_updated ) ) : '';
	
	$tax_display = $tax_amount > 0 ? geodir_payment_price( $tax_amount ) : '';
	$discount_display = $discount > 0 ? geodir_payment_price( $discount ) : '';
	$coupon_display = $discount > 0 && $coupon_code != '' ? $coupon_code : '';
	
	if ( in_array( geodir_strtolower( $inv_status ), array( 'paid', 'active', 'subscription-payment', 'free' ) ) ) {
		$inv_status = 'confirmed';
	} else if ( in_array( geodir_strtolower( $inv_status ), array( 'unpaid' ) ) ) {
		$inv_status = 'pending';
	}
	
	$listing_display = '';
	$package_display = '';
	if ( ( $invoice_type == 'add_listing' || $invoice_type == 'upgrade_listing' || $invoice_type == 'renew_listing' || $invoice_type == 'claim_listing' ) && $post_id > 0 ) {
		$post_status = get_post_status( $post_id );
		$listing_display = get_the_title( $post_id );
		
		if ( $post_status == 'publish' || $post_status == 'private' ) {
			$listing_display = '<a href="' . get_permalink( $post_id ) . '" target="_blank">' . $listing_display . '</a>';
		}
		
		$package_id = $invoice_info->package_id;
		$package_display = $invoice_info->package_title;
	}
	
	$status_text = geodir_payment_status_name( $inv_status );
	$invoice_type_name = geodir_payment_invoice_type_name( $invoice_type );
	
	$invoice_display = trim($invoice_info->HTML);
	ob_start();
	?>
	<div class="gd-inv-detail-box gd-inv-detail-lft">
		<?php do_action( 'geodir_payment_invoice_custom_details_left_before', $invoice_info ); ?>
		<div class="gd-inv-detail">
			<label class="gd-inv-lbl"><?php _e( 'Type:', 'geodir_payments' );?> </label>
			<span class="gd-inv-val"><?php echo $invoice_type_name;?></span>
		</div>
		<?php if ( $listing_display ) { ?>
		<div class="gd-inv-detail">
			<label class="gd-inv-lbl"><?php _e( 'Listing ID:', 'geodir_payments' );?> </label>
			<span class="gd-inv-val"><?php echo $post_id;?></span>
		</div>
		<div class="gd-inv-detail">
			<label class="gd-inv-lbl"><?php _e( 'Listing Title:', 'geodir_payments' );?> </label>
			<span class="gd-inv-val"><?php echo $listing_display;?></span>
		</div>
		<?php } if ( $package_display ) { ?>
		<div class="gd-inv-detail">
			<label class="gd-inv-lbl"><?php _e( 'Package ID:', 'geodir_payments' );?> </label>
			<span class="gd-inv-val"><?php echo $package_id;?></span>
		</div>
		<div class="gd-inv-detail">
			<label class="gd-inv-lbl"><?php _e( 'Package:', 'geodir_payments' );?> </label>
			<span class="gd-inv-val"><?php echo $package_display;?></span>
		</div>
		<?php } ?>
		<div class="gd-inv-detail">
			<label class="gd-inv-lbl"><?php _e( 'Payable Amount:', 'geodir_payments' );?> </label>
			<span class="gd-inv-val"><?php echo $amount;?></span>
		</div>
		<?php if ( $tax_display ) { ?>
		<div class="gd-inv-detail">
			<label class="gd-inv-lbl"><?php _e( 'Tax:', 'geodir_payments' );?> </label>
			<span class="gd-inv-val"><?php echo $tax_display;?></span>
		</div>
		<?php } if ( $discount_display ) { ?>
		<div class="gd-inv-detail">
			<label class="gd-inv-lbl"><?php _e( 'Discount:', 'geodir_payments' );?> </label>
			<span class="gd-inv-val"><?php echo $discount_display;?></span>
		</div>
		<?php } if ( $coupon_display ) { ?>
		<div class="gd-inv-detail">
			<label class="gd-inv-lbl"><?php _e( 'Coupon:', 'geodir_payments' );?> </label>
			<span class="gd-inv-val"><?php echo $coupon_display;?></span>
		</div>
		<?php } ?>
		<?php do_action( 'geodir_payment_invoice_custom_details_left_after', $invoice_info ); ?>
	</div>
	<div class="gd-inv-detail-box gd-inv-detail-rgt">
		<?php do_action( 'geodir_payment_invoice_custom_details_right_before', $invoice_info ); ?>
		<div class="gd-inv-detail">
			<label class="gd-inv-lbl"><?php _e( 'Status:', 'geodir_payments' );?> </label>
			<span class="gd-inv-val"><?php echo $status_text;?></span>
		</div>
		<div class="gd-inv-detail">
			<label class="gd-inv-lbl"><?php _e( 'Payment Method:', 'geodir_payments' );?> </label>
			<span class="gd-inv-val"><?php echo $payment_method;?></span>
		</div>
		<?php if ( $date_display ) { ?>
		<div class="gd-inv-detail">
			<label class="gd-inv-lbl"><?php _e( 'Date:', 'geodir_payments' );?> </label>
			<span class="gd-inv-val" title="<?php echo $date;?>"><?php echo $date_display;?></span>
		</div>
		<?php } if ( $date_updated_display ) { ?>
		<div class="gd-inv-detail">
			<label class="gd-inv-lbl"><?php _e( 'Last Updated:', 'geodir_payments' );?> </label>
			<span class="gd-inv-val" title="<?php echo $date_updated;?>"><?php echo $date_updated_display;?></span>
		</div>
		<?php } if ( $invoice_display ) { ?>
		<div class="gd-inv-detail gd-inv-invoice">
			<label class="gd-inv-lbl"><?php _e( 'Invoice:', 'geodir_payments' );?> </label>
			<span class="gd-inv-text"><?php echo $invoice_display;?></span>
		</div>
		<?php } ?>
		<?php do_action( 'geodir_payment_invoice_custom_details_right_after', $invoice_info ); ?>
	</div>
	<?php
	$info = ob_get_clean();	
	$info = apply_filters( 'geodir_payment_invoice_view_details', $info, $invoice_info );
	
	return $info;
}

function geodir_payment_add_invoice_scripts() {
	if ( geodir_payment_is_page( 'invoice' ) ) {
		wp_enqueue_style( 'gd_payment-cart-style', plugins_url( '', __FILE__ ) . '/css/gd-cart.css', array(), GEODIRPAYMENT_VERSION );
	}
	wp_enqueue_style( 'gd_payment-invoices-style', plugins_url( '', __FILE__ ) . '/css/gd-invoices.css', array(), GEODIRPAYMENT_VERSION );
		
	add_action( 'wp_footer', 'geodir_payment_localize_all_js_msg' );
		
	wp_register_script( 'gd_payment-invoices-js', plugins_url( '',__FILE__ ) . '/js/gd-invoices.js', array(), GEODIRPAYMENT_VERSION );
	wp_enqueue_script( 'gd_payment-invoices-js' );
}
?>
