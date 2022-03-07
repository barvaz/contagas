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


function getUserId(&$cnn)
{
	static $userId = 0;
	if($userId == 0)
	{
		if(isLogged($cnn))
		{
			$userId = getSessionUserId($cnn);
		}
	}
	return $userId;
}
//
function isLogged(&$cnn)
{
	static $userStatus = FALSE;
	if($userStatus == FALSE)
	{
		$userId = getSessionUserId($cnn);
		//debug("getSessionUserId" . $userId);
		if($userId > 0)
		{
			$sql = "SELECT fl_attivo FROM users WHERE id = $userId";
			//debug($sql);
			$result = mysqli_query($cnn, $sql) or doError ("sql", "Errore nell'esecuzione della query SQL: " . $sql);
			while($row = mysqli_fetch_assoc($result))
			{
				if($row["fl_attivo"])
				{
					$userStatus = TRUE;
				}
				else
				{
					$userStatus = FALSE;
				}
			}
			mysqli_free_result($result);
		}
	}
	return $userStatus;
}

//
function login(&$cnn, $userName, $userPassword, $caseSensitive = FALSE, $encrypt = FALSE)
{
	// check per caratteri
	$tmpUserId = 0;
	$tmpPassword = "";
	$tmpUserName = "";
	$dataOk = FALSE;
	//closeSession($cnn);
	if(checkCleanChar($userName) && checkCleanChar($userPassword))
	{
		$sql = "SELECT id, username, password FROM users WHERE username = '$userName' AND fl_attivo = 1";
		//debug($sql);
		$result = mysqli_query($cnn, $sql) or doError ("sql", "Errore nell'esecuzione della query SQL: " . $sql);
		$found = FALSE;
		while ($row = mysqli_fetch_assoc($result))
		{
			if($caseSensitive)
			{
				if($row["username"] == $userName)
				{
					$found = TRUE;
				}
			}
			else
			{
				if(mysqli_num_rows($result) == 1)
				{
					$found = TRUE;
				}
			}
			if($found)
			{
				$tmpUserId = $row["id"];
				$tmpUserName = $row["username"];
				$tmpPassword = $row["password"];
			}
		}
		mysqli_free_result($result);
		if($tmpUserId > 0)
		{
			if($encrypt)
			{
				if(crypt($userPassword, $tmpPassword) == $tmpPassword)
				{
					$dataOk = TRUE;
				}
			}
			else
			{
				if($userPassword == $tmpPassword)
				{
					$dataOk = TRUE;
				}
			}
		}
	}
	if($dataOk == TRUE)
	{
		$tmpSessionID = startSession($cnn, $tmpUserId);
		if(strlen($tmpSessionID) > 0)
		{
			return TRUE;
		}
	}
	return FALSE;
}
//
function logout(&$cnn)
{
	$sessionId = getSessionId();
	if(strlen($sessionId) > 0)
	{
		$tmpUserId = getSessionUserId($cnn);
		closeSession($cnn);
		return TRUE;
	}
	return FALSE;
}

function getUserData(&$db, $userId){
	$userId = intval($userId);
	$userData = array();
	$userData["id"] = 0;
	$userData["username"] = "";
	$userData["fl_admin"] = 0;

	if($userId > 0)
	{
		$query = "SELECT * FROM users WHERE id = $userId AND fl_attivo = 1";
		//debug($query);
		$result = mysqli_query($db, $query) or doError ("sql", "Errore nell'esecuzione della query SQL: " . $query);
		while($row = mysqli_fetch_assoc($result))
		{
			$userData = $row;
		}
		mysqli_free_result($result);
	}
	return $userData;
}

function cambiaPassword(&$cnn, $userId, $oldPass, $newPass)
{
	$sql = "SELECT password  FROM users WHERE id = $userId AND fl_attivo = 1";
	//debug($sql);
	$result = mysqli_query($cnn, $sql) or doError ("sql", "Errore nell'esecuzione della query SQL: " . $sql);
	while($row = mysqli_fetch_assoc($result))
	{
		$pass = $row["password"];
	}
	mysqli_free_result($result);
	if(USERPASSWORD_CRYPT)
	{
		if(crypt($oldPass, $pass) == $pass)
		{
			$newPass = crypt($newPass1);
		}
		else
		{
			doError("wrongpass");
			exit;
		}
	}
	else
	{
		if($oldPass == $pass)
		{
			$oldOK = TRUE;
		}
		else
		{
			doError("wrongpass");
			exit;
		}
	}
	
	$sql = "UPDATE users SET password = '" . addslashes($newPass) . "' WHERE id = $userId";
	//debug($sql);
    mysqli_query($cnn, $sql) or doError("sql", $sql);
}
?>