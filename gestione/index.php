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
$cnn = getDbConnection();


$userId = getUserId($cnn);
if ($userId > 0) {
    $userData = getUserData($cnn, $userId);
} else {
    header("location: ../gestione/login.php");
    exit;
}
if (!$userData["fl_admin"] && !$userData["fl_contabile"] && !$userData["fl_attivo"]) {
    doError("wrong_login");
    exit;
}
$isAdmin = false;
if ($userData['fl_admin']) {
    $isAdmin = true;
}
$isCont = false;
if ($userData['fl_contabile']) {
    $isCont = true;
}
$target = $_REQUEST["sec"];
$selectedSection = "";
$includeFile = "";
$addFilter = "";
$excludeColumns = array();

$targetList[] = array("users", "Gasisti");
$targetList[] = array("fornitori", "Fornitori");
$targetList[] = array("causali", "Causali");
$targetList[] = array("versamenti", "Versamenti C/C");
$targetList[] = array("pagamenti", "Pagamenti C/C");
$targetList[] = array("movimenti", "Movimenti dei Gasisti");

switch ($target) {
    case "users":
        $onlyAdmin = true;
        $selectedSection = "users";
        $excludeColumns = array("id", "dt_ins", "dt_agg", "indirizzo_1", "indirizzo_2", "password", "username");
        break;
    case "fornitori":
        $onlyAdmin = true;
        $selectedSection = "fornitori";
        $excludeColumns = array("id", "dt_ins", "dt_agg", "indirizzo_1", "indirizzo_2");
        break;
    case "versamenti":
        $onlyAdmin = false;
        $selectedSection = "versamenti";
        $excludeColumns = array("id");
        break;
    case "pagamenti":
        $onlyAdmin = false;
        $selectedSection = "v_pagamenti";
        $excludeColumns = array("id", "tot_movimenti", "diff");
        break;
    case "movimenti":
        $onlyAdmin = false;
        $selectedSection = "movimenti";
        $excludeColumns = array("id");
        break;
    case "causali":
        $onlyAdmin = true;
        $selectedSection = "causali";
        $excludeColumns = array("id");
        break;
    default:
        $onlyAdmin = true;
        $selectedSection = "";
        break;
}
$baseUrl = "../gestione/index.php";
$absPage = 1;
$offset = 0;
$pageSize = PAGE_SIZE;
if (array_key_exists("absPage", $_REQUEST)) {
    $absPage = intval($_REQUEST["absPage"]);
}
if (array_key_exists("pageSize", $_REQUEST)) {
    $pageSize = intval($_REQUEST["pageSize"]);
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link href="../css/default.css" rel="STYLESHEET" type="text/css"/>
    <title>Contabilita' GAS DEL SOLE</title>
</head>
<body>
<script>
    function popUp(url, name, features) {
        window.open(url, name, features);
    }
</script>
<?
foreach ($targetList as $t) {
    $style = "background-color: white;";
    if ($t[0] == $target) {
        $style = "background-color: #ff6666;";
    }
    if ($t[0] == "versamenti" | $t[0] == "pagamenti" | $t[0] == "movimenti") {
        $style1 = "margin-left:100px;font-color:#ff6666;";
    }
    $str = "<p style=\"$style;$style1;margin-bottom:4px;margin-top:4px\"><a href=\"?sec=" . $t[0] . "\">" . $t[1] . "</a></p>";
    echo $str;
}
echo "<br />";
if ($isAdmin || $isCont) {
    echo "<a href=\"javascript:popUp('../gestione/saldo.php')\"> Estratto conto e Saldo C/C</a><br/>";
}
if ($isAdmin) {
    echo "<a href=\"javascript:popUp('../gestione/quote_annuali.php')\" onClick=\"return confirm('Attribuisci quote annuali a tutti i gasisti???operazione non è riversibile!');\"> Attribuisci quote annuali a tutti i gasisti</a><br/>";
    echo "<br/><a href=\"javascript:popUp('../gestione/mail_gasisti.php')\" onClick=\"return confirm('Spedire decine di email???');\"> Manda mail - situazione contabile a tutti</a><br/>";
}
echo "<br/><a href=\"../gestione/logout.php\">logout</a><br />";

echo "<div class=\"main\">";

if ($target != "") {
    echo "<br />\n";
    $tableData = array();
    $iniFile = "../conf/gestione.ini.php";
    $definition = getDefinition($cnn, $iniFile, $selectedSection);
    if ($addFilter != "") {
        $definition["filter"] = $addFilter;
    }

    if (count($definition) > 0) {
        $order = "";
        $direction = "";
        if (array_key_exists("ord", $_REQUEST)) {
            if (checkCleanChar($_REQUEST["ord"])) {
                $order = $_REQUEST["ord"];
            }
        }
        if (array_key_exists("dir", $_REQUEST)) {
            $direction = "asc";
            if ($_REQUEST["dir"] == "desc") {
                $direction = "desc";
            }
        }
        if (strlen($order) == 0) {
            $orderBy = $definition["order"];
        } else {
            $orderBy = "$order $direction";
        }

        $filter = $definition["filter"];
        $filterValues = array();
        if (array_key_exists("submit", $_REQUEST)) {
            $tmpFilter = array();
            foreach ($definition["fields"] as $f) {
                if (array_key_exists($f, $_REQUEST)) {
                    if (strlen($_REQUEST[$f]) > 0) {
                        if (checkCleanChar($_REQUEST[$f])) {
                            $filterValues[$f] = $_REQUEST[$f];
                            $tmpFilter[] = "$f LIKE '%" . $_REQUEST[$f] . "%'";
                        }
                    }
                }
            }
            if (count($tmpFilter) > 0) {
                if (trim($filter) == "") {
                    $filter = implode(" AND ", $tmpFilter);
                } else {
                    $filter .= " AND " . implode(" AND ", $tmpFilter);
                }
            }
        }

        $currUrl = getCurrUrl(array("ord", "dir", "absPage"));
        $blocksCount = countRowsFromTable($cnn, $definition["table"], $filter, $pageSize, $absPage);

        if ($blocksCount < ($pageSize * ($absPage - 1))) {
            $absPage = 1;
        }
        $tableData = getRowsFromTable($cnn, $definition["table"], $filter, $orderBy, $pageSize, $absPage);
    }
    echo "<table width=\"100%\" border=\"0\">";
    echo "<tr>";
    echo "<th></th>";
    foreach ($definition["fields"] as $f) {
        if (in_array($f, $excludeColumns)) {
            continue;
        }
        $newDir = "asc";
        if (($order == $f) && ($direction == "asc")) {
            $newDir = "desc";
        }
        echo "<th>";
        echo "<a href='$currUrl&ord=$f&dir=$newDir' class='link bold'>";
        echo $definition[$f]["label"];
        echo "</a>";
        if ($order == $f) {
            echo "&nbsp;";
            echo "<img src='../img/$newDir.png' border='0' width='7' height='7'>";
        }
        echo "</th>";
    }
    echo "<th>";

    echo "</th>";
    echo "</tr>";
    echo "<tr>";
    echo "<form name='srcusr' method='get' action='$baseUrl'>";
    echo GetInput("sec", "hidden", $target, 10, 10);
    echo GetInput("ord", "hidden", $order, 10, 10);
    echo GetInput("dir", "hidden", $direction, 10, 10);
    echo GetInput("absPage", "hidden", $absPage);
    echo "<th><input type='submit' name='submit' value='-&gt;]'></th>";

    foreach ($definition["fields"] as $f) {
        if (in_array($f, $excludeColumns)) {
            continue;
        }

        echo "<th>";
        if (($definition[$f]["type"] == "text") && ($definition[$f]["macrotype"] != "calendar")) {
            echo GetInput($f, "text", $filterValues[$f], $definition[$f]["datasize"], 10);
        }
        echo "</th>";
    }
    echo "<th>&nbsp;</th>";
    echo "</form>";
    echo "</tr>";

    echo "<tr>";
    echo "<td colspan=\"" . (count($definition["fields"]) + 1) . "\">";
    if (($onlyAdmin && $isAdmin) || (!$onlyAdmin && $isCont)) {
        echo getCmAdminBlockIcons("add", "param1=$selectedSection", $tableData[$i][$definition["key"]]);
    }
    echo "</td>";
    echo "</tr>";
    echo "<tr><td colspan=\"" . (count($definition["fields"]) + 1) . "\"><hr noshade size='1' color='$leadcolor' style='dot'></td></tr>";

    if (count($tableData) > 0) {
        for ($i = 0; $i < count($tableData); $i++) {
            $bgcolor = "";
            if ($target == "pagamenti" ) {
                if ($tableData[$i]['diff'] > 0) {
                    $bgcolor = 'lightgreen';
                } elseif (!$tableData[$i]['diff'] || $tableData[$i]['diff'] < 0) {
                    $bgcolor = 'pink';
                }
            } else {

            }
            echo "<tr bgcolor=\"$bgcolor\">";
            echo "<td>";
            if (($onlyAdmin && $isAdmin) || (!$onlyAdmin && $isCont)) {

                if ($target == "users" && getBilancio($cnn, $tableData[$i][$definition["key"]]) != 0) {
                    echo getCmAdminBlockIcons("edit_only", "param1=$selectedSection", $tableData[$i][$definition["key"]]);
                } else if ( /*($target == "pagamenti" || $target == "movimenti") && */
                    !$isAdmin && $isCont
                ) {
                    echo getCmAdminBlockIcons("edit_only", "param1=$selectedSection", $tableData[$i][$definition["key"]]);
                } else if ($isAdmin) {
                    echo getCmAdminBlockIcons("edit", "param1=$selectedSection", $tableData[$i][$definition["key"]]);
                } else {
                    echo getCmAdminBlockIcons("view_only", "param1=$selectedSection", $tableData[$i][$definition["key"]]);
                }

            } else {
                if ($target == 'users') {
                    if ($tableData[$i][$definition["key"]] == $userId) {
                        echo getCmAdminBlockIcons("edit_only", "param1=$selectedSection", $tableData[$i][$definition["key"]]);
                    } else {
                        echo getCmAdminBlockIcons("view_only", "param1=$selectedSection", $tableData[$i][$definition["key"]]);
                    }
                } else {
                    echo getCmAdminBlockIcons("view_only", "param1=$selectedSection", $tableData[$i][$definition["key"]]);
                }
            }
            echo "</td>";
            foreach ($definition["fields"] as $f) {
                if (in_array($f, $excludeColumns)) {
                    continue;
                }
                echo "<td>";
                switch ($definition[$f]["type"]) {
                    case "textarea":
                    case "htmlarea":
                        echo getAbstract($tableData[$i][$f]);
                        break;
                    case "text":
                        echo $tableData[$i][$f];
                        break;
                    case "color":
                        echo "<span style=\"border:1px solid black; color: #" . $tableData[$i][$f] . "\">" . $tableData[$i][$f] . "</span>";
                        break;
                    case "combo":
                        echo getValsFromDB($cnn, $definition[$f]["combo_lookup"], $definition[$f]["combo_order"], $definition[$f]["combo_key"], $definition[$f]["combo_value"], $tableData[$i][$f], $definition[$f]["combo_multiple"], $definition[$f]["combo_emptyline"], TRUE);
                        break;
                    case "password":
                        echo "******";
                        break;
                    case "checkbox":
                        echo GetCheckBox("", 1, $tableData[$i][$f], "disabled");
                        break;
                }
                echo "</td>";
            }
            echo "<td>";
            echo "</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td colspan=\"" . (count($definition["fields"]) + 1) . "\">";
            echo "<hr noshade size='1' color='$leadcolor' style='dot'>";
            echo "</td>";
            echo "</tr>";
        }
    }
    echo "<tr>";
    echo "<td>";
    if (($onlyAdmin && $isAdmin) || (!$onlyAdmin && $isCont)) {
        if ($target != "pages" && $target != "style") {
            echo getCmAdminBlockIcons("add", "param1=$selectedSection", $tableData[$i][$definition["key"]]);
        }
    }
    echo "<td colspan=\"" . (count($definition["fields"]) + 1) . "\">";
    echo "</td>";
    echo "</tr>";

    echo "</table>";
    echo getHumanPagesBar($blocksCount, $pageSize, $absPage);
}
echo "</div>";
?>


</body>
</html>
