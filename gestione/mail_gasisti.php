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

if ($userId > 0) {
    $userData = getUserData($cnn, $userId);
} else {
    header("location: ../gestione/login.php");
    exit;
}
//debug($userData);
if (!$userData["fl_admin"] && !$userData["fl_contabile"]) {
    doError("noaccess");
    exit;
}
$action = $_REQUEST["action"];
$nodeId = intval($_REQUEST["nodeId"]);
$param1 = "";
if (array_key_exists("param1", $_REQUEST)) {
    $param1 = $_REQUEST["param1"];
}
$formReadOnly = FALSE;
$useDefinition = TRUE;
$ini = "../conf/gestione.ini.php";

$baseUrl = "../gestione/index.php";
///////////////////////////////////////////////////////////////////////////////
switch ($param1) {
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

$sql_gasisti = "select id,nm_nome,nm_cognome,ds_email,ds_telefono,indirizzo_1,indirizzo_2,username,password,fl_admin,fl_contabile,fl_attivo,dt_ins,dt_agg from users where fl_attivo = 1";
$result = mysql_query($sql_gasisti, $cnn) or doError("sql_gasisti", "Errore nell'esecuzione della query: " . $sql_gasisti);
$gasisti = array();
while ($row = mysql_fetch_assoc($result)) {
    $gasisti[] = $row;
}
mysql_free_result($result);

foreach ($gasisti as $gasista) {
//debug($gasista);	
    $nodeId = $gasista['id'];
//debug($nodeId);
    $html = "";
    // query per fotografare situazione contabile del gasista
    //USCITE
    $conta_uscite = 0;
    $sql_uscite = " select B.importo, D.nm_nome, C.ds_nota, C.dt_pagamento from movimenti as B, pagamenti as C, fornitori as D  where C.id=B.id_pagamento and C.id_fornitore=D.id and id_gasista  = " . $nodeId . " order by C.dt_pagamento desc";
//debug($sql_uscite);	           
    $result = mysql_query($sql_uscite, $cnn) or doError("sql_uscite", "Errore nell'esecuzione della query: " . $sql_uscite);

    $html .= "<table width=\"100%\"  border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"text-align: center;\" >\n";
    $html .= "<hr noshade size='1' color='$leadcolor' style='dot'>";
    $html .= "<td>&nbsp;</td> \n";
    $html .= "<tr><b>ACQUISTI</b></tr>\n";
    $html .= "<hr noshade size='1' color='$leadcolor' style='dot'>";
    $html .= "<tr><td colspan=5>IMPORTO </td><td colspan=5>NOME </td><td colspan=5>DESCRIZIONE</td><td colspan=5>DATA PAGAMENTO </td> </tr>\n";
    while ($row = mysql_fetch_assoc($result)) {
        //debug($row);
        $importo = $row["importo"];
        $nm_nome = $row["nm_nome"];
        $ds_nota = $row["ds_nota"];
        $dt_pagamento = $row["dt_pagamento"];
        $html .= "<tr> <td colspan=5>$importo </td><td colspan=5>$nm_nome </td><td colspan=5>$ds_nota </td><td colspan=5>$dt_pagamento </td></tr> \n";
        $conta_uscite = $conta_uscite + $importo;
    }
    mysql_free_result($result);
    $html .= "<td>&nbsp;</td> \n";
    $html .= "</table>\n";
    //ENTRATE
    $conta_entrate = 0;
    $sql_entrate = " select A.importo, A.dt_versamento,B.ds_causale from versamenti as A, causali as B where A.id_causale=B.id and id_gasista  = " . $nodeId . " order by A.dt_versamento desc";
    $result = mysql_query($sql_entrate, $cnn) or doError("sql_entrate", "Errore nell'esecuzione della query: " . $sql_entrate);
    $html .= "<table width=\"100%\"  border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"text-align: center;\" >\n";
    $html .= "<td>&nbsp;</td> \n";
    $html .= "<hr noshade size='1' color='$leadcolor' style='dot'>";
    $html .= "<tr><b>BONIFICI</b></tr>\n";
    $html .= "<hr noshade size='1' color='$leadcolor' style='dot'>";
    $html .= "<tr><td colspan=5>IMPORTO </td><td colspan=5>DESCRIZIONE</td><td colspan=5>DATA VERSAMENTO </td> </tr>\n";
    while ($row = mysql_fetch_assoc($result)) {
        $importo = $row["importo"];
        $ds_causale = $row["ds_causale"];
        $dt_versamento = $row["dt_versamento"];
        $html .= "<tr><td colspan=5>$importo </td><td colspan=5>$ds_causale </td><td colspan=5>$dt_versamento </td></tr> \n";
        $conta_entrate = $conta_entrate + $importo;
    }
    mysql_free_result($result);
    $html .= "<td>&nbsp;</td> \n";
    $html .= "</table>\n";
    //TOTALI
    $euro_tot = $conta_entrate - $conta_uscite;
    $ts = date("Y-m-d");
    $tsUmano = date("d-m-Y");
    $html .= "<table width=\"100%\"  border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"text-align: center;\" >\n";
    $html .= "<td>&nbsp;</td> \n";
    $html .= "<hr noshade size='1' color='$leadcolor' style='dot'>";
    $html .= "<hr noshade size='1' color='$leadcolor' style='dot'>";
    if ($euro_tot >= 0) {
        $html .= "<tr><td colspan=5><b> ENTRATI</b></td><td colspan=5><b>USCITI</b></td><td colspan=5> <b><font color=\"green\">SALDO AL $ts</font></b></td></tr>\n";
        $html .= "<tr><td colspan=5><b>$conta_entrate Euro </b></td><td colspan=5><b>$conta_uscite Euro </b></td><td colspan=5><b><font color=\"green\">$euro_tot Euro</font></b></td></tr> \n";
    } else {
        $html .= "<tr><td colspan=5><b> ENTRATI</b></td><td colspan=5><b>USCITI</b></td><td colspan=5> <b><font color=\"red\">SALDO AL $ts</font></b></td></tr>\n";
        $html .= "<tr><td colspan=5><b>$conta_entrate Euro </b></td><td colspan=5><b>$conta_uscite Euro </b></td><td colspan=5><b><font color=\"red\">$euro_tot Euro</font></b></td></tr> \n";
    }
    $html .= "</table> \n";
    $html = "<hr/>a: " . $gasista['nm_nome'] . " " . $gasista['nm_cognome'] . "\nall'indirizzo: " . $gasista['ds_email'] . "\n\n" . $html;
    echo $html;


    require_once("../lib/PHPMailer_v5.1/class.phpmailer.php");
    //		require_once("../lib/PHPMailer/class.smtp.php");
//require_once("../lib/PHPMailer/class.phpmailer.php");
    //			require_once("../lib/PHPMailer/class.smtp.php");

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->CharSet = 'utf-8';
    $mail->Mailer = "smtp";
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "tls";
    $mail->SMTPDebug = 0;
    $mail->Port = 25;
    $mail->Username = EMAIL_SENDER_USER;
    $mail->Password = EMAIL_SENDER_PWD;
    $mail->From = EMAIL_SENDER;
    $mail->FromName = EMAIL_SENDER_NAME;
    $mail->AddReplyTo(EMAIL_REPLY_TO, EMAIL_REPLY_TO_NAME);

    if ($gasista['ds_email'] != "-") {

        $arrMail = explode(';', $gasista['ds_email']);
        if (!empty($arrMail)) {
            foreach ($arrMail as $indirizzo) {
                $mail->AddAddress($indirizzo);
            }
        }
    } else {

    }

    $mail->AddAddress(EMAIL_CC);

    $mail->IsHTML(true);
    $mail->Subject = "situazione contabile al $tsUmano";
    $mail->Body = $html;

    if ($mail->Send()) {

        echo "Mailer OK ";
    } else {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }


}


//exit;