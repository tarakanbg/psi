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

// Let's find out what the public language is

// Is there a cookie already set?
if  (isset ($_COOKIE['psi-lang'])) {
	$lang = $_COOKIE['psi-lang'];
}

// There's no cookie - let's ask the database
else {
	$query = "SELECT * FROM ".DB_PREFIX."variables ";
	$query .= "WHERE id=1";
	$result = mysql_query($query, $connection);
	$result_array = mysql_fetch_array($result);
	$lang = $result_array['value1'];
}

// Now we know the language. Let's set a cookie to
// avoid querying the database on each page reload
setcookie("psi-lang", $lang, time()+3600*24*5);

// Now let's retrieve the appropriate language pack
if ($lang == "us") {
	require_once("includes/languages/psi-english_us.php");
} elseif ($lang == "bg") {
	require_once("includes/languages/psi-bulgarian.php");
}

?>
