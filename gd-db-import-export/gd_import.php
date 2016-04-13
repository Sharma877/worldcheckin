<?php

error_reporting(E_ALL);
set_time_limit(0);
//set_time_limit(99999999);		

if (isset($_POST['submit']) && ($_POST['req'] == "gd_import")) {
    require_once('gd_function.php');
    /*     * * */
    $mydb = new wpdb('worldche_wci', 'h7z*HDqfbTP1', 'worldche_wcimsgd_KBS', 'localhost')or die("not connected");
    /*     * * */
    $ext = strtolower(end(explode('.', $_FILES['csv_file']['name'])));
    $file_temp_name = $_FILES['csv_file']['tmp_name'];
    $custom_post_type = $_POST['custom_post_type'];
    $field_value = $_POST['field_value'];
    if ($ext == "csv") {
        $csv = array();
        $col_count = 0;
        //Custom Field Insert New - Update
        if ($field_value == "custom_field") {
            $row = 1;
            $keys = array();
            $val = array();
            $dataValues = array();
            $dv = array();
            $post_id_val = 0;
            if (($handle = fopen("$file_temp_name", "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $num = count($data);
                    for ($c = 0; $c < $num; $c++) {
                        if ($data[$c] == "Yes") {
                            $data[$c] = "1";
                        } elseif ($data[$c] == "No") {
                            $data[$c] = "0";
                        }
                        if ($row == "1") {
                            $keys[] = $data[$c];
                        } else {
                            $post_id_val = $data[0];
                            $dataValues[$keys[$c]] = $data[$c];
                            $dataValues['post_type'] = $custom_post_type;
                            $val[] = $data[$c];
                        }
                    }
                    if ($row > 1) {
                        $existingRows = $mydb->get_row("SELECT id from " . $wpdb->base_prefix . "geodir_custom_fields where id=" . $post_id_val, ARRAY_A);
                        if (count($existingRows) > 0) {
                            $mydb->update($wpdb->base_prefix . "geodir_custom_fields", $dataValues, array('id' => $post_id_val));
                            if ($mydb->rows_affected > 0) {
                                echo "Data Updated successfully row affected :" . $mydb->rows_affected . "<br/>";
                            }
                        } else {
                            $mydb->insert($wpdb->base_prefix . "geodir_custom_fields", $dataValues);
                            if ($mydb->insert_id > 0) {
                                echo "Data Inserted Successfully ID :" . $mydb->insert_id . "<br>";
                            }
                        }
                    }
                    $row++;
                }
                unset($data);
                fclose($handle);
            }
        }
        if ($field_value == "advance_search") {
            $row = 1;
            $keys = array();
            $val = array();
            $dataValues = array();
            $dv = array();
            $post_id_val = 0;
            if (($handle = fopen("$file_temp_name", "r")) !== FALSE):
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $num = count($data);
                    for ($c = 0; $c < $num; $c++) {
                        if ($data[$c] == "Yes") {
                            $data[$c] = "1";
                        } elseif ($data[$c] == "No") {
                            $data[$c] = "0";
                        }
                        if ($row == "1") {
                            $keys[] = $data[$c];
                        } else {
                            $post_id_val = $data[0];
                            $dataValues[$keys[$c]] = $data[$c];
                            $dataValues['post_type'] = $custom_post_type;
                            $val[] = $data[$c];
                        }
                    }
                    if ($row > 1) {
                        $existingRows = $mydb->get_row("SELECT id from " . $wpdb->base_prefix . "geodir_custom_advance_search_fields where id=" . $post_id_val, ARRAY_A);
                        if (count($existingRows) > 0) {
                            $mydb->update($wpdb->base_prefix . "geodir_custom_advance_search_fields", $dataValues, array('id' => $post_id_val));
                            if ($mydb->rows_affected > 0) {
                                echo "Data Updated successfully row affected :" . $mydb->rows_affected . "<br/>";
                            }
                        } else {
                            $mydb->insert($wpdb->base_prefix . "geodir_custom_advance_search_fields", $dataValues);
                            if ($mydb->insert_id > 0) {
                                echo "Data Inserted Successfully ID :" . $mydb->insert_id . "<br/>";
                            }
                        }
                    }
                    $row++;
                }
                unset($data);
                fclose($handle);
            endif;
        }
        if ($field_value == "sort_field") {
            $row = 1;
            $keys = array();
            $val = array();
            $dataValues = array();
            $dv = array();
            $post_id_val = 0;
            if (($handle = fopen("$file_temp_name", "r")) !== FALSE):
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $num = count($data);
                    for ($c = 0; $c < $num; $c++) {
                        if ($data[$c] == "Yes") {
                            $data[$c] = "1";
                        } elseif ($data[$c] == "No") {
                            $data[$c] = "0";
                        }
                        if ($row == "1") {
                            $keys[] = $data[$c];
                        } else {
                            $post_id_val = $data[0];
                            $dataValues[$keys[$c]] = $data[$c];
                            $dataValues['post_type'] = $custom_post_type;
                            $val[] = $data[$c];
                        }
                    }
                    if ($row > 1) {
                        $existingRows = $mydb->get_row("SELECT id from " . $wpdb->base_prefix . "geodir_custom_sort_fields where id=" . $post_id_val, ARRAY_A);
                        if (count($existingRows) > 0) {
                            $mydb->update($wpdb->base_prefix . "geodir_custom_sort_fields", $dataValues, array('id' => $post_id_val));
                            if ($mydb->rows_affected > 0) {
                                echo "Data Updated successfully row affected :" . $mydb->rows_affected . "<br/>";
                            }
                        } else {
                            $mydb->insert($wpdb->base_prefix . "geodir_custom_sort_fields", $dataValues);
                            if ($mydb->insert_id > 0) {
                                echo "Data Inserted Successfully ID :" . $mydb->insert_id . "<br/>";
                            }
                        }
                    }
                    $row++;
                }
                unset($data);
                fclose($handle);
            endif;
        }
        /**
         * Other Sections
         * ** */
        if (in_array($field_value, array('categories', 'tags', 'listing', 'prices', 'locations'))) {
            switch ($custom_post_type) {
                case "gd_place":
                    $post_type_tags = "gd_place_tags";
                    $post_type_cat = "gd_placecategory";
                    $post_type_table = "geodir_gd_place_detail";
                    break;
                case "gd_service":
                    $post_type_tags = "gd_service_tags";
                    $post_type_cat = "gd_servicecategory";
                    $post_type_table = "geodir_gd_service_detail";
                    break;
                case "gd_video":
                    $post_type_tags = "gd_video_tags";
                    $post_type_cat = "gd_videocategory";
                    $post_type_table = "geodir_gd_video_detail";
                    break;
                case "gd_accommodation":
                    $post_type_tags = "gd_accommodation_tags";
                    $post_type_cat = "gd_servicecategory";
                    $post_type_table = "geodir_gd_service_detail";
                    break;
                case "gd_hospitality":
                    $post_type_tags = "gd_hospitality_tags";
                    $post_type_cat = "gd_hospitalitycategory";
                    $post_type_table = "geodir_gd_hospitality_detail";
                    break;
                case "gd_event":
                    $post_type_tags = "gd_event_tags";
                    $post_type_cat = "gd_eventcategory";
                    $post_type_table = "geodir_gd_event_detail";
                    break;
            }
            /*             * *************************************** */
            $row = 1;
            $keys = array();
            $val = array();
            $dataValues = array();

            $dv = array();
            $post_id_val = 0;
            if (($handle = fopen("$file_temp_name", "r")) !== FALSE):
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $num = count($data);
                    for ($c = 0; $c < $num; $c++) {
                        if ($data[$c] == "Yes") {
                            $data[$c] = "1";
                        } elseif ($data[$c] == "No") {
                            $data[$c] = "0";
                        }
                        if ($row == "1") {
                            $keys[] = $data[$c];
                        } else {
                            $post_id_val = $data[0];
                            $dataValues[$keys[$c]] = $data[$c];
                            $val[] = $data[$c];
                        }
                    }
                    if ($row > 1) {
                        if ($field_value == "categories" || $field_value == "tags") {
                            $existingRows = $mydb->get_row("SELECT term_id from " . $wpdb->base_prefix . "terms where term_id=" . $post_id_val, ARRAY_A);
                            if (count($existingRows) > 0) {
                                $mydb->update($wpdb->base_prefix . "terms", $dataValues, array('term_id' => $post_id_val));
                                if ($mydb->rows_affected > 0) {
                                    echo "Data Updated successfully row affected :" . $mydb->rows_affected . "<br/>";
                                }
                            } else {
                                $mydb->insert($wpdb->base_prefix . "terms", $dataValues);
                                $taxonomy = $mydb->insert_id;
                                $taxo_nomy = "";
                                if ($field_value == "categories") {
                                    $taxo_nomy = $post_type_cat;
                                } else if ($field_value == "tags") {
                                    $taxo_nomy = $post_type_tags;
                                }
                                $arr = array('term_taxonomy_id' => $taxonomy, 'term_id' => $taxonomy, 'taxonomy' => $taxo_nomy);
                                $mydb->insert($wpdb->base_prefix . "term_taxonomy", $arr);
                                if ($mydb->insert_id != "0") {
                                    echo "Data Inserted Successfully ID :" . $mydb->insert_id . "<br/>";
                                }
                            }
                        } elseif ($field_value == "listing") {

                            $newArray = array();
                            $featured_image = explode('images/', $dataValues['featured_image']);
                            $default_category = $mydb->get_var("SELECT `term_id` FROM " . $wpdb->base_prefix . "terms WHERE  name = '" . $dataValues['default_category'] . "'");
                            $package_id = $mydb->get_var("SELECT `pId` FROM " . $wpdb->base_prefix . "geodir_price WHERE title = '" . $dataValues['package_id'] . "'");
                            if (count($dataValues[$post_type_cat]) > 1) {
                                $dataVal = implode("','", $dataValues[$post_type_cat]);
                            } else {
                                $dataVal = $dataValues[$post_type_cat];
                            }
                            $gd_servicecategory = $mydb->get_var("select group_concat(term_id) as term_id from `" . $wpdb->base_prefix . "terms` where name IN('" . $dataVal . "')");
                            $dataValues['featured_image'] = $featured_image['1'];
                            $dataValues['package_id'] = $package_id;
                            $dataValues['default_category'] = $default_category;
                            $dataValues['submit_time'] = make_unix_time($dataValues['submit_time']);
                            $dataValues[$post_type_cat] = $gd_servicecategory;
                            $existingRows = $mydb->get_row("SELECT post_id from " . $wpdb->base_prefix . $post_type_table . " where post_id=" . $post_id_val, ARRAY_A);
                            $dvkey = array();
                            $dval = array();
                            $update = "";
                            if (count($existingRows) > 0) {
                                $mydb->update($wpdb->base_prefix . $post_type_table, $dataValues, array('post_id' => $post_id_val));
                                if ($mydb->rows_affected > 0) {
                                    echo "Data Updated successfully row affected :" . $mydb->rows_affected . "<br/>";
                                }
                            } else {
                                $mydb->insert($wpdb->base_prefix . $post_type_table, $dataValues);
                                if ($mydb->insert_id > 0) {
                                    echo "Data Inserted Successfully ID :" . $mydb->insert_id . "<br/>";
                                }
                            }
                        } else if ($field_value == "prices") {
                            $catVal = "'" . str_replace(",", "','", $dataValues['cat']) . "'";
                            $gd_servicecategory = $mydb->get_var("select group_concat(term_id) as term_id from `" . $wpdb->base_prefix . "terms` where name IN(" . $catVal . ")");
                            $dataValues['cat'] = $gd_servicecategory;
                            $existingRows = $mydb->get_row("SELECT pid from " . $wpdb->base_prefix . "geodir_price where pid=" . $post_id_val, ARRAY_A);
                            if (count($existingRows) > 0) {
                                $mydb->update($wpdb->base_prefix . "geodir_price", $dataValues, array('pid' => $post_id_val));
                                if ($mydb->rows_affected > 0) {
                                    echo "Data Updated successfully row affected :" . $mydb->rows_affected . "<br/>";
                                }
                            } else {
                                $mydb->insert($wpdb->base_prefix . "geodir_price", $dataValues);
                                if ($mydb->insert_id > 0) {
                                    echo "Data Inserted Successfully ID :" . $mydb->insert_id . "<br/>";
                                }
                            }
                        } elseif ($field_value == "locations") {
                            $existingRows = $mydb->get_row("SELECT location_id from " . $wpdb->base_prefix . "geodir_post_locations where location_id=" . $post_id_val, ARRAY_A);
                            if (count($existingRows) > 0) {
                                $mydb->update($wpdb->base_prefix . "geodir_post_locations", $dataValues, array('location_id' => $post_id_val));
                                if ($mydb->rows_affected > 0) {
                                    echo "Data Updated successfully row affected :" . $mydb->rows_affected . "<br/>";
                                }
                            } else {
                                $mydb->insert($wpdb->base_prefix . "geodir_post_locations", $dataValues);
                                if ($mydb->insert_id > 0) {
                                    echo "Data Inserted Successfully ID :" . $mydb->insert_id . "<br/>";
                                }
                            }
                        }
                    }
                    $row++;
                }
                unset($data);
                fclose($handle);
            endif;
        }
    }
    
    
    
    if($ext == "xls" || $ext == "xlsx") {  //* Excel File Upload field
		include("../../../wp-config.php");
        include('Classes/PHPExcel/IOFactory.php');
        $kbsXlsArray = array();
        
       /*********/
       switch ($custom_post_type) {
                case "gd_place":
                    $post_type_tags = "gd_place_tags";
                    $post_type_cat = "gd_placecategory";
                    $post_type_table = "geodir_gd_place_detail";
                    break;
                case "gd_service":
                    $post_type_tags = "gd_service_tags";
                    $post_type_cat = "gd_servicecategory";
                    $post_type_table = "geodir_gd_service_detail";
                    break;
                case "gd_video":
                    $post_type_tags = "gd_video_tags";
                    $post_type_cat = "gd_videocategory";
                    $post_type_table = "geodir_gd_video_detail";
                    break;
                case "gd_accommodation":
                    $post_type_tags = "gd_accommodation_tags";
                    $post_type_cat = "gd_servicecategory";
                    $post_type_table = "geodir_gd_service_detail";
                    break;
                case "gd_hospitality":
                    $post_type_tags = "gd_hospitality_tags";
                    $post_type_cat = "gd_hospitalitycategory";
                    $post_type_table = "geodir_gd_hospitality_detail";
                    break;
                case "gd_event":
                    $post_type_tags = "gd_event_tags";
                    $post_type_cat = "gd_eventcategory";
                    $post_type_table = "geodir_gd_event_detail";
                    break;
            }
       /*********/
        //array("column"=>$column,"value"=>$newVal,'combine'=>$main_array,'uniqueId' =>$uid)
        if ($field_value == "categories"	||	$field_value == "tags") {
			$dataRRR = read_cat_xlsx($file_temp_name);
			$xlsDataCount = count($dataRRR['combine']);
			if ($xlsDataCount > 0) {
				for ($kbs_Count = 0; $kbs_Count < $xlsDataCount; $kbs_Count++) {
				   for ($kbs_i = 0; $kbs_i < count($dataRRR['combine']); $kbs_i++) {
                        $post_id_val = $dataRRR['uniqueId'][$kbs_i];
                        $existRows = $mydb->get_row("SELECT term_id from " . $wpdb->base_prefix . "terms where term_id=".$post_id_val, ARRAY_A);
                        if(isset($post_id_val) && !empty($post_id_val) && (count($existRows) > 0)){
							$mydb->update($wpdb->base_prefix . "terms", $dataRRR['combine'][$kbs_i], array('term_id' => $post_id_val));
                            if ($mydb->rows_affected > 0) {
                                echo "Data Updated successfully row affected :" . $mydb->rows_affected . "<br/>";
                            }
						}else{
							$mydb->insert($wpdb->base_prefix . "terms", $dataRRR['combine'][$kbs_i]);
                            $taxonomy = $mydb->insert_id;
                            $taxo_nomy = "";
                            if ($field_value == "categories") {
                                $taxo_nomy = $post_type_cat;
                            } else if ($field_value == "tags") {
                                $taxo_nomy = $post_type_tags;
                            }
                            $arr = array('term_taxonomy_id' => $taxonomy, 'term_id' => $taxonomy, 'taxonomy' => $taxo_nomy);
                            $mydb->insert($wpdb->base_prefix . "term_taxonomy", $arr);
                            if ($mydb->insert_id != "0") {
                                echo "Data Inserted Successfully ID :" . $mydb->insert_id . "<br/>";
                            }	
						}
                    }die;
				}
			}
		}elseif($field_value == "locations"){
			
			$locationData = read_location_xlsx($file_temp_name);
			pr($locationData);
			
		}
        unset($kbsXlsArray);
    } else {

        echo "
				<script type='text/javascript'>
					alert('Please Choose valid CSV,xlsx or xls file.');
					/**window.parent.location.href='" . $_SERVER['HTTP_REFERER'] . "';*/
				</script>
			";
    }//check CSV file format
}
?>
