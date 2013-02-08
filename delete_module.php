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
?>
<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php
	include_once("includes/form_functions.php");

$id = $_GET['id'];
$query = "DELETE FROM ".DB_PREFIX."modules WHERE id = ".$id." LIMIT 1";
$result = mysql_query ($query, $connection);
if (mysql_affected_rows() == 1) {
// Successfully deleted
	redirect_to("manage_modules.php?msg=deldone");
} else {
	redirect_to("manage_modules.php?msg=delfail");
}
	
?>
<?php 
// because this file didn't include footer.php we need to add this manually
mysql_close($connection);
?>
