<?php
// Get the language setting - from cookie,
// from browser URL or set default
if (isset($_GET['lang'])) {
	$lang = $_GET['lang'];
} elseif  (isset ($_COOKIE['psi-lang'])) {
	$lang = $_COOKIE['psi-lang'];
} else {
	$lang="us";
}
//Set a cookie with the language choice
setcookie("psi-lang", $lang, time()+3600*24*365);

// Get the appropriate language file
if ($lang == "us") {
	require_once("languages/psi-english_us.php");
} elseif ($lang == "bg") {
	require_once("languages/psi-bulgarian.php");
}
 ?>
<?php require_once("constants.php"); ?>
<?php
// First - get the site title and short decription
// Are they stored in session?
if (isset($_SESSION['site_name'])) {
	$site_name = $_SESSION['site_name'];
	$site_short_descr = $_SESSION['site_short_descr'];
}
	else {

// The values are not in session - fetch them from DB
$query = "SELECT * FROM ".DB_PREFIX."variables ";
$query .= "WHERE id=2";
$result = mysql_query($query, $connection);
$site_details = mysql_fetch_array($result);
$site_name = $site_details['value1'];
$site_short_descr = $site_details['value2'];
}

// Let us store the values in session to avoid
// multiple queries
$_SESSION['site_name'] = $site_name;
$_SESSION['site_short_descr'] = $site_short_descr;
?>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" /> 
		<title><?php echo $site_name . " - " . $l_acptitle; ?></title>
		<meta name="keywords" content="PSI CMS">
		<meta name="description" content="PSI CMS">
		<link href="stylesheets/admin.css" media="all" rel="stylesheet" type="text/css" />
		
	</head>
	<body>
		<div id="header">
			<h1><?php echo "<a href=\"staff.php\" class=\"site-title\">" . $site_name . "</a>"; ?></h1>
		</div>
		<div id="main">
