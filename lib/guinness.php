<?
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
function getIniFile($iniFile){
	static $ini;
	static $data = array();
	if($iniFile != $ini){
		$data = parse_ini_file($iniFile, 1);
		$ini = $iniFile;
	}
	return $data;
}
//
function getIniSection($iniFile, $iniSection){
	static $ini;
	static $sec;
	static $section = array();
	$data = getIniFile($iniFile);
	if(($iniFile != $ini) || ($iniSection != $sec)){
		$section = $data[$iniSection];
		$sec = $iniSection;
	}
	return $section;
}
//
function getDefinition(&$db, $iniFile, $iniSection, $tableName = ""){
	static $ini;
	static $sec;
	static $tab;
	static $data = array();
	if((strlen($iniSection) != 0) && (strlen($iniFile) != 0)){
		if(($iniFile != $ini) || ($iniSection != $sec) || ($tableName != $tab)){
			$tmp = array();
			$data = array();
			$section = getIniSection($iniFile, $iniSection);
			if(is_array($section)){
				$fields = explode(",", $section["fields"]);
				for($i = 0; $i < count($fields); $i++){
					$fields[$i] = trim($fields[$i]);
				}
				if(strlen($tableName) > 0){
					$data["table"] = $tableName;
				}else{
					$data["table"] = $section["table"];
				}
				$data["fields"] = $fields;
				$data["key"] = $section["key"];
				$data["order"] = $section["orderby"];
				$data["filter"] = $section["filter"];
				for($i=0 ; $i < count($fields) ; $i++){
					$field = $fields[$i];
//debugMsg($field);
					$data[$field] = Array();

					$data[$field]["label"] = $section[$field . "_label"];
					if(strlen($data[$field]["label"]) == 0){
						$data[$field]["label"] = $field;
					}
					$data[$field]["type"] = strtolower($section[$field . "_type"]);
					if(strlen($data[$field]["type"]) == 0){
						$data[$field]["type"] = "text";
					}
					$data[$field]["ro"] = FALSE;
					$data[$field]["insertvalue"] = "";
					$data[$field]["defaultvalue"] = "";
					$data[$field]["size"] = 0;
					//$data[$field]["optional"] = FALSE;
					$data[$field]["required"] = FALSE;
					$data[$field]["validation"] = "";
					$data[$field]["skipempty"] = FALSE;
					$data[$field]["minlen"] = 0;

					if(array_key_exists($field . "_source", $section)){
						$data[$field]["source"] = trim($section[$field . "_source"]);
					}
					
					if(array_key_exists($field . "_ro", $section)){
						$data[$field]["ro"] = ($section[$field . "_ro"] == TRUE ) ? TRUE : FALSE;
					}
					if(array_key_exists($field . "_insertvalue", $section)){
						$data[$field]["insertvalue"] = $section[$field . "_insertvalue"];
						//$data[$field]["ro"] = TRUE;
					}
					if(array_key_exists($field . "_defaultvalue", $section)){
						$data[$field]["defaultvalue"] = $section[$field . "_defaultvalue"];
					}
					if(array_key_exists($field . "_size", $section)){
						$data[$field]["size"] = intval($section[$field . "_size"]);
					}
					if(array_key_exists($field . "_minlen", $section)){
						$data[$field]["minlen"] = intval($section[$field . "_minlen"]);
					}
					//if(array_key_exists($field . "_optional", $section)){
					//	$data[$field]["optional"] = ($section[$field . "_optional"] == TRUE ) ? TRUE : FALSE;
					//}
					if(array_key_exists($field . "_required", $section)){
						$data[$field]["required"] = ($section[$field . "_required"] == TRUE ) ? TRUE : FALSE;
					}
					if(array_key_exists($field . "_validation", $section)){
						$data[$field]["validation"] = strtolower($section[$field . "_validation"]);
					}
					if(array_key_exists($field . "_skipempty", $section)){
						$data[$field]["skipempty"] = ($section[$field . "_skipempty"] == TRUE ) ? TRUE : FALSE;
					}
					switch($section[$field . "_type"]){
						case "combo":
							$data[$field]["combo_source"] = $section[$field . "_combo_source"];
							switch($data[$field]["combo_source"]){
								case "table":
									$data[$field]["combo_lookup"] = $section[$field . "_combo_lookup"];
									$data[$field]["combo_key"] = $section[$field . "_combo_key"];
									$data[$field]["combo_value"] = $section[$field . "_combo_value"];
									$data[$field]["combo_filter"] = $section[$field . "_combo_filter"];
									$data[$field]["combo_order"] = $section[$field . "_combo_order"];
									break;
								case "sql":
									$data[$field]["combo_sql"] = $section[$field . "_combo_sql"];
									$data[$field]["combo_key"] = $section[$field . "_combo_key"];
									$data[$field]["combo_value"] = $section[$field . "_combo_value"];
									break;
								case "values":
									$data[$field]["combo_key_val"] = $section[$field . "combo_key_val"];
									break;
								case "array":
									$data[$field]["combo_lookup"] = $section[$field . "_combo_lookup"];
									$data[$field]["combo_lookup_key"] = $section[$field . "_combo_lookup_key"];
									$data[$field]["combo_key"] = $section[$field . "_combo_key"];
									$data[$field]["combo_value"] = $section[$field . "_combo_value"];
									break;
								case "variable":
									//todo
									break;
							}
							$data[$field]["combo_widget"] = "list";
							if (strtolower($section[$field . "_combo_widget"]) == "group" ){
								$data[$field]["combo_widget"] = "group";
							}
							$data[$field]["combo_emptyline"] = ($section[$field . "_combo_emptyline"] == TRUE ) ? TRUE : FALSE;
							$data[$field]["combo_multiple"] = ($section[$field . "_combo_multiple"] == TRUE ) ? TRUE : FALSE;
							break;
						case "file":
							$data[$field]["enable_delete"] = ($section[$field . "_disable_delete"] == TRUE ) ? FALSE : TRUE;
							break;
						case "img":
							$data[$field]["enable_delete"] = ($section[$field . "_disable_delete"] == TRUE ) ? FALSE : TRUE;
							break;
						case "text":
						case "password":
							$data[$field]["width"] = intval($section[$field . "_cols"]);
							break;
						case "textarea":
							$data[$field]["width"] = intval($section[$field . "_cols"]);
							$data[$field]["height"] = intval($section[$field . "_rows"]);
							break;
						case "htmlarea":
							$data[$field]["width"] = intval($section[$field . "_cols"]);
							$data[$field]["height"] = intval($section[$field . "_rows"]);
							$data[$field]["config"] = $section[$field . "_config"];
							break;
						case "checkbox":
							break;
						case "radio":
							$data[$field]["radio_source"] = $section[$field . "_radio_source"];
							switch($data[$field]["radio_source"]){
								case "table":
									$data[$field]["radio_lookup"] = $section[$field . "_radio_lookup"];
									$data[$field]["radio_key"] = $section[$field . "_radio_key"];
									$data[$field]["radio_value"] = $section[$field . "_radio_value"];
									$data[$field]["radio_filter"] = $section[$field . "_radio_filter"];
									$data[$field]["radio_order"] = $section[$field . "_radio_order"];
									break;
								case "sql":
									$data[$field]["radio_sql"] = $section[$field . "_radio_sql"];
									$data[$field]["radio_key"] = $section[$field . "_radio_key"];
									$data[$field]["radio_value"] = $section[$field . "_radio_value"];
									break;
								case "values":
									$data[$field]["radio_value"] = explode(",", $section[$field . "_radio_value"]);
									break;
								case "variable":
									//todo
									break;
							}
							break;
					}
				}
				if(strlen($data["table"]) > 0){
					$query = "SHOW FIELDS FROM `" . $data["table"] . "`";
					//debugMsg($query);
					$rs = mysql_query($query, $db);
					while ($row = mysql_fetch_array($rs)){
						if(in_array($row["Field"], $fields)){
							$dataType = $row["Type"];
							$x = strpos($dataType, "(");
							if($x > 0){
								$type = substr($row["Type"], 0, $x );
								$size = substr($row["Type"], ++$x, -1);
							}else{
								$size = "";
								$type = $row["Type"];
							}
							$data[$row["Field"]]["datatype"] = strtolower($type);
							$data[$row["Field"]]["macrotype"] = "text";//(text, int, calendar, float)
							$data[$row["Field"]]["datasize"] = 10;
							switch(strtoupper($type)){
								case "TINYINT":
								case "SMALLINT":
								case "MEDIUMINT":
								case "INT":
									$data[$row["Field"]]["macrotype"] = "int";
									$data[$row["Field"]]["datasize"] = intval($size);
									break;
	//							case "BIGINT":
	//								break;

								case "CHAR":
								case "VARCHAR":
									$data[$row["Field"]]["macrotype"] = "text";
									$data[$row["Field"]]["datasize"] = intval($size);
									break;

								case "DATE":
									$data[$row["Field"]]["macrotype"] = "calendar";
									$data[$row["Field"]]["datasize"] = 10;
									break;
								case "TIME":
									$data[$row["Field"]]["macrotype"] = "calendar";
									$data[$row["Field"]]["datasize"] = 10;
									break;
	//							case "YEAR":
	//								$data[$row["Field"]]["macrotype"] = "year";
	//								$data[$row["Field"]]["datasize"] = intval($size);
	//								break;
								case "DATETIME":
									$data[$row["Field"]]["macrotype"] = "calendar";
									$data[$row["Field"]]["datasize"] = 19;
									break;
								case "TIMESTAMP":
									$data[$row["Field"]]["macrotype"] = "calendar";
									$data[$row["Field"]]["datasize"] = 14;
									break;

								case "FLOAT":
								case "DOUBLE":
									$data[$row["Field"]]["macrotype"] = "float";
									if($size != ""){
										$y = strpos($size, ",");
										if($y > 0){
											$data[$row["Field"]]["datasize"] = intval(substr($size, 0, $y));
										}else{
											$data[$row["Field"]]["datasize"] = 10;
										}
									}
									break;
	//							case "DECIMAL":
	//								break;

								case "TINYTEXT":
									$data[$row["Field"]]["macrotype"] = "text";
									$data[$row["Field"]]["datasize"] = 255;
									break;
								case "TEXT":
									$data[$row["Field"]]["macrotype"] = "text";
									$data[$row["Field"]]["datasize"] = 65535;
									break;
	//							case "MEDIUMTEXT":
	//								$data[$row["Field"]]["macrotype"] = "text";
	//								$data[$row["Field"]]["datasize"] = 16777215;
	//								break;
	//							case "LONGTEXT":
	//								$data[$row["Field"]]["macrotype"] = "text";
	//								$data[$row["Field"]]["datasize"] = 4294967295;
	//								break;

								case "TINYBLOB":
									$data[$row["Field"]]["macrotype"] = "text";
									$data[$row["Field"]]["datasize"] = 255;
									break;
								case "BLOB":
									$data[$row["Field"]]["macrotype"] = "text";
									$data[$row["Field"]]["datasize"] = 65535;
									break;
	//							case "MEDIUMBLOB":
	//								$data[$row["Field"]]["macrotype"] = "text";
	//								$data[$row["Field"]]["datasize"] = 16777215;
	//								break;
	//							case "LONGBLOB":
	//								$data[$row["Field"]]["macrotype"] = "text";
	//								$data[$row["Field"]]["datasize"] = 4294967295;
	//								break;

	//							case "ENUM":
	//								break;
	//							case "SET":
	//								break;
							}
							if($data[$row["Field"]]["datasize"] == 0){
								$data[$row["Field"]]["datasize"] = 10;
							}
							if($data[$row["Field"]]["size"] > 0){
								$data[$row["Field"]]["datasize"] = min($data[$row["Field"]]["size"], $data[$row["Field"]]["datasize"]);
							}
						}
					}
				}
			}
			//debugMsg($data);
			$ini = $iniFile;
			$sec = $iniSection;
			$tab = $tableName;
		}
	}
	return $data;
}
//popola l'array outDefinition con le definizioni lette da formDefinition ed i valori letti da values
//facendo caso ai valori di default
function readValues($formDefinition, $values, $escape = FALSE, $forInsert = FALSE){
	$outDefinition = array();
	$outDefinition = $formDefinition;
	for($i=0; $i < count($formDefinition["fields"]); $i++){
		$fieldName = $formDefinition["fields"][$i];
		$val = "";
		if(($formDefinition[$fieldName]["insertvalue"] == "") || ($forInsert == FALSE)){
			if($formDefinition[$fieldName]["defaultvalue"] != ""){
				$val = $formDefinition[$fieldName]["defaultvalue"];
			}
			if(is_array($values) && array_key_exists($fieldName, $values)){
				if(is_array($values[$fieldName])){
					$val = implode(",", $values[$fieldName]);
				}else{

					switch($formDefinition[$fieldName]["datatype"]){
						case "date":
							$val = getHumanDate($values[$fieldName]);
							break;
						case "datetime";
							$val = getHumanDateTime($values[$fieldName]);
							break;
						default:
							$val = $values[$fieldName];
					}
				}
				//debugMsg($val);
			}
		}else{
			$val = $formDefinition[$fieldName]["insertvalue"];
		}
		if($escape){
			$outDefinition[$fieldName]["value"] = addslashes($val);
		}else{
			$outDefinition[$fieldName]["value"] = $val;
		}
	}
	return 	$outDefinition;
}
//imposta i valori di outDefinition con i dati inviati dall'utente
function setValues($formDefinition, $nameAppend, $values, $forInsert = FALSE){
//debugMsg($values);
	$outDefinition = array();
	$outDefinition = $formDefinition;
	foreach($values as $k => $v){
		$values[$k] = adjustWordQuote($v);
		if(!ini_get("magic_quotes_gpc")){
			if(is_array($v)){
				foreach($v as $kk => $kk){
					$values[$k][$kk] =  addslashes($values[$k][$kk]);
				}
			}else{
				$values[$k] = addslashes($values[$k]);
			}
		}
	}
	for($i=0; $i < count($formDefinition["fields"]); $i++){
		$fieldName = $formDefinition["fields"][$i];
			if(($formDefinition[$fieldName]["insertvalue"] == "") || ($forInsert == FALSE)){
	//debugMsg($formDefinition[$fieldName]);		
				$val = "";
				$t = $formDefinition[$fieldName]["type"];
				if(($t != "file") && ($t != "obj") && ($t != "img")){
				//if(($t != "file")){
					if(array_key_exists($fieldName . $nameAppend, $values)){
						if(is_array($values[$fieldName . $nameAppend])){
				
							$val = implode(",", $values[$fieldName . $nameAppend]);
							//debugMsg($val);
						}else{
							switch($formDefinition[$fieldName]["type"]){
								case "password":
									if($values[$fieldName . $nameAppend . "_confirm"] == $values[$fieldName . $nameAppend]){
										$val = ($values[$fieldName]);
									}
									break;
								case "hidden":
								case "text":
									//str_replace("�", "�0", $altaclsData[$i]["OTHER_R"]);
									$val = ($values[$fieldName . $nameAppend]);
									break;
								case "textarea":
									$val = str_replace("\r\n", "", nl2br(htmlspecialchars($values[$fieldName . $nameAppend])));
                                    /** @noinspection PhpDeprecationInspection */
                                    $val = (eregi_replace("[\n]*[\r]*",  "", $val));
									break;
								case "htmlarea":
									/** @noinspection PhpDeprecationInspection */
                                    $val = (eregi_replace("[\n]*[\r]*",  "", $values[$fieldName . $nameAppend]));
									break;
								default:
									$val = $values[$fieldName . $nameAppend];
							}
							if(!ini_get("magic_quotes_gpc")){
						
								//$val = addslashes($val);
							} 
						}
					}else{
						if($formDefinition[$fieldName]["type"] == "combo"){
							if($formDefinition[$fieldName]["combo_widget"]){
								$val = implode(",", getArrayFromList( $values, $fieldName . $nameAppend));
							}
						}
					}
				}else{
					$val["file"] = "";
					$val["file_ext"] = "";
					$val["file_size"] = "";
					$val["file_name"] = "";
					$val["file_delete"] = 0;
					if(array_key_exists($fieldName . $nameAppend . "_delete", $values)){
						$val["file_delete"] = $values[$fieldName . $nameAppend . "_delete"];
					}
					if(!$val["file_delete"]){
						if(array_key_exists($fieldName . $nameAppend, $_FILES)){
							if(($_FILES[$fieldName . $nameAppend]["error"] == 0) && ($_FILES[$fieldName . $nameAppend]["size"] > 0)){

								$path_parts = pathinfo($_FILES[$fieldName . $nameAppend]["name"]);
								$val["file_ext"] = $path_parts["extension"];
								$val["file_size"] = $_FILES[$fieldName . $nameAppend]["size"];
								$val["file_name"] = $_FILES[$fieldName . $nameAppend]["name"];
								$val["file"] = $_FILES[$fieldName . $nameAppend]["tmp_name"];
							}
						}
					}
				}

				if(($t != "file") && ($t != "obj") && ($t != "img") && (strlen($val) == 0 ) && ($formDefinition[$fieldName]["skipempty"] == FALSE)){
				//if(($t != "file") && (strlen($val) == 0 ) && ($formDefinition[$fieldName]["skipempty"] == FALSE)){
					switch($formDefinition[$fieldName]["macrotype"]){//(text, int, calendar, float)
						case "text":
							$val = "";
							break;
						case "int":
						case "float":
							$val = 0;
							break;
						case "calendar":
							switch($formDefinition[$fieldName]["datatype"]){
								case "date":
									$val = "00/00/0000";
									break;
								case "time":
									$val = "00:00:00";
									break;
								case "datetime";
									$val = "00/00/0000 00:00:00";
									break;
								case "timestamp":
									$val = "0";
									break;
								case "year":
									$val = "0";
									break;
							}
							break;
					}
				}
			}else{
				if(($t != "file") && ($t != "obj") && ($t != "img")){
					$val = $formDefinition[$fieldName]["insertvalue"];
				}
			}
			$outDefinition[$fieldName]["value"] = $val;
	}
//	debugMsg($outDefinition);
	return 	$outDefinition;
}
//
function getParsedValues($itemDefinition, $enableEscape = FALSE){
	switch($itemDefinition["macrotype"]){//(text, int, calendar, float)
		case "text":
			if($enableEscape){
				$value = "'" . addslashes($itemDefinition["value"]) . "'";
			}else{
				$value = "'" . $itemDefinition["value"] . "'";
			}
			break;
		case "int":
			$value = intval($itemDefinition["value"]);
			break;
		case "float":
			$value = $itemDefinition["value"];
			break;
		case "calendar":
			switch($itemDefinition["datatype"]){
				case "date":
					$value = "'" . getIsoDate($itemDefinition["value"]) . "'";
					break;
				case "time":
					$value = "'" . getIsoTime($itemDefinition["value"]) . "'";
					break;
				case "datetime":
					$value = "'" . getIsoDateTime($itemDefinition["value"]) . "'";
					break;
				case "year":
					$value = "'" . intval($itemDefinition["value"]) . "'";
					break;
				case "timestamp":
					$value = "'" . intval($itemDefinition["value"]) . "'";
			}
			break;
	}
	return $value;
}
//
function getUpdateQuery($tableName, $formDefinition, $otherValues = 0, $excludeFields = 0, $enableEscape = FALSE){
	for($i = 0; $i < count($formDefinition["fields"]); $i++){
		$fieldName = $formDefinition["fields"][$i];
		if(is_array($excludeFields)){
			if (in_array($fieldName, $excludeFields)) {
				continue;
			}
		}
		if($formDefinition[$formDefinition["fields"][$i]]["ro"]){
			continue;
		}
		
		if((strlen($formDefinition[$fieldName]["value"]) > 0 ) || ($formDefinition[$fieldName]["skipempty"] == FALSE)){
			$query .= "`" . $fieldName . "` = ";
			$query .= getParsedValues($formDefinition[$fieldName], $enableEscape) . ", ";
		}
	}
	if(is_array($otherValues)){
		foreach($otherValues as $k => $v){
			$query .= "`$k` = '$v', ";
		}
	}
	if(strlen($query) > 0){
		$query = substr($query, 0, -2);
	}
	$query = "UPDATE `$tableName` SET " . $query;

	return $query;
}
//
function getInsertQuery($tableName, $formDefinition, $otherValues = 0, $excludeFields = 0, $enableEscape = FALSE, $queryPart = "all"){
	$fields = "";
	$values = "";
	for($i = 0; $i < count($formDefinition["fields"]); $i++){
		$fieldName = $formDefinition["fields"][$i];
		if(is_array($excludeFields)){
			if (in_array($fieldName, $excludeFields)) {
				continue;
			}
		}
		if($formDefinition[$formDefinition["fields"][$i]]["ro"]){
			continue;
		}
		if($queryPart != "all"){
			$fields .= "`" . $fieldName . "`, ";
			$values .= getParsedValues($formDefinition[$fieldName], $enableEscape) . ", ";
		}else{
			if((strlen($formDefinition[$fieldName]["value"]) > 0 ) || ($formDefinition[$fieldName]["skipempty"] == FALSE)){
				$fields .= "`" . $fieldName . "`, ";
				$values .= getParsedValues($formDefinition[$fieldName], $enableEscape) . ", ";
			}
		}
	}
	if(is_array($otherValues)){
		foreach($otherValues as $k => $v){
			$fields .= "`$k`, ";
			$values .= "'$v', ";
		}
	}
	if(strlen($fields) > 0){
		$fields = substr($fields, 0, -2);
		$values = substr($values, 0, -2);
	}
	switch($queryPart){
		case "all":
			$query = "INSERT INTO `$tableName` ($fields) VALUES ($values)";
			break;
		case "values":
			$query = "($values)";
			break;
		case "fields":
			$query = "($fields)";
			break;
	}
	return $query;
}

?>