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

// This module prints the classic unmodified version
// of the frontend footer

// Let's check the PSI CMS version
// Is it in session?
if (isset($_SESSION['psi_version'])) {
	$psi_version = $_SESSION['psi_version'];
} else {
// The version is not in session, we'll get it from DB
	$query = "SELECT * FROM ".DB_PREFIX."variables ";
	$query .= "WHERE id=5";
	$result = mysql_query($query, $connection);
	$version_set = mysql_fetch_array($result);
	$psi_version = $version_set['value1'];
}
// We will write the version to session to avoid
// multiple queries
$_SESSION['psi_version'] = $psi_version;
//Get the site name from session - it should be there
$site_name = $_SESSION['site_name'];
?>
Copyright <?php echo date ('Y') . ", " . $site_name; ?>
<br />
<font size="1">Powered by <a href="http://psi.bgnetwork.net"><font color="#EEE4B9">
PSI CMS</font></a> v. <?php echo $psi_version; ?></font>
