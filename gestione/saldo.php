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
if(array_key_exists("param1", $_REQUEST)){
	$param1 = $_REQUEST["param1"];
}
$formReadOnly = FALSE;
$useDefinition = TRUE;
$ini = "../conf/gestione.ini.php";

$baseUrl = "../gestione/index.php";
///////////////////////////////////////////////////////////////////////////////
switch ($param1){
	case "users":
		$selectedSection = "users";
		$title = "modifica gasista";
		$title = "situazione contabile gasista";
		break;
	case "fornitori":
		$selectedSection = "fornitori";
		$title = "modifica fornitore";
		break;
	case "versamenti":
		$selectedSection = "versamenti";
		$title = "modifica versamento";
		break;
	case "ordini":
		$selectedSection = "ordini";
		$title = "modifica ordine";
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
		<title>Content Management</title>
</head>
<body onload="window.focus();">

	<table width="100%"  border="0" cellspacing="0" cellpadding="0" style="text-align: center;" >
		<tr>
			<td class="blockcarrier le ri bo">
			</td>
		</tr>
	</table>
<?php
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
			<td class="titolo w" colspan="3"><?php echo $title; ?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="3">

				<table width="100%" border="0" cellspacing="0" cellpadding="5" class="admintable">
<?php
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
}
// query per fotografare situazione contabile del conto  
//USCITE
           $conta_uscite=0;
           $sql_uscite = "select importo from ordini where fl_paid=1";
                $result = mysqli_query($cnn, $sql_uscite) or doError("sql_uscite","Errore nell'esecuzione della query: " . $sql_uscite);
                while ($row = mysqli_fetch_assoc($result)){
                 $importo=$row["importo"];
                 $nm_nome=$row["nm_nome"];
                 $ds_nota=$row["ds_nota"];
                 $dt_ordine=$row["dt_ordine"];
                 $conta_uscite=$conta_uscite+$importo;
                }
                mysqli_free_result($result);
//ENTRATE
           $conta_entrate=0;
           $sql_entrate = " select importo from versamenti";
                $result = mysqli_query($cnn, $sql_entrate) or doError("sql_entrate","Errore nell'esecuzione della query: " . $sql_entrate);
                while ($row = mysqli_fetch_assoc($result)){
                 $importo=$row["importo"];
                 $ds_causale=$row["ds_causale"];
                 $dt_versamento=$row["dt_versamento"];
                 $conta_entrate=$conta_entrate+$importo;
                }
                mysqli_free_result($result);
//TOTALI
$euro_tot=$conta_entrate-$conta_uscite;
$ts = date("Y-m-d");
           echo "<table width=\"100%\"  border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"text-align: center;\" >\n";
           echo "<td>&nbsp;</td> \n";
           echo "<hr noshade size='1' color='$leadcolor' style='dot'>";
           echo "<tr><td>SALDO DA ESTRATTO CONTO AL $ts --> <b>$euro_tot</b> Euro</td></tr>\n";
           echo "</table> \n";
//DETTAGLI
           //QUOTE ANNUALI
           $conta_quote=0;
           $conta_ingressi=0;
           $conta_spese=0;
           $saldo_gas=0;
           $sql_ingressi = "select importo from versamenti where id_causale in (7,8)";
                $result = mysqli_query($cnn, $sql_ingressi) or doError("sql_ingressi","Errore nell'esecuzione della query: " . $sql_ingressi);
                while ($row = mysqli_fetch_assoc($result)){
                 $importo=$row["importo"];
                 $conta_ingressi=$conta_ingressi+$importo;
                }
                mysqli_free_result($result);
           //$sql_quote = "select importo from movimenti where id_ordine=12";
           $sql_quote = "select importo from versamenti where id_causale=2";
                $result = mysqli_query($cnn, $sql_quote) or doError("sql_quote","Errore nell'esecuzione della query: " . $sql_quote);
                while ($row = mysqli_fetch_assoc($result)){
                 $importo=$row["importo"];
                 $conta_quote=$conta_quote+$importo;
                }
                mysqli_free_result($result);
           $sql_spese = "select importo from ordini where id_causale in (4,6) and fl_paid=1";
                $result = mysqli_query($cnn, $sql_spese) or doError("sql_spese","Errore nell'esecuzione della query: " . $sql_spese);
                while ($row = mysqli_fetch_assoc($result)){
                 $importo=$row["importo"];
                 $conta_spese=$conta_spese+$importo;
                }
                mysqli_free_result($result);
           $saldo_gas=$conta_quote+$conta_ingressi-$conta_spese;
           echo "<table width=\"100%\"  border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"text-align: center;\" >\n";
           echo "<td>&nbsp;</td> \n";
           echo "<hr noshade size='1' color='$leadcolor' style='dot'>";
           echo "<tr><td>di cui del GAS: </td> </tr> \n";

           if($saldo_gas >= 0)
           {
            echo "<tr><td><b>$conta_quote</b> Euro di quote annuali + <b>$conta_ingressi</b> Euro di altre entrate - <b>$conta_spese</b> Euro di spese = <b>$saldo_gas</b> Euro a disposizione </td></tr> \n";
           }else{
            echo "<tr><td><b>$conta_quote</b> Euro di quote annuali + <b>$conta_ingressi</b> Euro di altre entrate - <b>$conta_spese</b> Euro di spese = <b><font color=\"red\"> $saldo_gas</b> Euro. GAS in rosso!!</font></td></tr> \n";
           }



           echo "</table> \n";
?>

			</td>
		</tr>
	</table>
<?php
echo "</form>\n";
if(count($vaidationItem) > 0)
{
	$validation = "[" . implode(", ", $vaidationItem) . "]";
	echo "<script>items = $validation;</script>";
}
?>
</body>
</html>
