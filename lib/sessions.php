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


function generateSessionId()
{
	$sessionId = "";
	$sessionId = md5($_SERVER["REMOTE_ADDR"]);
	$sessionId .= md5($_SERVER["HTTP_USER_AGENT"]);
	$sessionId .= md5($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
	$sessionId .= md5(time());
 	$sessionId .= md5(rand(0, 65535));
	return $sessionId;
}
//
function startSession($cnn, $userId)
{
	$ts = time();
	$tmpSessionId = generateSessionId();
	$sql = "INSERT INTO sessions (sessid, userid, firstts, lastts) VALUES ('$tmpSessionId', $userId, $ts, $ts)";
	//debug($sql);
	$result = mysqli_query($cnn, $sql) or doError ("sql", "Errore nell'esecuzione della query SQL: " . $sql);
	setSessionId($tmpSessionId, $ts);
	return $tmpSessionId;
}
//
function refreshSession(&$cnn)
{
	$sessionId = getSessionId();
	$ts = time();
	$deltats = $ts - SESSIONTIMEOUT;
	if(ENABLE_CLEANUP)
	{
		$sql = "DELETE FROM titolo WHERE sessid  NOT IN (SELECT sessid  FROM sessions)";
		debug($sql);
        mysqli_query($cnn, $sql) or doError ("sql", "Errore nell'esecuzione della query SQL: " . $sql);
		
		$sql = "DELETE FROM tmp_variazione WHERE sessid  NOT IN (SELECT sessid  FROM sessions)";
		debug($sql);
        mysqli_query($cnn, $sql) or doError ("sql", "Errore nell'esecuzione della query SQL: " . $sql);
	}
	$sql = "DELETE FROM sessions WHERE lastts < $deltats";
	//debug($sql);
    mysqli_query($cnn, $sql) or doError ("sql", "Errore nell'esecuzione della query SQL: " . $sql);
	
	if(strlen($sessionId) > 0)
	{
		$sql = "UPDATE sessions SET lastts = $ts WHERE sessid='$sessionId'";
		//debug($sql);
		$result = mysqli_query($cnn, $sql) or doError ("sql", "Errore nell'esecuzione della query SQL: " . $sql);
		setSessionId($sessionId, $ts);
	}
}
//
function closeSession($cnn)
{
	$sessionId = getSessionId();
	setSessionId("");
	if(strlen($sessionId) > 0)
	{
		$sql = "DELETE FROM sessions WHERE sessid = '$sessionId'";
		//debug($sql);
		$result = mysqli_query($cnn, $sql) or doError ("sql", "Errore nell'esecuzione della query SQL: " . $sql);
	}
}
//
function setSessionId($sessionId, $ts = 0)
{
	if(strlen($sessionId) == 0)
	{
		if($ts == 0)
		{
			$ts = time() - 3600;
		}
	}
	else
	{
		if($ts == 0)
		{
			$ts = time() + SESSIONTIMEOUT;
		}
		else
		{
			$ts += SESSIONTIMEOUT;
		}
	}
	setcookie ("STAROPRAMEN_ID", $sessionId, $ts, "/");
}
//
function getSessionId()
{
	$sessionId = "";
	if(array_key_exists("STAROPRAMEN_ID", $_COOKIE))
	{
		$sessionId = $_COOKIE["STAROPRAMEN_ID"];
	}
	else
	{
		if(array_key_exists("SID", $_GET))
		{
			$sessionId = $_GET["SID"];
		}
	}
	$tmp = md5($_SERVER["REMOTE_ADDR"]);
	$tmp .= md5($_SERVER["HTTP_USER_AGENT"]);
	$tmp .= md5($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
	if(substr($sessionId, 0, 96) != $tmp)
	{
		$sessionId = "";
	}
	return $sessionId;
}
//
function getSessionUserId(&$cnn)
{
	static $userId = 0;
	if($userId == 0)
	{
		if(getSessionStatus($cnn) == OK)
		{
			$sessionId = getSessionId();
			if(strlen($sessionId) > 0)
			{
				$sql="SELECT userid FROM sessions WHERE sessid = '$sessionId'";
				//debug($sql);
				$result=mysqli_query($cnn, $sql) or doError ("sql", "Errore nell'esecuzione della query " . $sql);
				while($row = mysqli_fetch_assoc($result))
				{
					$userId = $row['userid'];
				}
				mysqli_free_result($result);
			}
		}
	}
	return $userId;
}
//
function getSessionStatus(&$cnn)
{
	static $sessionId = "";
	if($sessionId == "")
	{
		$sessionId = getSessionId();
		$lastts = 0;
		if($sessionId == "")
		{
			return FALSE;
		}
		else
		{
			$ts=time();
			$sql = "SELECT lastts FROM sessions WHERE sessid='$sessionId'";
			//debug($sql);
			$result = mysqli_query($cnn, $sql) or doError ("sql", "Errore nell'esecuzione della query SQL: " . $sql);
			while($row=mysqli_fetch_assoc($result))
			{
				$lastts = $row['lastts'];
			}
			mysqli_free_result($result);
			if($ts > $lastts + SESSIONTIMEOUT)
			{
				//session expired
				closeSession($cnn);
				$sessionId = "";
				return FALSE;
			}
			else
			{
				//session ok
				refreshSession($cnn);
				return TRUE;
			}
		}
	}
	else
	{
		return TRUE;
	}
}
?>