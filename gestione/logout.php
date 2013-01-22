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
$cnn = getDbConnection();

$status = logout($cnn);
header ("Location: ../gestione/login.php");
exit;
?>