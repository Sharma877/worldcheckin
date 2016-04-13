<?php

	if(isset($_POST['submit']))
	{
		//require_once('gd_function.php');
		include('../../../wp-config.php');
		global $wpdb;
		
		//pr($_POST);die;
		
		/****/
		$mydb = new wpdb('worldche_wci','h7z*HDqfbTP1','worldche_wcimsgd_KBS','localhost')or die($mydb->show_errors());
		/*****/
		
		$custom_post_type	= $_POST['custom_post_type'];
		$field_value 		= $_POST['field_value'];
		$file_type			= $_POST['file_type'];	
				
		$host = explode('.',$_SERVER['HTTP_HOST']);
		switch($file_type){
			case "1":
				$csvFileName = $host[1]."_".$custom_post_type."_".$field_value."_".date("Y-m-d")."_at_".date("H-i-s").".csv";
				$content_type = " text/csv; charset=utf-8";
			break;	
			case "2":
				$content_type = " application/vnd.ms-excel; name='excel'";
				$csvFileName = $host[1]."_".$custom_post_type."_".$field_value."_".date("Y-m-d")."_at_".date("H-i-s").".xlsx";
			break;
		}
		

		header("Content-Type:$content_type");
		header("Content-Disposition: attachment; filename=$csvFileName");
		header("Pragma: no-cache");
		header("Expires: 0");	
    
        $newArray = array();
        $post_type_cat = "";
		$post_type_table = "";
        $upload_dir = wp_upload_dir();
		$imgUrl = $upload_dir['baseurl'];
        //* Custom Field Section 
        
        if($field_value == "custom_field")
        {
			$dataVal = $mydb->get_results("SELECT * FROM `".$wpdb->base_prefix."geodir_custom_fields` WHERE `post_type` = '$custom_post_type'", ARRAY_A);
			foreach($dataVal as $value)
            {
				$visible = array('is_active','is_default','is_admin','is_required','show_on_listing','show_on_detail','show_as_tab','for_admin_use','cat_sort','cat_filter');
				foreach($visible as $access){
					//$value[$access] = $value[$access]== "1"?"Yes":"No";
					$value[$access] = $value[$access];
				}
				$packagesData = ltrim(rtrim($value['packages'],','),',');
		        $packages = $mydb->get_var("SELECT group_concat(title) FROM `".$wpdb->base_prefix."geodir_price` WHERE `pid` in (".$packagesData.")").",";
                $value['packages'] = $packages;
                $newArray[] = $value;
            }
			$data = $newArray;
			if($file_type == "1"){
				generateCsv($data);
			}
			if($file_type == "2"){//Excel Data
				get_excel($data);
			}
		}
		
		//  advance search section
		if($field_value == "advance_search")
		{
			$data = $mydb->get_results("SELECT * FROM `".$wpdb->base_prefix."geodir_custom_advance_search_fields` WHERE `post_type` = '".$custom_post_type."'", ARRAY_A);
			foreach($data as $value){
				//$value['expand_search']	= $value['expand_search']=="1"?"Yes":"No";
				$value['expand_search']	= $value['expand_search'];
				$newArray[] = $value;
			}
			$data = $newArray;
			if($file_type == "1"){
				generateCsv($data);
			}
			if($file_type == "2"){//Excel Data
				get_excel($data);
			}
		}
		  // Sort Field Section
		if($field_value == "sort_field")
		{
			$data = $mydb->get_results("SELECT * FROM `".$wpdb->base_prefix."geodir_custom_sort_fields` WHERE `post_type` = '".$custom_post_type."'", ARRAY_A);
			foreach($data as $value){
				$visible = array('is_active','is_default','sort_asc','sort_desc');
				foreach($visible as $access){
					//$value[$access] = $value[$access]== "1"?"Yes":"No";
					$value[$access] = $value[$access];
				}
				$newArray[] = $value;
			}
			$data = $newArray;
			if($file_type == "1"){
				generateCsv($data);
			}
			if($file_type == "2"){//Excel Data
				get_excel($data);
			}
		}
		
		if(in_array($field_value,array('categories','tags','listing','prices','locations')))
		{
			switch($custom_post_type){
				case "gd_place":
					$post_type_tags		=	"gd_place_tags";
					$post_type_cat		=	"gd_placecategory";
					$post_type_table	=	"geodir_gd_place_detail";
				break;
				case "gd_service":
					$post_type_tags		=	"gd_service_tags";
					$post_type_cat		=	"gd_servicecategory";
					$post_type_table	=	"geodir_gd_service_detail"; 
				break;
				case "gd_video":
					$post_type_tags		=	"gd_video_tags";
					$post_type_cat		=	"gd_videocategory";
					$post_type_table	=	"geodir_gd_video_detail"; 
				break;
				case "gd_accommodation":
					$post_type_tags		=	"gd_accommodation_tags";
					$post_type_cat		=	"gd_servicecategory";
					$post_type_table	=	"geodir_gd_service_detail"; 
				break;
				case "gd_hospitality":
					$post_type_tags		=	"gd_hospitality_tags";
					$post_type_cat		=	"gd_hospitalitycategory";
					$post_type_table	=	"geodir_gd_hospitality_detail"; 
				break;
				case "gd_event":
					$post_type_tags		=	"gd_event_tags";
					$post_type_cat		=	"gd_eventcategory";
					$post_type_table	=	"geodir_gd_event_detail"; 
				break;
			}
			if($field_value == "categories"){
				$dataRes = $mydb->get_results("SELECT T.* FROM ".$wpdb->base_prefix."term_taxonomy as TT JOIN ".$wpdb->base_prefix."terms as T ON T.term_id = TT.term_id WHERE taxonomy = '".$post_type_cat."'",ARRAY_A);
				foreach($dataRes as $d){
					$newArray[] = $d;
				}
			}elseif($field_value == "tags"){
				$dataRes = $mydb->get_results("SELECT TT.term_id,T.name,T.slug FROM ".$wpdb->base_prefix."term_taxonomy as TT JOIN ".$wpdb->base_prefix."terms as T ON T.term_id = TT.term_id WHERE taxonomy = '".$post_type_tags."'",ARRAY_A);
				foreach($dataRes as $d){
					$newArray[] = $d;
				}
			}elseif($field_value == "listing"){	
				$data = $mydb->get_results("SELECT *,IF(featured_image != '',concat('".$imgUrl."',featured_image),'') as featured_image_one,from_unixtime(submit_time, '%Y-%m-%d %h:%i:%s') as submit_time_one,".$post_type_cat." FROM `".$wpdb->base_prefix.$post_type_table, ARRAY_A);
				foreach($data as $value){
					//$value['is_featured']	=	$value['is_featured'] == '1'?"No":"Yes";
					//$value['claimed']		=	$value['claimed'] == '1'?"No":"Yes";
					$value['featured_image']	=	$value['featured_image_one'];
					$value['submit_time']		=	$value['submit_time_one'];
					$default_category			=	$mydb->get_var("SELECT `name` FROM ".$wpdb->base_prefix."terms WHERE term_id = ".$value['default_category']);
					$package_id					=	$mydb->get_var("SELECT `title` FROM ".$wpdb->base_prefix."geodir_price WHERE pId = ".$value['package_id']);
					$gd_servicecategory			=	$mydb->get_var("select group_concat(name) as ".$post_type_cat." from `".$wpdb->base_prefix."terms` where term_id IN(".ltrim(rtrim($value[$post_type_cat],','),',').")");
					$value['default_category']	=	$default_category;
					$value['package_id']		=	$package_id;
					$value[$post_type_cat]		=	$gd_servicecategory;
					unset($value['featured_image_one']);
					unset($value['submit_time_one']);
					$newArray[] = $value;
				}
			}elseif($field_value == "prices"){
				$dataRes = $mydb->get_results("SELECT GP.* from ".$wpdb->base_prefix.$post_type_table." as PT JOIN ".$wpdb->base_prefix."geodir_price as GP ON GP.pId = PT.package_id group by PT.package_id",ARRAY_A);
				foreach($dataRes as $dr){
					$visible = array('is_default','is_featured','allow_vouchers','google_analytics','sendtofriend','has_upgrades','hide_related_tab','disable_coupon','allow_business_hours');
					foreach($visible as $access){
						//$dr[$access] = $dr[$access]== "1"?"Yes":"No";
						$dr[$access] = $dr[$access];
					}
					$dr['cat'] = $mydb->get_var("select group_concat(name) as ".$post_type_cat." from `".$wpdb->base_prefix."terms` where term_id IN(".ltrim(rtrim($dr['cat'],','),',').")");
					$newArray[] = $dr;
				}
			}elseif($field_value == "locations"){
				$locations = $mydb->get_results("SELECT GPL.* FROM ".$wpdb->base_prefix.$post_type_table." as PT Join ".$wpdb->base_prefix."geodir_post_locations as GPL on GPL.location_ID = PT.post_location_id group by GPL.location_ID", ARRAY_A);
				foreach($locations as $l){
					//$l['is_default'] = $l['is_default']=="1"?"Yes":"No";
					$newArray[] = $l;
				}
			}	
			$data = $newArray;
			if($file_type == "1"){
				generateCsv($data);
			}
			if($file_type == "2"){//Excel Data
				get_excel($data);
			}
		}
	}
		
function generateCsv($data, $delimiter = ',', $enclosure = '"') {
	$csv_array = array();
	$array = array();
	$key_array = array();
	$fp = fopen('file.csv', 'w');
	if(count($data) > 0){
		foreach ($data as $fields){}
		foreach($fields as $key=>$value){
			$key_array[] = $key;
		}
			
		fputcsv($fp, $key_array);
		foreach ($data as $fields) {
			fputcsv($fp, $fields);
		}
		echo file_get_contents('file.csv');
	}else{
		echo "No Records Found";
	}
    fclose($fp);
}

function get_excel($dataVal){
	$excelColumn = "";
	$excelVal = array();
	$excelVal1 = array();
	$line = '';
	foreach($dataVal as $firstArray){
		foreach($firstArray as $keyCol=>$value){
			if(!isset($value) || $value == ""){
                $value = "\t";
			}else{
                $value = str_replace('"', '""', $value);
                $value = '"' . $value . '"' . "\t";
            }
			$line .= $value;
        }
		$data .= trim($line)."\n";
		$data = str_replace("\r", "", $data);
		$data = str_replace(',', '"\t"', $data);
		unset($line);		
	}	
	$data = ($data == "")?"\nno matching records found\n":$data;
	foreach(@$firstArray as $__k =>$__v){
		$excelColumn .= $__k."\t";
	}
	$excelColumn = str_replace(',', '"\t"', $excelColumn);
	echo $excelColumn."\n".$data;
	
	unset($data);
	unset($excelColumn);
	die();	
}

function pr($__){
	echo "<pre>";
	print_r($__);
	echo "</pre>";
}
    
	
?>
