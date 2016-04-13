<?php	
	/***
	Functions.php File
	*/
	
	function pr($__){
		echo "<pre>";
		print_r($__);
		echo "</pre>";
	}
	
	function array_kshift($arr)
	{
		list($k) = array_keys($arr);
		$r  = array($k=>$arr[$k]);
		unset($arr[$k]);
		return $r;
	}
	
	function make_unix_time($to__){
		list($part1,$part2) = explode('-', $to__);
		list($day, $month, $year) = explode('-', $part1);
		list($hours, $minutes,$seconds) = explode(':', $part2);
		$timeto =  mktime($hours, $minutes, $seconds, $month, $day, $year);
		return $timeto;
	}
	/**
Read the excel fiule for category and tags
**/
function read_cat_xlsx($inputFileName){
	$i = 0;
	$column = array();
	$main_array_key = array();
	$main_array = array();
	$fileValues = array();
	$countVal = 0;
	$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
	$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
	foreach($sheetData as $k =>$v){
		if(count($sheetData) > 1 && count($sheetData) != 1){
			if($i == 0){
				$countVal = count($sheetData);
				$ineerSR =  0;
				foreach($sheetData as $sd =>$sdVal){
					if($ineerSR == 0 ){
						$columnData = $sdVal;
					}
					$ineerSR++;
				}
				$column = array_filter($columnData);
				if(count($column)==1){
					$column = explode("\t",$column['A']);
				}
			
			}elseif($i > 0){
				
				if(count($sheetData) > 1){
					$fileData = $v;
				}else{
					$fileData = explode("\t",$column['A']);	
				}
				$aRV = array_filter($fileData);
				if(array_key_exists('D',$column)){
					$aRV['D'] = "";
				}
				if(array_key_exists('E',$column)){
					$aRV['E'] = "";
				}
				
				$arr_Count = count($aRV);
				for($kbs_col = 0;$kbs_col < $arr_Count;$kbs_col++):
					if(!array_key_exists('A',$aRV)){
						$aRV['A'] = "";
					}
					ksort($aRV);
					$main_array_key = array_combine($column,$aRV);
				endfor;
				$main_array[] = $main_array_key;
				$fileValues[] = $aRV; 
				$teardId[]	=	$aRV['A'];
				
			}
		}
		unset($value);
		$i++;
	}
	$dataRes = array("column"=>$column,"value"=>$fileValues,'combine'=>$main_array,'uniqueId' =>$teardId);
	return $dataRes;
}


function read_location_xlsx($inputFileName){
	$i = 0;
	$column = array();
	
	$fileData = array();
	$sData = array();
	$countVal = 0;
	$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
	$sheetData = $objPHPExcel->getActiveSheet()->toArray(false,true,true,true);
	
	if(count($sheetData) > 1 && count($sheetData) != 1){
		foreach($sheetData as $k =>$v){
			if($i == 0){
				$countVal = count($sheetData);
				$ineerSR =  0;
				foreach($sheetData as $sd =>$sdVal){
					if($ineerSR == 0 ){
						$columnData = $sdVal;
					}
					$ineerSR++;
				}
				$column = array_filter($columnData);
				if(count($column)==1){
					$column = explode("\t",$column['A']);
				}
				array_filter($column);
			}elseif($i > 0){
				
				if(count($sheetData) > 1){
					$fileData = $v;
				}else{
					$fileData = explode("\t",$column['A']);	
				}
				$aRV = array_filter($fileData);
				$countDataKbs = count($column);
				
				$dummy = array('A'=>"",'B'=>"",'C'=>"",'D'=>"",'E'=>"",'F'=>"",'G'=>"",'H'=>"",'I'=>"",'J'=>"",'K'=>"",'L'=>"");
				$nFinalData = array_replace($dummy, $aRV);
				$fileData = array_combine($column,$nFinalData);
				//pr($fileData);	
				$sData[] = $fileData;
				
			}
			$i++;
		}
		pr($sData);
	}
	
}	
