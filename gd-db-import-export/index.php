<?php
/*
Plugin Name: GD DB Import/Export
Plugin URI: http://google.com
Description: This is a sample plugin with backup and restore options feature.
Author: KBS
Version: 1.0
Author URI: http://google.com
*/


function register_ie_option() {
    add_menu_page('IE Option Page', 'GD DB Import/Export', 'activate_plugins', 'ie-option', 'ie_option_page',  plugins_url( 'gd-db-import-export/favicon.ico'), 100);
    add_submenu_page('ie-option', 'Import', 'Import', 'activate_plugins', 'ie-import-option', 'ie_import_option_page');
    add_submenu_page('ie-option', 'Export', 'Export', 'activate_plugins', 'ie-export-option', 'ie_export_option_page');
}
 
function ie_option_page() {

}
 
function ie_import_option_page() {
	include('../../../wp-config.php');
	global $wpdb;
	$a =  get_option( 'geodir_post_types' ); ?>
	<style>
	red{color:#f00;}
	</style>
	<div class='wrap'>
		<form action="" method="POST" id="import_data_form" enctype="multipart/form-data">
			<table>
				<tr>
					<input type='hidden' name='req' value='gd_import'/>
					<th><label for="custom_post_type">Custom Post Type</label></th>
					<td>
						<select name="custom_post_type" id="custom_post_type" onchange="val()" id="custom_post_type">
						<?php foreach($a as $key=>$value){ ?>
								<?php $custom_post_type = $value['labels']['name'];	?>
								<option value="<?php echo $key;?>"><?php echo $custom_post_type;    ?></option> 
							<?php } ?>
						</select>	
					</td>
				</tr>
				<tr>
					<th><label for="field_value">Which once to Import</label></th>
					<td>
						<select name="field_value" id="field_value">
							<?php
								$options = array('custom_field' =>"Custom Fields",'advance_search' => "Advance Search",'sort_field' => "Sorting Options",'listing' =>"Listings",'categories' =>"CPT Categories",'tags' => "CPT Tags",'prices' => "Prices",'locations' => "Locations");
								foreach($options as $k=>$op){
									echo "<option value='".$k."'>".$op."</option>";
								}	
							?>
						</select>
					</td>
				</tr>
				<tr>
					<th><label for="csv_file">Upload File <small>(<red>only csv, xls, xlsx are allowed</red>)</small></label></th>
					<td><input type="file" name="csv_file" id="csv_file" placeholder="Choose File" /></td>
				</tr>
			</table>
			<p class="submit">
				<input id="submit" name="submit" type="submit" class="button button-primary" value="Import Data file" />
			</p>
		</form>
	</div>
	<?php
	require_once('gd_import.php');
}
 
function ie_export_option_page() {
  global $wpdb;
	$a =  get_option( 'geodir_post_types' ); ?>
	<div class='wrap'>
		<form action="<?php echo plugins_url( 'gd_export.php', __FILE__ ) ?>" method="POST" >
			<table width="50%">
				<tr>
					<th><label for="custom_post_type">Custom Post Type</label></th>
					<td>
						<select name="custom_post_type" id="custom_post_type" onchange="val()" id="custom_post_type">
						<?php foreach($a as $key=>$value){ ?>
								<?php $custom_post_type = $value['labels']['name'];	?>
								<option value="<?php echo $key;?>"><?php echo $custom_post_type;    ?></option> 
							<?php } ?>
						</select>	
					</td>
				</tr>
				<tr>
					<th><label for="field_value">Which once to Export</label></th>
					<td>
						<select name="field_value" id="field_value">
							<?php
								$options = array('custom_field' =>"Custom Fields",'advance_search' => "Advance Search",'sort_field' => "Sorting Options",'listing' =>"Listings",'categories' =>"CPT Categories",'tags' => "CPT Tags",'prices' => "Prices",'locations' => "Locations");
								foreach($options as $k=>$op){
									echo "<option value='".$k."'>".$op."</option>";
								}	
							?>
							
						</select>
					</td>
				</tr>
				<tr>
					<th><label for="file_type">File Type</label></th>
					<td>
						<input type="radio" value="1" name="file_type" id="file_type" />
						<label for="file_type">CSV</label>
						<input type="radio" value="2" name="file_type" id="file_type_xls" />
						<label for="file_type_xls">Excel <small>(xls,xlsx)</small></label>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input id="submit" name="submit" type="submit" class="button button-primary" value="Export/Download File" />
			</p>
		</form>
	</div>
	<?php
	
}


add_action('admin_menu', 'register_ie_option');

