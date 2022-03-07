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
if(!$userData["fl_admin"])
{
    doError("noaccess");
    exit;
}
$curDate = date("Y-m-d H:i:s");
$description = 'quota annuale ' . date("Y");
//insert new entry in ordini
$ini = "../conf/gestione.ini.php";
$definition = getDefinition($cnn, $ini, 'ordini');
$pagamData=array();
$pagamData['id_fornitore'] = 4; // spese GAS
$pagamData['importo'] = 0; // to update after
$pagamData['id_causale'] = 2; // quote annuali
$pagamData['ds_nota'] = $description; // quote annuali
$pagamData['dt_ordine'] = date('d-m-Y');
$otherValues["dt_ins"] = $curDate;
$otherValues["dt_agg"] = $curDate;
$newValues = setValues($definition, "", $pagamData, true);

mysqli_begin_transaction($cnn); // transaction begins

$sql = getInsertQuery($definition["table"], $newValues, $otherValues, $excludeValues);//array("dt_ins","dt_agg"));
$result = mysqli_query($cnn, $sql) or doError("sql_gasisti","Errore nell'esecuzione della query: " . $sql);
$pagamId = mysqli_insert_id($cnn);

$sql_gasisti = "select id,nm_nome,nm_cognome,ds_email,ds_telefono,indirizzo_1,indirizzo_2,username,password,fl_admin,fl_contabile,fl_attivo,dt_ins,dt_agg from users where fl_attivo = 1 and id > 0";
$result = mysqli_query($cnn, $sql_gasisti) or doError("sql_gasisti","Errore nell'esecuzione della query: " . $sql_gasisti);
$gasisti = array();
while ($row = mysqli_fetch_assoc($result)){
    $gasisti[] = $row;
}
//mysql_free_result($result);
$totaleQuote = 0;
foreach($gasisti as $gasista){
    $nodeId = $gasista['id'];
    $definition = getDefinition($cnn, $ini, 'movimenti');
    $movData=array();
    $movData['id_gasista'] = $nodeId;
    $movData['importo'] = QUOTA_ANNUALE; // quota singola
    $movData['id_ordine'] = $pagamId; // quote annuali
    $movData['id_autore'] = 0; // utente GAS
    $movData['ds_nota'] = $description; // quote annuali

    $newValues = setValues($definition, "", $movData, true);

    $sql = getInsertQuery($definition["table"], $newValues, $otherValues, $excludeValues);//array("dt_ins","dt_agg"));
    $result = mysqli_query($cnn, $sql) or mysqli_rollback($cnn);
    $totaleQuote += QUOTA_ANNUALE;
}

$pagamData['importo'] = $totaleQuote; // new import
$definition = getDefinition($cnn, $ini, 'ordini');
$newValues = setValues($definition, "", $pagamData, true);
$sql = getUpdateQuery($definition["table"], $newValues, $otherValues, $excludeValues);//array("dt_ins","dt_agg"));
$sql .= " WHERE " . $definition["key"] . " = $pagamId";
debug($sql);
$result = mysqli_query($cnn, $sql) or mysqli_rollback($cnn);
mysqli_commit($cnn);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<?php
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
