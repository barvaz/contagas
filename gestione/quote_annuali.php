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

function rollback(){
    mysql_query("ROLLBACK"); // transaction rolls back
    echo "transaction rolled back";
    exit;
}
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
//insert new entry in pagamenti
$ini = "../conf/gestione.ini.php";
$definition = getDefinition($cnn, $ini, 'pagamenti');
$pagamData=array();
$pagamData['id_fornitore'] = 4; // spese GAS
$pagamData['importo'] = 0; // to update after
$pagamData['id_causale'] = 2; // quote annuali
$pagamData['ds_nota'] = $description; // quote annuali
$pagamData['dt_pagamento'] = date('d-m-Y');
$otherValues["dt_ins"] = $curDate;
$otherValues["dt_agg"] = $curDate;
$newValues = setValues($definition, "", $pagamData, true);

mysql_query("BEGIN TRANSACTION"); // transaction begins

$sql = getInsertQuery($definition["table"], $newValues, $otherValues, $excludeValues);//array("dt_ins","dt_agg"));
$result = mysql_query($sql, $cnn) or doError("sql_gasisti","Errore nell'esecuzione della query: " . $sql);
$pagamId = mysql_insert_id();

$sql_gasisti = "select id,nm_nome,nm_cognome,ds_email,ds_telefono,indirizzo_1,indirizzo_2,username,password,fl_admin,fl_contabile,fl_attivo,dt_ins,dt_agg from users where fl_attivo = 1";
$result = mysql_query($sql_gasisti, $cnn) or doError("sql_gasisti","Errore nell'esecuzione della query: " . $sql_gasisti);
$gasisti = array();
while ($row = mysql_fetch_assoc($result)){
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
    $movData['id_pagamento'] = $pagamId; // quote annuali
    $movData['id_autore'] = 0; // utente GAS
    $movData['ds_nota'] = $description; // quote annuali

    $newValues = setValues($definition, "", $movData, true);

    $sql = getInsertQuery($definition["table"], $newValues, $otherValues, $excludeValues);//array("dt_ins","dt_agg"));
    $result = mysql_query($sql, $cnn) or rollback();
    $totaleQuote += QUOTA_ANNUALE;
}

$pagamData['importo'] = $totaleQuote; // new import
$definition = getDefinition($cnn, $ini, 'pagamenti');
$newValues = setValues($definition, "", $pagamData, true);
$sql = getUpdateQuery($definition["table"], $newValues, $otherValues, $excludeValues);//array("dt_ins","dt_agg"));
$sql .= " WHERE " . $definition["key"] . " = $pagamId";
debug($sql);
$result = mysql_query($sql, $cnn) or rollback();
mysql_query("COMMIT");

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