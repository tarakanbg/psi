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
	$id = $_GET['image'];
	$query = "DELETE FROM ".DB_PREFIX."image WHERE ImageId = ".$id." LIMIT 1";
	$result = mysql_query ($query);
	if (mysql_affected_rows() == 1) {
		// Successfully deleted
			redirect_to("content.php");
		} else {
			// Deletion failed
			echo "<p>Page deletion failed.</p>";
			echo "<p>" . mysql_error() . "</p>";
			echo "<a href=\"content.php\">Return to Main Site</a>";
		}
	
?>
<?php 
// because this file didn't include footer.php we need to add this manually
mysql_close($connection);
?>
