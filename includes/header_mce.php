<?php require_once("constants.php"); ?>
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
		<link href="stylesheets/admin.css" media="all" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="javascripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	editor_selector : "mceEditor",
	editor_deselector : "mceNoEditor",
	plugins : "table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,flash,searchreplace,print,contextmenu",
	theme_advanced_buttons1_add_before : "save,separator",
	theme_advanced_buttons1_add : "fontselect,fontsizeselect",
	theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,zoom,separator,forecolor,backcolor",
	theme_advanced_buttons2_add_before: "cut,copy,paste,separator,search,replace,separator",
	theme_advanced_buttons3_add_before : "tablecontrols,separator",
	theme_advanced_buttons3_add : "emotions,iespell,flash,advhr,separator,print",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	plugin_insertdate_dateFormat : "%Y-%m-%d",
	plugin_insertdate_timeFormat : "%H:%M:%S",
	extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	external_link_list_url : "example_data/example_link_list.js",
	external_image_list_url : "javascripts/images.js",
	flash_external_list_url : "example_data/example_flash_list.js"
});
</script>

<script>
function openPopup(url) {
 window.open(url, "popup_id", "scrollbars,resizable,width=300,height=400");
 return false;
}
</script>
	</head>
	<body>
		<div id="header">
			<h1><?php echo "<a href=\"staff.php\" class=\"site-title\">" . $site_name . "</a>"; ?></h1>
		</div>
		<div id="main">
