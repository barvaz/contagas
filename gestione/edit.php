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
if(!$userData["fl_admin"] && !$userData["fl_contabile"] && !$userData["fl_attivo"])
{
	doError("noaccess");
	exit;
}
$isAdmin = false;
if($userData['fl_admin']){
    $isAdmin = true;
}
$isCont = false;
if($userData['fl_contabile']){
    $isCont = true;
}

$action = $_REQUEST["action"];
$nodeId = intval($_REQUEST["nodeId"]);
$param1 = "";
if(array_key_exists("param1", $_REQUEST)){
	$param1 = $_REQUEST["param1"];
}
$formReadOnly = FALSE;
if($action == 'view'){
   $action = 'edit';
   $formReadOnly = TRUE;
}
$useDefinition = TRUE;
$ini = "../conf/gestione.ini.php";

$baseUrl = "../gestione/index.php";
///////////////////////////////////////////////////////////////////////////////
switch ($param1){
	case "users":
		$selectedSection = "users";
		$title = "modifica gasista";
		break;
	case "fornitori":
		$selectedSection = "fornitori";
		$title = "modifica fornitore";
		break;
	case "versamenti":
		$selectedSection = "versamenti";
		$title = "modifica versamento";
		break;
	case "pagamenti":
		$selectedSection = "pagamenti";
		$title = "modifica pagamento";
		break;
	case "movimenti":
		$selectedSection = "movimenti";
		$title = "modifica movimento";
		break;
	case "causali":
		$selectedSection = "causali";
		$title = "modifica causale";
		break;
	default:
		$selectedSection = "";
		$title = "";
		break;	
}
$definition = getDefinition($cnn, $ini, $selectedSection);
$attr = "";
$flTinyMCE = false;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link href="../css/default.css" rel="STYLESHEET" type="text/css" />
		<title>Modifica o Inserimento</title>
		<script type="text/javascript" src="../js/common.js"></script>
		<script src="../js/jquery-1.9.0.min.js"></script>
		<script type="text/javascript" src="../js/datepicker/js/datepicker.js"></script>
		<script type="text/javascript" src="../js/datepicker/js/eye.js"></script>
		<script type="text/javascript" src="../js/datepicker/js/utils.js"></script>
		<script>
		function popUp(url,name,features) {
			window.open(url,name,features);
		}
	</script>
</head>
<body onload="window.focus();">

	<table width="100%"  border="0" cellspacing="0" cellpadding="0" style="text-align: center;" >
		<tr>
			<td class="blockcarrier le ri bo">
			</td>
		</tr>
	</table>
<?
$validation = "";
$vaidationItem = array();
echo "<script>var items = Array();</script>\n";

if($param1=="projects" || $param1=="press" || $param1=="style" || $param1=="images")
{
	echo "<form name=\"dataForm\" method=\"post\" action=\"../gestione/admin.php\" enctype=\"multipart/form-data\" onSubmit='return validForm(this, items);'>\n";
}
else
{
	echo "<form name=\"dataForm\" method=\"post\" action=\"../gestione/admin.php\" onSubmit='return validForm(this, items);'>\n";
}

//echo "<form name=\"dataForm\" method=\"post\" action=\"../gestione/admin.php\" onSubmit='return validazione(this, items);'>\n";

echo GetInput("nodeId", "hidden", $nodeId);
echo GetInput("action", "hidden", $action);
echo GetInput("extended", "hidden", $param1);

?>
	<table width="100%"  border="0" cellspacing="0" cellpadding="5" style="text-align: center;">
		<tr>
  			<td style="width: 10px;"><img src="../img/spacer.gif" width="10" height="1" style="border: none;" /></td>
  			<td style="width: 150px;"><img src="../img/spacer.gif" width="150" height="1" style="border: none;" /></td>
  			<td style="width: 10px;"><img src="../img/spacer.gif" width="10" height="1" style="border: none;" /></td>
  			<td style="width: 100%;"><img src="../img/spacer.gif" width="1" height="1" style="border: none;" /></td>
  			<td style="width: 10px;"><img src="../img/spacer.gif" width="10" height="1" style="border: none;" /></td>
		</tr>
		<tr>
			<td colspan="5"></td>
		</tr>
		<tr>
			<td class="titolo" colspan="5"></td>
		</tr>
		<tr>
			<td style="width: 10px;"><img src="../img/spacer.gif" width="10" height="1" style="border: none;" /></td>
			<td style="width: 150px;"><img src="../img/spacer.gif" width="150" height="1" style="border: none;" /></td>
			<td style="width: 10px;"><img src="../img/spacer.gif" width="10" height="1" style="border: none;" /></td>
			<td style="width: 100%;"><img src="../img/spacer.gif" width="1" height="1" style="border: none;" /></td>
			<td style="width: 10px;"><img src="../img/spacer.gif" width="10" height="1" style="border: none;" /></td>
		</tr>
		<tr style="background-color: #666666;">
			<td>&nbsp;</td>
			<td class="titolo w" colspan="3"><? echo $title; ?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="3">

				<table width="100%" border="0" cellspacing="0" cellpadding="5" class="admintable">
<?
if(count($definition) > 0)
{
    $condition = $definition["key"] . " = $nodeId";
	if(strlen($definition["filter"]) > 0)
	{
		$condition .= " AND " . $definition["filter"];
	}
	$tmpData = getRowsFromTable($cnn, $definition["table"], $condition);
	if(count($tmpData) == 0)
	{
		$forInsert = TRUE;
		$tableData = array();
	}
	else
	{
		$forInsert = FALSE;
		$tableData = $tmpData[0];
	}
	if($nodeId == 0){
//        $forInsert = TRUE;
//        $tableData = array();
    }
	if((!$forInsert && ($action == "edit")) || ($forInsert && ($action == "new")))
	{
		$definition = readValues($definition, $tableData, FALSE, $forInsert);
		for($i=0 ; $i < count($definition["fields"]) ; $i++)
		{
			$field = $definition["fields"][$i];
			$iniItem = $definition[$field];
			$type = $iniItem["type"];
			if(($field == "dt_ins") || ($field == "dt_agg"))
			{
				continue;
			}

            if($param1 == 'users' && !$isAdmin){
                if(($field == "fl_attivo") || ($field == "fl_admin") || ($field == "fl_contabile"))
                {
                    $iniItem["ro"]=true;
                }
            }
			if(($action == "new") && ($type == "password"))
			{
				$iniItem["required"] = TRUE;
			}
			
			if($type != "hidden")
			{
				/*echo "<tr>\n";
				echo "<td colspan=\"5\"><img src=\"../img/spacer.gif\" width=\"10\" height=\"10\" border=\"0\"></td>\n";
				echo "</tr>\n";*/
				echo "<tr valign=\"top\">\n";
				echo "<td bgcolor=\"#E6E6E6\">&nbsp;</td>\n";
				echo "<td bgcolor=\"#E6E6E6\">&nbsp;</td>\n";
				echo "<td align=\"right\" bgcolor=\"#E6E6E6\" class=\"testo bold\">\n";
				echo $iniItem["label"];
				if($iniItem["required"]){
					echo "&nbsp;*";
				}
				echo "</td>\n";
				echo "<td align=\"left\" bgcolor=\"#FFFFFF\">\n";
			}
			switch ($type)
			{
				case "text":
					if(!$formReadOnly && !$iniItem["ro"] )
					{
						if($iniItem["datatype"] == "datetime" && $iniItem["macrotype"] == "calendar")
						{
							echo GetInput($field, $type, htmlspecialchars($iniItem["value"]), $iniItem["datasize"], 35, $onBlur);
							switch($iniItem["datatype"])
							{
								case "date" :
									echo "gg/mm/aaaa";
									break;
								case "datetime" :
									echo "gg/mm/aaaa hh:mm:ss";
									break;
								case "time" :
									echo "hh:mm:ss";
									break;
							}
						}
						elseif($iniItem["datatype"] == "date")
						{
							echo GetInputDate($field, htmlspecialchars($iniItem["value"]));
						}
						else
						{
							echo GetInput($field, $type, htmlspecialchars($iniItem["value"]), $iniItem["datasize"], 35);
						}
						
					}
					else
					{
						echo $iniItem["value"];
					}
					break;
				case "color":
					if(!$formReadOnly && !$iniItem["ro"] )
					{
						echo GetInputColor($field, $type, htmlspecialchars($iniItem["value"]), $iniItem["datasize"], 35);
						?><script>$(function(){
			$("#colorPicker_<?php echo $field?>").mlColorPicker({'onChange': function(val){
				$("#colorPicker_<?php echo $field?>").css("background-color", "#" + val);
				$("#text_<?php echo $field?>").text("" + val);
				$("#<?php echo $field?>").val(val);
			}});
		});
						</script><?
					}
					else
					{
						echo $iniItem["value"];
					}
					break;
				case "hidden":
					if(!$formReadOnly)
					{
						echo GetInput($field, $type, $iniItem["value"], $iniItem["datasize"], 35);
					}
					break;
				case "password":
					if(!$formReadOnly)
					{
						
						echo GetInput($field, $type, $iniItem["value"], $iniItem["datasize"], 35);
						echo "<br/>";
						echo GetInput($field . "_confirm", $type, $iniItem["value"], $iniItem["datasize"], 35);
					}
					else
					{
						echo "**********";
					}
					break;
				case "textarea":
					if(!$formReadOnly)
					{
						echo GetTextArea($field, br2nl($iniItem["value"]), $iniItem["width"], $iniItem["height"]);
						//echo GetTextArea($field, $iniItem["value"], $iniItem["width"], $iniItem["height"]);
					}
					else
					{
						echo $iniItem["value"];
					}
					break;
				case "combo":
					switch($iniItem["combo_source"])
					{
						case "table":
							if(!$formReadOnly && !$iniItem["ro"])
							{
								$sql = "SELECT * FROM " .  $iniItem["combo_lookup"];
								if(strlen($iniItem["combo_filter"]) > 0)
								{
									$sql .= " WHERE " . $iniItem["combo_filter"];
								}
								if(strlen($iniItem["combo_order"]) > 0)
								{
									$sql .= " ORDER BY " . $iniItem["combo_order"];
								}
								if($iniItem["combo_widget"] == "list")
								{
									echo GetComboFromSql($field, $cnn, $sql, $iniItem["combo_key"], $iniItem["combo_value"], $iniItem["value"], $iniItem["combo_multiple"], $iniItem["combo_emptyline"]);
								}
								else
								{
									echo getCheckboxGroupFromSql($field, $cnn, $sql, $iniItem["combo_key"], $iniItem["combo_value"], $iniItem["value"], $iniItem["combo_multiple"], $iniItem["combo_emptyline"]);
								}
							}
							else
							{
								echo getValsFromDB($cnn, $iniItem["combo_lookup"], $iniItem["combo_order"], $iniItem["combo_key"], $iniItem["combo_value"], $iniItem["value"], $iniItem["combo_multiple"], $iniItem["combo_emptyline"], "disabled");
							}
							break;
						case "array":
							if(strlen($iniItem["combo_lookup_key"]) > 0)
							{
								$tmp = $$iniItem["combo_lookup"];
								$data = $tmp[$iniItem["combo_lookup_key"]];
							}
							else
							{
								$data = $$iniItem["combo_lookup"];
							}
							if(!$formReadOnly)
							{
								if($iniItem["combo_widget"] == "list")
								{
									echo getComboFromArray($field, $data, $iniItem["combo_key"], $iniItem["combo_value"], $iniItem["value"], $iniItem["combo_multiple"], $iniItem["combo_emptyline"]);
								}
								else
								{
									echo getCheckboxGroupFromArray ($field, $data, $iniItem["combo_key"], $iniItem["combo_value"], $iniItem["value"], $iniItem["combo_multiple"], $iniItem["combo_emptyline"]);
								}
							}
							else
							{
								echo getValsFromArray($data, $iniItem["combo_key"], $iniItem["combo_value"], $iniItem["value"], $iniItem["combo_multiple"], $iniItem["combo_emptyline"]);
							}
							break;
					}
					break;
				case "checkbox":
					if(!$formReadOnly && !$iniItem["ro"])
					{
						echo GetCheckBox($field, 1, $iniItem["value"]);
					}
					else
					{
						echo GetCheckBox($field, 1, $iniItem["value"], "disabled");
					}
					break;
				case "radio":
					if(!$formReadOnly)
					{
						switch($iniItem["radio_source"])
						{
							case "values":
								foreach($iniItem["radio_value"] as $r)
								{
									echo GetRadio($field, $r, ($r == $iniItem["value"]));

									echo $r;
								}
								break;
						}
					}
					else
					{
						echo $iniItem["value"];
					}
					break;
				case "htmlarea":
					if(!$formReadOnly)
					{
						if($iniItem["config"] == "simple") {
						?>
							<?if(!$flTinyMCE){?>
							<script type="text/javascript" src="../js/tiny_mce/tiny_mce.js"></script>
						<?
							$flTinyMCE = true;
						}
						?>
							<script type="text/javascript">
							tinyMCE.init({
								// General options
								mode : "textareas",
								theme : "advanced",
								editor_selector : "<?=$field?>",
								plugins : "table,inlinepopups",
							
								// Theme options
							//	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,|,table,removeformat,code",
								theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,link,unlink,|,table,removeformat,code",
								theme_advanced_buttons2 : "",
								theme_advanced_buttons3 : "",
								theme_advanced_buttons4 : "",
								theme_advanced_toolbar_location : "top",
								theme_advanced_toolbar_align : "left",
								theme_advanced_statusbar_location : "bottom",
								theme_advanced_resizing : true,
							
								// Example content CSS (should be your site CSS)
								content_css : "../style/style.css"
							});
							</script>
						<? }else { // "advanced" ?>
							<? if(!$flTinyMCE){ ?>
							<script type="text/javascript" src="../js/tiny_mce/tiny_mce.js"></script>
							<?
								$flTinyMCE = true;
							}
							?>
							<script type="text/javascript">
							tinyMCE.init({
								// General options
								mode : "textareas",
								theme : "advanced",
								editor_selector : "<?=$field?>"
							});
							</script>
						<? }
						echo GetHTMLArea($field, $iniItem["value"], $iniItem["width"], $iniItem["height"] );
					}
					else
					{
						echo $iniItem["value"];
					}
					break;
			}
			if($type != "hidden")
			{
				echo "</td>\n";
				echo "<td bgcolor=\"#FFFFFF\">&nbsp;</td>\n";
				echo "</tr>\n";
			}
			//items = [['NOME','required','text','minlen'],['PASS','required','password','minlen']]
			if(($type == "combo") && $iniItem["combo_multiple"])
			{
				$field = $field . "[]";
			}
			if($iniItem["minlen"] > 0 || $iniItem["required"] == TRUE)
			{
				$vaidationItem[] = "['$field','" . $iniItem["validation"] . "','" . addslashes($iniItem["label"]) . "', " . ($iniItem["required"] ? "true" : "false") . ", " . $iniItem["minlen"] . ", " . $iniItem["datasize"]. "]";
			}
			/*if($iniItem["minlen"] > 0 || $iniItem["required"] == TRUE || $iniItem["validation"] != "")
			{
				$vaidationItem[] = "['$field', " . ($iniItem["required"] ? "true" : "false") . ", " . $iniItem["minlen"] . ", " . $iniItem["datasize"] . ", '" . $iniItem["validation"] . "','" . addslashes($iniItem["label"]) . "']";
			}*/
		}
	}
}
?>
		</table>

			</td>
			<td>&nbsp;</td>
		</tr>

		<tr>
			<td colspan="5"><img src="../img/spacer.gif" width="10" height="10" style="border: none" /></td>
		</tr>
		<tr>
			<td colspan="5" style="height:30px; background-color: #666666" align="center" valign="middle" >
<?
if(!$formReadOnly)
{
	echo "<input type=\"reset\" title=\"Annulla\" class=\"forms\">\n";
	echo "<input type=\"submit\"  title=\"Continua\" class=\"forms\" $attr>\n";
}
?>
			</td>
		</tr>
	</table>
<?
echo "</form>\n";
if(count($vaidationItem) > 0)
{
	$validation = "[" . implode(", ", $vaidationItem) . "]";
	echo "<script>items = $validation;</script>";
}
?>
</body>
</html>
