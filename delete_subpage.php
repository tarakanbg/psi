<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php
	// make sure the subject id sent is an integer
	if (intval($_GET['subpage']) == 0) {
		redirect_to('content.php');
	}
	
	$id = mysql_prep($_GET['subpage']);
	// make sure the page exists (not strictly necessary)
	// it gives some extra security and allows use of 
	// the page's subject_id for the redirect
	if ($subpage = get_subpage_by_id($id)) {
		// LIMIT 1 isn't necessary but is a good fail safe
		$query = "DELETE FROM ".DB_PREFIX."subpages WHERE id =".$subpage['id']." LIMIT 1";
		$result = mysql_query ($query);
		if (mysql_affected_rows() == 1) {
			// Successfully deleted
			redirect_to("content.php?page={$subpage['page_id']}");
		} else {
			// Deletion failed
			echo "<p>Page deletion failed.</p>";
			echo "<p>" . mysql_error() . "</p>";
			echo "<a href=\"content.php\">Return to Main Site</a>";
		}
	} else {
		// page didn't exist, deletion was not attempted
		redirect_to('content.php');
	}
?>
<?php 
// because this file didn't include footer.php we need to add this manually
mysql_close($db);
?>
