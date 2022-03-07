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

$time_start = microtime(true);
include("../conf/conf.php");
include("../lib/sessions.php");
include("../lib/users.php");
include("../lib/util.php");
include("../lib/htmlgen.php");
include("../lib/guinness.php");
include("../lib/gas.php");

$cnn = getDbConnection();
$userId = getUserId($cnn);

if ($userId > 0) {
    $userData = getUserData($cnn, $userId);
} else {
    header("location: ../gestione/login.php");
    exit;
}
if (!$userData["fl_admin"] && !$userData["fl_contabile"]) {
    doError("noaccess");
    exit;
}
$fromId = intval($_REQUEST["fromId"]);

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

$sql_gasisti = "select id,nm_nome,nm_cognome,ds_email,ds_telefono,indirizzo_1,indirizzo_2,username,password,fl_admin,fl_contabile,fl_attivo,dt_ins,dt_agg from users where fl_attivo = 1 and id >= $fromId order by id desc";
$result = mysqli_query($cnn, $sql_gasisti) or doError("sql_gasisti", "Errore nell'esecuzione della query: " . $sql_gasisti);
$gasisti = array();
while ($row = mysqli_fetch_assoc($result)) {
    $gasisti[] = $row;
}
mysqli_free_result($result);

foreach ($gasisti as $gasista) {
    $nodeId = $gasista['id'];
    $html = "";
    // query per fotografare situazione contabile del gasista
    //USCITE
    $conta_uscite = 0;
    $sql_uscite = " select sum(B.importo) uscite from movimenti as B, ordini as C, fornitori as D  where C.id=B.id_ordine and C.id_fornitore=D.id and id_gasista  = " . $nodeId . " order by C.dt_ordine desc";
    $result = mysqli_query($cnn, $sql_uscite) or doError("sql_uscite", "Errore nell'esecuzione della query: " . $sql_uscite);

    while ($row = mysqli_fetch_assoc($result)) {
        $conta_uscite = $row["uscite"];
    }
    $conta_uscite = round($conta_uscite, 2);
    mysqli_free_result($result);
    $conta_entrate = 0;
    $sql_entrate = " select sum(A.importo) entrate from versamenti as A, causali as B where A.id_causale=B.id and id_gasista  = " . $nodeId . " order by A.dt_versamento desc";
    $result = mysqli_query($cnn, $sql_entrate) or doError("sql_entrate", "Errore nell'esecuzione della query: " . $sql_entrate);
    while ($row = mysqli_fetch_assoc($result)) {
        $conta_entrate = $row["entrate"];
    }
    $conta_entrate = round($conta_entrate, 2);
    mysqli_free_result($result);
    //TOTALI
    $euro_tot = round(($conta_entrate - $conta_uscite), 2);
    if ($euro_tot <= -60.0) {
        $ts = date("Y-m-d");
        $tsUmano = date("d-m-Y");

        $html .= "<p>";
        $html .= "Gentile ".$gasista['nm_nome'].",<br/> \n";
        $html .= "il nostro fantasmagorico sistema di controllo ha verificato che il tuo conto personale ha superato la soglia critica: sei in debito verso il GAS di €".abs($euro_tot)."!<br/><br/>\n\n";

        $html .= "Tenere il conto in attivo è fondamentale per consentire al GAS di funzionare nel modo migliore e più corretto, pagando puntualmente i fornitori.<br/><br/>\n\n";

        $html .= "Ti suggeriamo quindi di provvedere al più presto a rifornire il conto comune, effettuando un bonifico su queste coordinate: IT38H0501801600000016673717 - intestato a Gas Del Sole<br/><br/>\n\n";

        $html .= "Ricordati di segnalarci il tuo bonifico, compilando questo form: <br/>\n";
        $html .= "https://docs.google.com/spreadsheet/viewform?formkey=dGNzZEJJRW5TbkZWVE9xd2ViNTRVYmc6MQ#gid=8<br/><br/>\n";

        $html .= "Grazie per la tua collaborazione!<br/>\n";
        $html .= "Buona giornata<br/><p/>\n";
        require_once("../lib/PHPMailer_v5.1/class.phpmailer.php");
        //		require_once("../lib/PHPMailer/class.smtp.php");
//require_once("../lib/PHPMailer/class.phpmailer.php");
        //			require_once("../lib/PHPMailer/class.smtp.php");

        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->CharSet = 'utf-8';
        $mail->Mailer = "smtp";
        $mail->Host = "smtp.sendgrid.net";
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "tls";
        $mail->SMTPDebug = 0;
        $mail->Port = 587;
        $mail->Username = EMAIL_SENDER_USER;
        $mail->Password = EMAIL_SENDER_PWD;
        $mail->From = EMAIL_SENDER;
        $mail->FromName = EMAIL_SENDER_NAME;
        $mail->AddReplyTo(EMAIL_REPLY_TO, EMAIL_REPLY_TO_NAME);

        if ($gasista['ds_email'] != "-") {

            $arrMail = explode(';', $gasista['ds_email']);
            // $arrMail = explode(';', 'amit.moravchick@gmail.com');
            if (!empty($arrMail)) {
                foreach ($arrMail as $indirizzo) {
                    $mail->AddAddress($indirizzo);
                }
            }
        } else {

        }

        $mail->AddCC(EMAIL_CC);
        $mail->AddBCC('amit.moravchick@gmail.com');
        $mail->AddBCC('donewitch@gmail.com');

        $mail->IsHTML(true);
        $mail->Subject = "situazione contabile al $tsUmano";
        $mail->Body = $html;

        if ($mail->Send()) {

            echo "Mailer OK ";
        } else {
            echo "Mailer Error: " . $mail->ErrorInfo;
        }
        logWrite('partial execution time in seconds: ' . (microtime(true) - $time_start), 'mail');
    }
}
logWrite('Total execution time in seconds: ' . (microtime(true) - $time_start), 'mail');

//exit;