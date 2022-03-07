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

if ($userId > 0) {
    $userData = getUserData($cnn, $userId);
} else {
    header("location: ../gestione/login.php");
    exit;
}
//debug($userData);
if (!$userData["fl_admin"] && !$userData["fl_contabile"]) {
//	doError("noaccess");
//	exit;
}
$nodeId = intval($_REQUEST["nodeId"]);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link href="../css/default.css" rel="STYLESHEET" type="text/css"/>
    <title>Content Management</title>
</head>
<body onload="window.focus();">


<table width="100%" border="0" cellspacing="0" cellpadding="5" style="text-align: center;">
    <tr>
        <td style="width: 10px;"><img src="../img/spacer.gif" width="10" height="1" style="border: none;"/></td>
        <td style="width: 150px;"><img src="../img/spacer.gif" width="150" height="1" style="border: none;"/></td>
        <td style="width: 10px;"><img src="../img/spacer.gif" width="10" height="1" style="border: none;"/></td>
        <td style="width: 100%;"><img src="../img/spacer.gif" width="1" height="1" style="border: none;"/></td>
        <td style="width: 10px;"><img src="../img/spacer.gif" width="10" height="1" style="border: none;"/></td>
    </tr>
    <tr>
        <td colspan="5"></td>
    </tr>
    <tr>
        <td class="titolo" colspan="5"></td>
    </tr>
    <tr>
        <td style="width: 10px;"><img src="../img/spacer.gif" width="10" height="1" style="border: none;"/></td>
        <td style="width: 150px;"><img src="../img/spacer.gif" width="150" height="1" style="border: none;"/></td>
        <td style="width: 10px;"><img src="../img/spacer.gif" width="10" height="1" style="border: none;"/></td>
        <td style="width: 100%;"><img src="../img/spacer.gif" width="1" height="1" style="border: none;"/></td>
        <td style="width: 10px;"><img src="../img/spacer.gif" width="10" height="1" style="border: none;"/></td>
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

// query per fotografare situazione contabile del ordine
                $sql_ordiniGAS = " select id_fornitore from ordini where id  = " . $nodeId . "";
                $resultordiniGAS = mysqli_query($cnn, $sql_ordiniGAS) or doError("sql_ordiniGAS", "Errore nell'esecuzione della query: " . $sql_ordiniGAS);
                while ($row = mysqli_fetch_assoc($resultordiniGAS)) {
                    $id_fornitore = $row["id_fornitore"];
                }
                mysqli_free_result($resultordiniGAS);
/// composizione della pagina solo nel caso che il ordine non sia relativo a speseGAS:
                if ($id_fornitore != 5) {
//USCITA = ordine
                    $conta_uscite = 0;
                    $diff = 0;
                    $sql_uscite = " select diff, importo from v_ordini where id  = " . $nodeId . "";
                    $result = mysqli_query($cnn, $sql_uscite) or doError("sql_uscite", "Errore nell'esecuzione della query: " . $sql_uscite);
                    echo "<table width=\"100%\"  border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"text-align: center;\" >\n";
                    echo "<hr noshade size='1' color='$leadcolor' style='dot'>";
                    echo "<td>&nbsp;</td> \n";
                    while ($row = mysqli_fetch_assoc($result)) {
                        $conta_uscite = round($row["importo"], 2);
                        $diff = round($row["diff"], 2);
                    }
                    mysqli_free_result($result);
                    echo "<td>&nbsp;</td> \n";
                    echo "</table>\n";
//ENTRATE
                    $conta_entrate = 0;
                    $sql_entrate = " select A.importo, B.nm_cognome from movimenti as A, users as B where A.id_gasista=B.id and A.id_ordine  = " . $nodeId;
                    $result = mysqli_query($cnn, $sql_entrate) or doError("sql_entrate", "Errore nell'esecuzione della query: " . $sql_entrate);
                    echo "<table width=\"100%\"  border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"text-align: center;\" >\n";
                    echo "<td>&nbsp;</td> \n";
                    echo "<hr noshade size='1' color='$leadcolor' style='dot'>";
                    echo "<tr>RIPARTIZIONE TRA I GASISTI</tr>\n";
                    echo "<hr noshade size='1' color='$leadcolor' style='dot'>";
                    echo "<tr><td colspan=5>IMPORTO </td><td colspan=5>NOME GASISTA</td></tr>\n";
                    while ($row = mysqli_fetch_assoc($result)) {
                        $importo = round($row["importo"], 2);
                        $nm_cognome = $row["nm_cognome"];
                        echo "<tr><td colspan=5>$importo </td><td colspan=5>$nm_cognome </td></td></tr> \n";
                        $conta_entrate = $conta_entrate + $importo;
                    }
                    mysqli_free_result($result);
                    echo "<td>&nbsp;</td> \n";
                    echo "</table>\n";
//TOTALI
                    $ts = date("Y-m-d");
                    echo "<table width=\"100%\"  border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"text-align: center;\" >\n";
                    echo "<td>&nbsp;</td> \n";
                    echo "<hr noshade size='1' color='$leadcolor' style='dot'>";
                    echo "<hr noshade size='1' color='$leadcolor' style='dot'>";
                    if ($diff > 0) {
                        echo "<tr><td colspan=5>PAGATI </td><td colspan=5>DAI GASISTI</td><td colspan=5> <b><font color=\"red\"> Conto Gas paga la differenza </font></b></td></tr>\n";
                        echo "<tr><td colspan=5>$conta_uscite Euro </td><td colspan=5>$conta_entrate Euro </td><td colspan=5><b><font color=\"red\">$diff Euro !!!</font></b></td></tr> \n";
                    } elseif ($diff < 0) {
                        echo "<tr><td colspan=5><b>PAGATI</b></td><td colspan=5><b>DAI GASISTI</b></td><td colspan=5> <b><font color=\"red\">Gasisti pagano troppo </font></b></td></tr>\n";
                        echo "<tr><td colspan=5><b>$conta_uscite Euro </b></td><td colspan=5><b>$conta_entrate Euro </b></td><td colspan=5><b><font color=\"red\">$diff Euro !!!</font></b></td></tr> \n";
                    } else {
                        echo "<tr><td colspan=5><b>PAGATI</b></td><td colspan=5><b>DAI GASISTI</b></td><td colspan=5> <b><font color=\"green\"> I CONTI TORNANO, DIFFERENZA DI </font></b></td></tr>\n";
                        echo "<tr><td colspan=5><b>$conta_uscite Euro </b></td><td colspan=5><b>$conta_entrate Euro</b></td><td colspan=5><b><font color=\"green\">$diff Euro</font></b></td></tr> \n";
                    }
                    echo "</table> \n";
                }
                ?>

                </td>
                </tr>
            </table>

</body>
</html>
