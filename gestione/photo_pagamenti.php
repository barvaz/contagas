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
		<title>Content Management</title>
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
}
// query per fotografare situazione contabile del pagamento  
           $sql_pagamentiGAS = " select id_fornitore from pagamenti where id  = " . $nodeId . "";
                $resultpagamentiGAS= mysql_query($sql_pagamentiGAS, $cnn) or doError("sql_pagamentiGAS","Errore nell'esecuzione della query: " . $sql_pagamentiGAS);
                while ($row = mysql_fetch_assoc($resultpagamentiGAS)){
                 $id_fornitore=$row["id_fornitore"];
                }
                mysql_free_result($resultpagamentiGAS);
/// composizione della pagina solo nel caso che il pagamento non sia relativo a speseGAS:
if ($id_fornitore != 5)
{
//USCITA = pagamento
           $conta_uscite=0;
           $sql_uscite = " select importo from pagamenti where id  = " . $nodeId . "";
                $result = mysql_query($sql_uscite, $cnn) or doError("sql_uscite","Errore nell'esecuzione della query: " . $sql_uscite);
           echo "<table width=\"100%\"  border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"text-align: center;\" >\n";
           echo "<hr noshade size='1' color='$leadcolor' style='dot'>";
           echo "<td>&nbsp;</td> \n";
//         echo "<tr>Bonifico a Fornitore</tr>\n";
//           echo "<hr noshade size='1' color='$leadcolor' style='dot'>";
//           echo "<tr><td colspan=5>IMPORTO </td></tr>\n";
                while ($row = mysql_fetch_assoc($result)){
                 $importo=$row["importo"];
//               echo "<tr> <td colspan=5>$importo </td></tr> \n";
                 $conta_uscite=$conta_uscite+$importo;
                }
                mysql_free_result($result);
           echo "<td>&nbsp;</td> \n";
           echo "</table>\n";
//ENTRATE
           $conta_entrate=0;
           $sql_entrate = " select A.importo, B.nm_cognome from movimenti as A, users as B where A.id_gasista=B.id and A.id_pagamento  = " . $nodeId;
                $result = mysql_query($sql_entrate, $cnn) or doError("sql_entrate","Errore nell'esecuzione della query: " . $sql_entrate);
           echo "<table width=\"100%\"  border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"text-align: center;\" >\n";
           echo "<td>&nbsp;</td> \n";
           echo "<hr noshade size='1' color='$leadcolor' style='dot'>";
           echo "<tr>RIPARTIZIONE TRA I GASISTI</tr>\n";
           echo "<hr noshade size='1' color='$leadcolor' style='dot'>";
           echo "<tr><td colspan=5>IMPORTO </td><td colspan=5>NOME GASISTA</td></tr>\n";
                while ($row = mysql_fetch_assoc($result)){
                 $importo=$row["importo"];
                 $nm_cognome=$row["nm_cognome"];
                 echo "<tr><td colspan=5>$importo </td><td colspan=5>$nm_cognome </td></td></tr> \n";
                 $conta_entrate=$conta_entrate+$importo;
                }
                mysql_free_result($result);
           echo "<td>&nbsp;</td> \n";
           echo "</table>\n";
//TOTALI
$euro_tot=$conta_entrate-$conta_uscite;
$ts = date("Y-m-d");
           echo "<table width=\"100%\"  border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"text-align: center;\" >\n";
           echo "<td>&nbsp;</td> \n";
           echo "<hr noshade size='1' color='$leadcolor' style='dot'>";
           echo "<hr noshade size='1' color='$leadcolor' style='dot'>";
           if($euro_tot < 0)
           {
            echo "<tr><td colspan=5> BONIFICO A FORNITORE </td><td colspan=5>DAI GASISTI</td><td colspan=5> <b><font color=\"red\"> MANCANO </font></b></td></tr>\n";
            echo "<tr><td colspan=5>$conta_uscite Euro </td><td colspan=5>$conta_entrate Euro </td><td colspan=5><b><font color=\"red\">$euro_tot Euro !!!</font></b></td></tr> \n";
           }elseif( $euro_tot > 0){
            echo "<tr><td colspan=5><b>PAGATI</b></td><td colspan=5><b>DAI GASISTI</b></td><td colspan=5> <b><font color=\"red\">TROPPA GRAZIA S.ANTONIO, AVANZANO </font></b></td></tr>\n";
            echo "<tr><td colspan=5><b>$conta_uscite Euro </b></td><td colspan=5><b>$conta_entrate Euro </b></td><td colspan=5><b><font color=\"red\">$euro_tot Euro !!!</font></b></td></tr> \n";
           }else{
            echo "<tr><td colspan=5><b>PAGATI</b></td><td colspan=5><b>DAI GASISTI</b></td><td colspan=5> <b><font color=\"green\"> I CONTI TORNANO, DIFFERENZA DI </font></b></td></tr>\n";
            echo "<tr><td colspan=5><b>$conta_uscite Euro </b></td><td colspan=5><b>$conta_entrate Euro</b></td><td colspan=5><b><font color=\"green\">$euro_tot Euro</font></b></td></tr> \n";
           }
           echo "</table> \n";
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
