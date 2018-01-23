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


function doError($errorCode, $errorData = "", $inPage = FALSE, $displayData = FALSE)
{
	static $disableDoError = FALSE;
	$err = array();
	$err[] = array("errorCode" => $errorCode);
	if($errorData)
	{
		$err[] = array("errorData " => $errorData);
	}
	if (function_exists("debug_backtrace"))
	{
		$err[] = array("errorBacktrace " => debug_backtrace());
	}
	if(DEBUG_ENABLE)
	{
		debug($err);
		exit;
	}
	else
	{
		logWrite(print_r($err,TRUE), "main");
		
		if($disableDoError)
		{
			logWrite("doError disabled to prevent recursion", "debug");
			exit;
		}
		else
		{
			$disableDoError = TRUE;
			if (!headers_sent($filename, $linenum) && ($inPage == FALSE)) 
			{
				$base = $_SERVER["PHP_SELF"];
				$pos =  strrpos($base ,"/");
				if ($pos !== false)
				{
					$base = substr($base, 0, $pos);
					$pos =  strrpos($base ,"/");
					if ($pos !== false)
					{
						$base = substr($base, $pos + 1);
					}
				}
				if($base == "private")
				{
					$url = "../private/error.php?errorcode=" . $errorCode;
				}
				else
				{
					$url = "./errore.php?errorcode=" . $errorCode;
				}
				if($inPage || $displayData)
				{
					$url .= "&errMsg=" . urlencode($errorData);
				}
				header("location: $url");
				exit;
			}
			else
			{
				$err = $errorCode;
				logWrite(" headers already sent $filename $linenum", "debug");
				if($inPage)
				{
					$errMsg = $errorData;
				}
//				include("../include/error.php");
			}
		}
	}
}

function logWrite($msg, $logLevel = "main")
{
	if(is_array($msg))
	{
		$msg = print_r($msg, TRUE);
	}
	$ts = date("Y-m-d H:i:s");
	switch ($logLevel)
	{
		case  "sql":
		case  "debug":
		case  "main":
		case  "mail":
		case  "ws":
			$logFile = ERROR_LOG_DIR . "/" .$logLevel . "_" . date("Y-m-d") . ".log";
			break;
		default:
			echo "wrong logLevel: '$logLevel' $msg";
			exit;
	}
	$msg = str_replace("\n\n", "\n", $msg);
	$logText = "";
	$logText = "[$ts] <" . $_SERVER["REQUEST_URI"] . ">\n $msg \n";
	if ($fd = fopen ($logFile, "a+"))
	{
		if (flock($fd, LOCK_EX))
		{ // do an exclusive lock
			fwrite($fd, $logText);
			flock($fd, LOCK_UN); // release the lock
		}
		else
		{
			echo "Couldn't lock the file !";
			exit;
		}
		fclose($fd);
	}
}

function getDbConnection()
{
	$cnn = mysql_connect(DB_HOST, DB_USER, DB_PWD) or doError ("sql", "ERRORE NELLA CONNESSIONE AL DATABASE");
	mysql_select_db(DB_NAME, $cnn) or doError ("sql", "ERRORE NELL'APERTURA DEL DATABASE");
	return $cnn;
}

function debug($text)
{
	if(DEBUG_ENABLE)
	{
		if(is_array($text))
		{
			echo "<pre>";
			print_r($text);
			echo "</pre>";
		}
		else
		{
			echo "** $text **<br>\n";
		}
	}
}

function checkBasicChar($txt)
{
	$ok = TRUE;
	for($i = 0; $i < strlen($txt); $i++)
	{
		$c = substr($txt, $i, 1);
		if((ord($c) >= ord("0") && ord($c) <= ord("9")) || (ord($c) >= ord("a") && ord($c) <= ord("z")))
		{
			continue;
		}
		else
		{
			$ok = FALSE;
			break;
		}
	}
	return $ok;
}

function checkCleanChar($txt)
{
	$reschar = array("#","/","*",";","\\","'","\"");
	foreach ($reschar as $c)
	{
		if(strstr($txt, $c) !=  FALSE)
		{
			return FALSE;
		}
	}
	return TRUE;
}
//
function getHumanDate($isoDate){
	$humanDate = "00/00/0000";
	$isoDate = str_replace("-","/", $isoDate);
	$tmp = explode(" ", $isoDate);
	$tmp = explode("/", $tmp[0]);
	if(count($tmp)== 3){
		if(checkdate ($tmp[1], $tmp[2], $tmp[0])){
			$humanDate = $tmp[2] . "/" . $tmp[1] . "/" . $tmp[0];
		}
	}
	return $humanDate;
}
function getHumanDateHyphen($isoDate){
	$humanDate = "00-00-0000";
	$tmp = explode(" ", $isoDate);
	$tmp = explode("-", $tmp[0]);
	if(count($tmp) == 3){
		if(checkdate ($tmp[1], $tmp[2], $tmp[0])){
			$humanDate = $tmp[2] . "-" . $tmp[1] . "-" . $tmp[0];
		}
	}
	return $humanDate;
}
//
function getIsoDate($humanDate){
	$humanDate = str_replace("-","/", $humanDate);
	$isoDate = "0000/00/00";
	$tmp = explode("/", $humanDate);
	if(count($tmp) == 3){
		if(checkdate ($tmp[1], $tmp[0], $tmp[2])){
			$isoDate = $tmp[2] . "/" . $tmp[1] . "/" . $tmp[0];
		}
	}
	return $isoDate;
}
//
function getIsoTime($humanTime){
	$humanTime = str_replace(".",":", $humanTime);
	$isoTime = "00:00:00";
	$tmp = explode(":", $humanTime);
	if(count($tmp) == 3){
		if(($tmp[0] >= 0) && ($tmp[0] < 24) && ($tmp[1] >= 0) && ($tmp[1] < 60) && ($tmp[2] >= 0) && ($tmp[2] < 60)){
			$isoTime = $humanTime;
		}
	}
	if(count($tmp) == 2){
		if(($tmp[0] >= 0) && ($tmp[0] < 24) && ($tmp[1] >= 0) && ($tmp[1] < 60)){
			$isoTime = $humanTime . ":00";
		}
	}
	return $isoTime;
}
//       gethumaddatetime
function getHumanDateTime($humanDateTime){
	$isoDateTime = "00/00/0000 00:00:00";
	$tmp = explode(" ", $humanDateTime);
	if(count($tmp) == 2){
		$humanDate = getHumanDate($tmp[0]);
		$humanDateTime = $humanDate . " " . $tmp[1];
	}
	return $humanDateTime;
}
//
function getIsoDateTime($humanDateTime){
	$isoDateTime = "0000/00/00 00:00:00";
	$tmp = explode(" ", $humanDateTime);
	if(count($tmp) == 2){
		$isoDate = getIsoDate($tmp[0]);
		$isoTime = getIsoTime($tmp[1]);
		$isoDateTime = $isoDate . " " . $isoTime;
	}else{
		$isoDate = getIsoDate($humanDateTime);
		$isoDateTime = $isoDate . " 00:00:00";
	}
	return $isoDateTime;
}

function getHumanPagesBar($recordCount, $pageSize, $absPage, $extra = "")
{
	//debugMsg($recordCount . " " . $pageSize . " "  . $absPage);
	$exclude = array ("absPage");
	$theurl = getCurrUrl($exclude);


	$mod = $recordCount % $pageSize;
	$pageCount = ($recordCount - $mod) / $pageSize;
	if ($mod != 0){
		$pageCount++;
	}
	$end = $pageCount;
	
	if($pageCount > 10) 
	{
		$start = max(1, $absPage - 5);
		$end = min($pageCount, $absPage + 5);
	}
	
	$navText = "";

	if($start > 1 )
	{
		$navText .= "<a $extra href=\"" . $theurl . "&absPage=1\">&lt;&lt;</a>&nbsp;&nbsp;&nbsp;";
	}
	
	for ($i=$start; $i<=$end; $i++){
		if ($i != $absPage){
			$navText .= "<a $extra href=\"" . $theurl . "&absPage=" . $i . "\">" . $i . "</a>";
		} else {
			$navText .= "<span class=\"selected\">$i</span>";
		}
		$navText .= "&nbsp;&nbsp;&nbsp;";
		/*if (($i % 20) != 0) {
			$navText .= "&nbsp;&nbsp;&nbsp;";
		} else {
			$navText .= "<br>";
		}*/
	}
	
	if($end < $pageCount )
	{
		$navText .= "&nbsp;&nbsp;&nbsp;<a $extra href=\"" . $theurl . "&absPage=" . $pageCount . "\">&gt;&gt;</a>";
	}

	return $navText;
}
function getCurrUrl($exclude = array ()){
	reset ($_GET);
	$tmp = "";
	while(list($key, $val) = each ($_GET)){
		if (!in_array($key, $exclude) && ($val != "")){
			$tmp = $tmp . "&" . urlencode($key) . "=" . urlencode($val);
		}
	}
	if (strlen($tmp)> 0){
		$tmp = substr($tmp, 1);
	}
	$theurl = $_SERVER["SCRIPT_NAME"] . "?" . $tmp;
	return $theurl;
}
function getCurrParam($exclude = array ()){
	reset ($_GET);
	$tmp = "";
	while(list($key, $val) = each ($_GET)){
		if (!in_array($key, $exclude) && ($val != "")){
			$tmp = $tmp . "&" . urlencode($key) . "=" . urlencode($val);
		}
	}
	if (strlen($tmp)> 0){
		$tmp = substr($tmp, 1);
	}
	return $tmp;
}
function getOrderByParam($from)
{
	$orderCrit = array();
	if (array_key_exists("ord", $from))
	{
		if(checkCleanChar($from["ord"]))
		{
			$orderCrit["order"] = $from["ord"];
		}
	}
	if (array_key_exists("dir", $from))
	{
		$orderCrit["direction"] = "asc";	
		if($from["dir"] == "desc")
		{
			$orderCrit["direction"] = "desc";
		}
	}
	return $orderCrit;
}

function getOrderByCriteria(&$from, &$definition, &$orderCrit)
{
	$orderBy = "";
	$orderCrit = getOrderByParam($from);
	
	if(strlen($orderCrit["order"]) == 0)
	{
		$orderBy = $definition["order"];
	}
	else
	{
		//$orderBy = "lower(" . $orderCrit["order"] . ") " . $orderCrit["direction"];
		$orderBy = $orderCrit["order"] . " " . $orderCrit["direction"];
	}
	return $orderBy;
}
function generateTmpKey($len = 20)
{
	$char = array("2","3","4","5","6","7","8","9","a","b","c","d","e","f","g","h","j","m","n","p","q","r","s","t","u","v","w","x","y","z");
	$pass = "";
	for($i = 0; $i < $len; $i++)
	{
		$pass .= $char[rand(0, count($char) - 1)];
	}
	return $pass;
}

function countRowsFromTable(&$cnn, $tableName, $whereCriteria = "", $pageSize = 0, $absPage = 0)
{
	$blocksCount = 0;
	$sql = "SELECT count(*) FROM `$tableName`";
	if(strlen($whereCriteria) > 0){
		$sql .= " WHERE $whereCriteria";
	}
	$result = mysql_query($sql, $cnn) or doError("sql","Errore nell'esecuzione della query: " . $sql);
	while($row = mysql_fetch_array($result))
	{
		$blocksCount = $row[0];
	}
	mysql_free_result($result);
	return $blocksCount;
}
//
function getRowsFromTable(&$cnn, $tableName, $whereCriteria = "", $orderCriteria = "", $pageSize = 0, $absPage = 0)
{
	$outData = array();
	$base = "SELECT * FROM `$tableName`";// ORDER BY POSITION";
	if(strlen($whereCriteria) > 0){
		$base .= " WHERE $whereCriteria";
	}
	$order = "";
	$limit = "";
	if(strlen($orderCriteria) > 0){
		$order .= " ORDER BY $orderCriteria";
	}
	if(($absPage > 0) && ($pageSize > 0)){
		$offset = ($absPage - 1) * $pageSize;
		$limit = " LIMIT $offset, $pageSize";
	}
	$sql = $base . $order . $limit;
	//debugMsg($orderCriteria);
	//debugMsg($sql);
	$result=mysql_query($sql, $cnn) or doError ("sql","Errore nell'esecuzione della query: " . $sql);
	while ($row = mysql_fetch_assoc($result)){
		$outData[] = $row;
	}
	mysql_free_result($result);

	return $outData;
}

function getCmAdminBlockIcons($family, $extra, $itemId = 0, $winname = "block", $winBehaviour = ""){
	$separator = "&nbsp;&nbsp;\n";
	$icone = "";
	switch ($family){
		case "edit":
			$p = "$extra&target=$target&nodeId=" . $itemId;
			$pp = $p;
			if ($winBehaviour != "") {
				if ($winBehaviour == "refresh") {
					$pp .= "&winBehaviour=backRefresh";
				}
			}
			$icone .= "<div class=\"cmAdminBlockIcon\"><a href=\"javascript:popUp('../gestione/edit.php?$pp&action=edit','$winname','" . WINDOWS_PARAM . "')\"><img src='../img/e_edit.gif' title='Modifica dato' width='16' height='16' border='0'></a></div>$separator";
			
			$icone .= "<div class=\"cmAdminBlockIcon\"><a href=\"javascript:popUp('../gestione/admin.php?$pp&action=delete','$winname','" . WINDOWS_PARAM . "')\" onClick=\"return confirm('Eliminazione dato');\"><img src='../img/e_cancel.gif' title='Cancella dato' width='16' height='16' border='0'></a></div>";
			if ($extra=="param1=users" && $itemId>0) {
			 $icone .= "<div class=\"cmAdminBlockIcon\"><a href=\"javascript:popUp('../gestione/photo_gasista.php?$pp&action=photo','$winname','" . WINDOWS_PARAM . "')\"><img src='../img/e_photo.gif' title='Situazione contabile' width='16' height='16' border='0'></a></div>";
			}
			if ($extra=="param1=v_ordini" || $extra=="param1=ordini") {
			 $icone .= "<div class=\"cmAdminBlockIcon\"><a href=\"javascript:popUp('../gestione/photo_ordini.php?$pp&action=photo','$winname','" . WINDOWS_PARAM . "')\"><img src='../img/e_photo.gif' title='Situazione contabile' width='16' height='16' border='0'></a></div>";
			}
			
			break;
		case "view_only":
			$p = "$extra&target=$target&nodeId=" . $itemId;
			$pp = $p;
			if ($winBehaviour != "") {
				if ($winBehaviour == "refresh") {
					$pp .= "&winBehaviour=backRefresh";
				}
			}
			$icone .= "<div class=\"cmAdminBlockIcon\"><a href=\"javascript:popUp('../gestione/edit.php?$pp&action=view','$winname','" . WINDOWS_PARAM . "')\"><img src='../img/e_edit.gif' title='Modifica dato' width='16' height='16' border='0'></a></div>$separator";
                        if ($extra=="param1=users" && $itemId>0) {
			 $icone .= "<div class=\"cmAdminBlockIcon\"><a href=\"javascript:popUp('../gestione/photo_gasista.php?$pp&action=photo','$winname','" . WINDOWS_PARAM . "')\"><img src='../img/e_photo.gif' title='Situazione contabile' width='16' height='16' border='0'></a></div>";
			}
			if ($extra=="param1=v_ordini" || $extra=="param1=ordini") {
			 $icone .= "<div class=\"cmAdminBlockIcon\"><a href=\"javascript:popUp('../gestione/photo_ordini.php?$pp&action=photo','$winname','" . WINDOWS_PARAM . "')\"><img src='../img/e_photo.gif' title='Situazione contabile' width='16' height='16' border='0'></a></div>";
			}
			break;
		case "edit_only":
			$p = "$extra&target=$target&nodeId=" . $itemId;
			$pp = $p;
			if ($winBehaviour != "") {
				if ($winBehaviour == "refresh") {
					$pp .= "&winBehaviour=backRefresh";
				}
			}
			$icone .= "<div class=\"cmAdminBlockIcon\"><a href=\"javascript:popUp('../gestione/edit.php?$pp&action=edit','$winname','" . WINDOWS_PARAM . "')\"><img src='../img/e_edit.gif' title='Modifica dato' width='16' height='16' border='0'></a></div>$separator";
			if ($extra=="param1=users" && $itemId>0) {
				 $icone .= "<div class=\"cmAdminBlockIcon\"><a href=\"javascript:popUp('../gestione/photo_gasista.php?$pp&action=photo','$winname','" . WINDOWS_PARAM . "')\"><img src='../img/e_photo.gif' title='Situazione contabile' width='16' height='16' border='0'></a></div>";
			}
if ($extra=="param1=v_ordini" || $extra=="param1=ordini") {
			 $icone .= "<div class=\"cmAdminBlockIcon\"><a href=\"javascript:popUp('../gestione/photo_ordini.php?$pp&action=photo','$winname','" . WINDOWS_PARAM . "')\"><img src='../img/e_photo.gif' title='Situazione contabile' width='16' height='16' border='0'></a></div>";
			}
			break;
		case "add":
			$p = "$extra&target=$target&nodeId=" . $itemId;
			$pp = $p;
			if ($winBehaviour != "") {
				if ($winBehaviour == "refresh") {
					$pp .= "&winBehaviour=backRefresh";
				}
			}
			$icone .= "<div class=\"cmAdminBlockIcon\"><a href=\"javascript:popUp('../gestione/edit.php?$pp&action=new','$winname','" . WINDOWS_PARAM . "')\"><img src='../img/e_add.gif' title='Aggiungi nuovo' width='16' height='16' border='0'></a></div>$separator";
			
			break;
	}
	return $icone;
}
//
function br2nl($text)
{
    /** @noinspection PhpDeprecationInspection */
    return @eregi_replace("<[ ]*br[ ]*[/]{0,1}[ ]*>",  "\n", $text);
}
//
function getAbstract($text, $max = 0)
{
	$text = br2nl($text);
	if($max == 0)
	{
		$max = ABSTRACT_LIMIT;
	}
	if (strlen($text) > $max)
	{
		return nl2br((substr($text , 0 , 97) . "..."));
	}
	else
	{
		return nl2br($text);
	}
}
function getArrayFromList(&$input, $name)
{
	$dataArray = array();
	foreach($input as $k => $v)
	{
		if (substr($k, 0, strlen($name)) . "_"  == $name . "_")
		{
			//$index = intval(substr($k, strlen($postName) + 1));
			$dataArray[] = $v;
		}
	}
	return $dataArray;
}
?>
