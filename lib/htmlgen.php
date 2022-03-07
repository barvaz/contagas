<?php
/*
   Copyright 2013 Amit Moravchick amit.moravchick@gmail.com

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
*/


//
function getValsFromDB(&$cnn, $table, $order, $itemId, $itemName, $selected, $multiple, $emptyrow, $isText = FALSE, $attrib = "" ){
	$sql = "";
	$combo = "";
	$items = array();
	$lista = array();
	$lista = explode(",", $itemName);
	if(strlen($selected) > 0){
		if($multiple){
			$items = explode(",", $selected);
			if($isText){
				for($i = 0; $i < count($items); $i++){
					$items[$i] = "'" . $items[$i] . "'";
				}
			}
			//$items = explode(",", $selected);
		}else{
			$selected = "'" . $selected . "'";
			$items[] = $selected;
		}
	}
	$selected = implode(",", $items);
	if(count($items) > 0){
		$sql = "SELECT * FROM " . $table . " WHERE $itemId IN ($selected)";
		if(trim($order) != "" ){
			$sql .= " order by " . $order;
		}
		logWrite($sql, "sql");
		$rs = mysqli_query($cnn, $sql);
		while($row = mysqli_fetch_array($rs)){
			for($i = 0; $i < count($lista); $i++){
				//$combo .= substr(($row[$lista[$i]]),0,50) . " ";
				$combo .= $row[$lista[$i]] . " ";
			}
			if($isText){
				$combo .= "<br/>";
			}
		}
		mysqli_free_result($rs);
	}
	return $combo;
}
//
function getComboFromDB($name, &$cnn, $table, $order, $itemId, $itemName, $selected, $multiple, $emptyrow, $attrib = "" ){
	$sql = "";
	$combo = "";
	$items = array();
	$lista = array();
	$lista = explode(",", $itemName);
	if($multiple){
		$items = explode(",", $selected);
		$name = $name . "[]";
	}else{
		$items[] = $selected;
	}
	$combo = "<select name=\"" . $name . "\" " . $attrib;
	if($multiple){
		$combo .= " multiple";
	}
	$combo .= ">\n";
	$sql = "SELECT * FROM " . $table;
	if(trim($order) != "" ){
		$sql .= " order by " . $order;
	}
	logWrite($sql, "sql");
	if($emptyrow){
		$combo .= "<option value=\"0\" ";
		for($i = 0; $i < count($items); $i++){
			if(" " . $items[$i] == " 0"){
				$combo .= "selected ";
				break;
			}
		}
		$combo .= ">&nbsp;</option>\n";
	}
	$rs = mysqli_query($cnn, $sql);
	while($row = mysqli_fetch_array($rs)){
		$combo .= "<option value=\"" . $row[$itemId] . "\" ";
		for($i = 0; $i < count($items); $i++){
			if(" " . $items[$i] == " " . $row[$itemId]){
				$combo .= "selected ";
				break;
			}
		}
		$combo .= ">";
		for($i = 0; $i < count($lista); $i++){
			//$combo .= substr(($row[$lista[$i]]),0,50) . " ";
			$combo .= $row[$lista[$i]] . " ";
		}
		$combo .= "</option>\n";
	}
	mysqli_free_result($rs);
	$combo .= "</select>\n";
	return $combo;
}
//
function getComboFromSql($name, &$cnn, $sql, $itemId, $itemName, $selected, $multiple, $emptyrow, $attrib = "" ){
	$combo = "";
	$items = array();
	$lista = array();
	$lista = explode(",", $itemName);
	if($multiple){
		$items = explode(",", $selected);
		$name = $name . "[]";
	}else{
		$items[] = $selected;
	}
	$combo = "<select name=\"" . $name . "\" " . $attrib;
	if($multiple){
		$combo .= " multiple";
	}
	$combo .= ">\n";
	logWrite($sql, "sql");
	if($emptyrow){
		$combo .= "<option value=\"0\" ";
		for($i = 0; $i < count($items); $i++){
			if(" " . $items[$i] == " 0"){
				$combo .= "selected ";
				break;
			}
		}
		$combo .= ">&nbsp;</option>\n";
	}
	$rs = mysqli_query($cnn, $sql);
	
	while($row = mysqli_fetch_array($rs)){
		$combo .= "<option value=\"" . $row[$itemId] . "\" ";
		for($i = 0; $i < count($items); $i++){
			if(" " . $items[$i] == " " . $row[$itemId]){
				$combo .= "selected ";
				break;
			}
		}
		$combo .= ">";
		for($i = 0; $i < count($lista); $i++){
			//$combo .= substr(($row[$lista[$i]]),0,50) . " ";
			$combo .= $row[$lista[$i]] . " ";
		}
		$combo .= "</option>\n";
	}
	mysqli_free_result($rs);
	$combo .= "</select>\n";
	return $combo;
}
//
function getCheckboxGroupFromSql($name, &$cnn, $sql, $itemId, $itemName, $selected, $multiple, $emptyrow, $attrib = "" ){
	$combo = "";
	$items = array();
	$lista = array();
	$lista = explode(",", $itemName);
	if($multiple){
		$items = explode(",",$selected);
		//$name = $name . "[]";
	}else{
		$items[] = $selected;
	}
	$combo = "";
	if($multiple){
		$widget = "checkbox";
	}else{
		$widget = "radio";
	}
	if($emptyrow){
		$combo .= "<input name='$name' type='$widget' value='0' ><br/>\n";
	}

	$j = 0;
	logWrite($sql, "sql");
	$rs = mysqli_query($cnn, $sql);
	while($row = mysqli_fetch_array($rs)){
		$itemName = $name;
		if($multiple){
			$itemName = $name . "_" . $j;
		}
		$combo .= "<input name='" . $itemName . "' type='$widget' value='" . $row[$itemId] . "' ";
		for($i = 0; $i < count($items); $i++){
			if(" " . $items[$i] == " " . $row[$itemId]){
				$combo .= "checked ";
				break;
			}
		}
		$combo .= ">";
		for($i = 0; $i < count($lista); $i++){
			$combo .= $row[$lista[$i]] . " ";
		}
		$combo .= "<br/>\n";
		$j ++;
	}
	mysqli_free_result($rs);
	return $combo;
}
//
function getValsFromArray($data, $itemId, $itemName, $selected, $multiple, $emptyrow, $attrib = "" ){
	$combo = "";
	$items = array();
	$lista = array();
	$lista = explode(",", $itemName);
	if($multiple){
		$items = explode(",",$selected);
	}else{
		$items[] = $selected;
	}
	foreach($data as $row){
		for($i = 0; $i < count($items); $i++){
			if($items[$i] == $row[$itemId]){
				if($items[$i] == $row[$itemId]){
					for($i = 0; $i < count($lista); $i++){
						//$combo .= substr(($row[$lista[$i]]),0,50) . " ";
						$combo .= $row[$lista[$i]] . " ";
					}
					$combo .= "<br>\n";
				}
			}
		}
	}
	return $combo;
}
//
function getComboFromArray($name, $data, $itemId, $itemName, $selected, $multiple, $emptyrow, $attrib = "" ){
	$combo = "";
	$items = array();
	$lista = array();
	$lista = explode(",", $itemName);
	if($multiple){
		$items = explode(",",$selected);
		$name = $name . "[]";
	}else{
		$items[] = $selected;
	}
	$combo = "<select name=\"" . $name . "\" " . $attrib;
	if($multiple){
		$combo .= " multiple";
	}
	$combo .= ">\n";
	if($emptyrow){
		$combo .= "<option value=\"0\">&nbsp;</option>\n";
	}
	foreach($data as $row){
		$combo .= "<option value=\"" . $row[$itemId] . "\" ";
		for($i = 0; $i < count($items); $i++){
			if(" " . $items[$i] == " " . $row[$itemId]){
				$combo .= "selected ";
				break;
			}
		}
		$combo .= ">";
		for($i = 0; $i < count($lista); $i++){
//			$combo .= substr(($row[$lista[$i]]),0,50) . " ";
			$combo .= $row[$lista[$i]] . " ";
		}
		$combo .= "</option>\n";
	}
	$combo .= "</select>\n";
	return $combo;
}
//
function getCheckboxGroupFromArray($name, $data, $itemId, $itemName, $selected, $multiple, $emptyrow, $attrib = "" ){
	$combo = "";
	$items = array();
	$lista = array();
	$lista = explode(",", $itemName);
	if($multiple){
		$items = explode(",",$selected);
		//$name = $name . "[]";
	}else{
		$items[] = $selected;
	}
	$combo = "";
	if($multiple){
		$widget = "checkbox";
	}else{
		$widget = "radio";
	}
	if($emptyrow){
		$combo .= "<input name='$name' type='$widget' value='0' ><br/>\n";
	}
	$j = 0;
	foreach($data as $row){
		$combo .= "<input name='" . $name . "_" . $j . "' type='$widget' value='" . $row[$itemId] . "' ";
		for($i = 0; $i < count($items); $i++){
			if(" " . $items[$i] == " " . $row[$itemId]){
				$combo .= "checked ";
				break;
			}
		}
		$combo .= ">";
		for($i = 0; $i < count($lista); $i++){
			$combo .= $row[$lista[$i]] . " ";
		}
		$combo .= "<br/>\n";
		$j ++;
	}
	return $combo;
}
//
function GetFile($name, $maxlen=25, $visiblelen=25, $attrib = ""){
	if(($maxlen == "") || ($maxlen <= 0)){
		$maxlen = 10;
	}
	if(($visiblelen == "") || ($visiblelen <= 0)){
		$visiblelen = $maxlen;
	}
	return  "<input type=\"file\" name=\"" . $name . "\" " . $attrib . " size=\"" . $visiblelen . "\" maxlength=\"" . $maxlen . "\">\n";
}
//
function GetInput($name, $type, $value, $maxlen=25, $visiblelen=25, $attrib = ""){
	if($type == "password"){
		$value = "";
	}
	if(($maxlen == "") || ($maxlen <= 0)){
		$maxlen = 10;
	}
	if(($visiblelen == "") || ($visiblelen <= 0)){
		$visiblelen = $maxlen;
	}
	return  "<input type=\"" . $type . "\" name=\"" . $name . "\" " . $attrib . " value=\"" . ($value) . "\" size=\"" . $visiblelen . "\" maxlength=\"" . $maxlen . "\">\n";
}
//
function GetInputDate($name, $value) {
	$retVal = "<link rel=\"stylesheet\" media=\"screen\" type=\"text/css\" href=\"../js/datepicker/css/datepicker.css\" />\n
					<!-- link rel=\"stylesheet\" media=\"screen\" type=\"text/css\" href=\"../js/datepicker/css/layout.css\" / -->\n
					<input class=\"inputDate\" id=\"$name\" name=\"$name\" value=\"$value\" />\n
					<script>\n
						if($('#$name').val() == ''){
							$('#$name').val('" . date('d/m/Y') . "');
						}
						$('#$name').DatePicker({\n
							format:'d/m/Y',\n
							date: $('#$name').val(),\n
							current: $('#$name').val(),\n
							starts: 1,\n
							position: 'r',\n
							onBeforeShow: function(){\n
								$('#$name').DatePickerSetDate($('#$name').val(), true);\n
							},\n
							onChange: function(formated, dates){\n
								$('#$name').val(formated);\n
								$('#$name').DatePickerHide();\n
							}\n
						});\n
						\n
						$('#$name').focus(function(){\n
							$(this).DatePickerShow();\n
						});\n
					</script>\n";
	
	return $retVal;
}
//
function GetInputColor($name, $type, $value, $maxlen=25, $visiblelen=25, $attrib = ""){
	if(($maxlen == "") || ($maxlen <= 0)){
		$maxlen = 10;
	}
	if(($visiblelen == "") || ($visiblelen <= 0)){
		$visiblelen = $maxlen;
	}
	$retVal = "<input type=\"hidden\" value=\"$value\" name=\"$name\" id=\"$name\" /><div style=\"\">
			<span id=\"colorPicker_$name\" style=\"float:left; border:1px solid black; width:20px; height:20px;margin:5px;background-color:#$value\"></span>
			<span id=\"text_$name\" style=\"\">$value</span>
		</div>";
	
	return  $retVal;
}
//
function GetHTMLArea($name, $value, $rows, $cols,  $attrib = ""){
	//return "<textarea name=\"$name\" id=\"$name\"  wrap=\"hard\" class=\"testo\">" . $value."</textarea>";
	return "<textarea name=\"$name\" cols=\"40\" rows=\"10\" id=\"$name\" class=\"$name\">" . $value."</textarea>";
}
//
function GetTextArea($name, $value,  $cols, $rows, $attrib = ""){
	if(($rows == "") || ($rows <= 0)){
		$rows = 3;
	}
	if(($cols == "") || ($cols <= 0)){
		$cols = 10;
	}
	//return  "<textarea  name=\"" . $name . "\" " . $attrib . " cols=\"" . $cols . "\" rows=\"" . $rows . "\">" . htmlentities($value) . "</textarea>\n";
	return  "<textarea  name=\"" . $name . "\" " . $attrib . " cols=\"" . $cols . "\" rows=\"" . $rows . "\">" . $value . "</textarea>\n";
}
//
function GetCheckBox($name, $value, $checked, $attrib = "")
{
	$status = "";
	if($value != "")
	{
		if(($checked == TRUE) || ($checked == 1)){
			$status = "checked";
		}
	}
	return "<input type=\"checkbox\" name=\"" . $name . "\" value=\"$value\" " . $attrib . " " . $status . ">\n";
}
//
function GetRadio($name, $value, $checked, $attrib = ""){
	$status = "";
	if($value != ""){
		if(($checked == TRUE) || ($checked == 1)){
			$status = "checked";
		}
	}
	return "<input type=\"radio\" name=\"" . $name . "\" value=\"$value\" " . $attrib . " " . $status . ">\n";
}

function adjustWordQuote($text){
	$text = str_replace("&#8220;", "\"", $text);
	$text = str_replace("&#8221;", "\"", $text);
	$text = str_replace("&#8216;", "'", $text);
	$text = str_replace("&#8217;", "'", $text);
	return $text;	
}
?>