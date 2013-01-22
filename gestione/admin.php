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


include ("../conf/conf.php");
include ("../lib/sessions.php");
include ("../lib/users.php");
include ("../lib/util.php");
include ("../lib/htmlgen.php");
include ("../lib/guinness.php");
include ("../lib/gas.php");

$cnn = getDbConnection();
$userId = getUserId($cnn);

if($userId > 0)
{
	$userData = getUserData($cnn, $userId);
}
else
{
	header("location: ../gestione/login.php");
	exit;
}
//debug($userData);
if(!$userData["fl_admin"] && !$userData["fl_contabile"])
{
	doError("noaccess");
	exit;
}

$action = $_REQUEST["action"];
$nodeId = intval($_REQUEST["nodeId"]);
$param1 = "";
if(array_key_exists("extended", $_REQUEST)){
	$param1 = $_REQUEST["extended"];
}
if(array_key_exists("param1", $_REQUEST)){
	$param1 = $_REQUEST["param1"];
}
$curDate = date("Y-m-d H:i:s");
$formReadOnly = FALSE;
$useDefinition = TRUE;
$ini = "../conf/gestione.ini.php";

$baseUrl = "../gestione/index.php";
///////////////////////////////////////////////////////////////////////////////

array("dt_ins","dt_agg");
/*$size_x = 700;*/
switch ($param1){
	case "users":
		$selectedSection = "users";
		$table = "users";
		$title = "modifica gasista";
		break;
	case "fornitori":
		$selectedSection = "fornitori";
		$table = "fornitori";
		$title = "modifica fornitore";
		break;
	case "versamenti":
		$selectedSection = "versamenti";
		$table = "versamenti";
		$title = "modifica versamento";
		break;
	case "pagamenti":
		$selectedSection = "pagamenti";
		$table = "pagamenti";
		$title = "modifica pagamento";
		break;
	case "movimenti":
		$selectedSection = "movimenti";
		$table = "movimenti";
		$title = "modifica movimento";
		break;
	case "causali":
		$selectedSection = "causali";
		$table = "causali";
		$title = "modifica causale";
		break;
	default:
		$selectedSection = "";
		$title = "";
		break;	
}
$definition = getDefinition($cnn, $ini, $selectedSection);
$tmpData = getRowsFromTable($cnn, $definition["table"], $condition);
if(count($tmpData) > 0)
{
	$blockData = $tmpData[0];
}
$forInsert = FALSE;
switch ($action)
{
	case "new":
	case "edit":
		if($action == "new")
		{
			$forInsert = TRUE;
		}
		$newValues = array();
		$newValues = setValues($definition, "", $_POST, $forInsert);
		$otherValues = array();
		
		if(key_exists('importo', $newValues)){
			$tmp = $newValues['importo']['value'];
			$tmp = str_replace(',', '.', $tmp);
			$newValues['importo']['value'] = $tmp;
		}
		if($param1 == "versamenti" || $param1 == "pagamenti" || $param1 == "movimenti")
		{
			$otherValues["id_autore"] = $userId;
		}
		
		if($forInsert)
		{
			$otherValues["dt_ins"] = $curDate;
			$otherValues["dt_agg"] = $curDate;
			
			
			$sql = getInsertQuery($definition["table"], $newValues, $otherValues, $excludeValues);//array("dt_ins","dt_agg"));
			$result = mysql_query($sql, $cnn);
			$sqlError = mysql_errno($cnn);
			
			if($sqlError)
			{
				if($sqlError == 1062)
				{
					echo("chiave duplicata");
					exit;
				}
				else
				{
					doError ("sql","Errore nell'esecuzione della query: " . $sql);
				}
			}
			$nodeId = mysql_insert_id();
		}
		else
		{
			if($param1 == "users"){
				if(strlen($_REQUEST["password"]) == 0 || $_REQUEST["password"] != $_REQUEST["password_confirm"]){
					$excludeValues[] = "password";
				}
				
			}
			$otherValues["dt_agg"] = $curDate;
			
			$sql = getUpdateQuery($definition["table"], $newValues, $otherValues, $excludeValues);//array("dt_ins","dt_agg"));
			
			$sql .= " WHERE " . $definition["key"] . " = $nodeId";
			$result = mysql_query($sql, $cnn) or doError("sql", "Errore nell'esecuzione della query: " . $sql);
		}
			

		break;
	case "delete":
		if($param1 == "users"){
			$sql = "update `" . $definition["table"] . "` set fl_attivo = 0 where " . $definition["key"] . " = " . $nodeId;
		$result = mysql_query($sql, $cnn) or doError("sql","Errore nell'esecuzione della query: " . $sql);
		}else{
			$sql = "delete from `" . $definition["table"] . "` where " . $definition["key"] . " = " . $nodeId;
			$result = mysql_query($sql, $cnn) or doError("sql","Errore nell'esecuzione della query: " . $sql);	
		}
		
		break;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<?
echo "<script language=\"javascript\">\n";
echo "function chiudiAndRefresh(){\n";
echo "opener.location.reload();\n";
echo "window.close();\n";
echo "}\n";
echo "</script>\n";
?>
</head>
<body onLoad="chiudiAndRefresh()">
</body>
</html>
