<?php
/***********---   PSI CMS    ---***********
**                                       **
**  Visit us at http://psi.tarakan.eu    **
**  This software is released under the  **
**           terms of GNU GPL            **
**        Developed & maintained by      **
**    Svilen Vassilev (psi@tarakan.eu)   **
**                                       **
******************************************/
function load_modules($location) {
global $connection;

$query = "SELECT * FROM ".DB_PREFIX."modules ";
$query .= "WHERE location='".$location."' ";
$query .= "AND active=1 ";
$query .= "ORDER BY position ASC";
$result = mysql_query($query, $connection);

while ($module = mysql_fetch_array($result)) {
	$type = $module['type'];
	$address = $module['address'];
	$content = $module['content'];
	$scope = $module['scope'];
	$separator = $module['separator'];
	
	if ($scope == "global") {
		if ($type == "file") {
			require_once("$address");
		} elseif ($type == "html") {
			echo $content;
		}
	} elseif ($scope == "title") {
		if (empty($_GET)) {
			if ($type == "file") {
				require_once("$address");
			} elseif ($type == "html") {
				echo $content;
			}
		}
	} elseif ($scope == "inner") {
		if (!empty($_GET)) {
			if ($type == "file") {
				require_once("$address");
			} elseif ($type == "html") {
				echo $content;
			}
		}
	}
	echo $separator;
}

}

?>
