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

include('../conf/errortable.php');

$err = trim($_REQUEST["errorcode"]);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<link href="../css/default.css" rel="STYLESHEET" type="text/css">
	<title>Contabilita' GAS DEL SOLE - Login</title>
</head>

<body>
<h1><?=$errorTable[$err]?></h1>
<?php
include("../include/error.php");

{
	echo "<br/>";
	echo "<a href=\"../gestione/login.php\">login</a>";
}

?>

</body>
</html>
