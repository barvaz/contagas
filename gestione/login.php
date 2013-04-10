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

?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>GAS DEL SOLE - Login</title>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" type="text/CSS" href="../css/default.css" />
</head>

<body>
	<div id="Container">
		<hr />
		<div class="Col2">
			<form name="login" method="post" action="../gestione/dologin.php">
				<table border="0" cellpadding="10" cellspacing="0" width="300">
					<tr>
						<td align="left" valign="middle" class="form" width="100">USERNAME</td>
						<td align="left" valign="middle" width="100" class="form" colspan="3">
							<input type="text" name="username" size="50" maxlength="50" />
						</td>
					</tr>
					<tr>
						<td align="left" valign="middle" class="form">PASSWORD</td>
						<td align="left" valign="middle" class="form" colspan="3">
							<input type="password" name="password" size="50" maxlength="50" />
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center" valign="middle" class="form">
							<input type="submit" value="&nbsp;&nbsp;Login&nbsp;&nbsp;" class="testo" />
						</td>
					</tr>
				</table>
			</form>
		</div><!-- Fine Col2 -->
		<hr />
	</div><!-- Fine Container -->
</body>
</html>
