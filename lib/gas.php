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


function getBilancio(&$cnn, $userID){
	$retVal = 0.00;
	$userID = intval($userID);
	$sql = "SELECT ifnull( a.entrati, 0 ) - ifnull( b.usciti, 0 ) AS bilancio
			FROM (
			
			SELECT sum( B.importo ) usciti
			FROM movimenti AS B
			WHERE B.id_gasista =$userID
			)b, (
			
			SELECT sum( a.importo ) entrati
			FROM versamenti AS a
			WHERE a.id_gasista =$userID
			)a";
	$result = mysqli_query($cnn, $sql)
			or doError ("sql", "Errore nell'esecuzione della query " . $sql);
	while($row = mysqli_fetch_assoc($result))
	{
		$retVal = $row['bilancio'];
	}
	mysqli_free_result($result);
	return $retVal;
}
?>