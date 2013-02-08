<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php

if (isset($_GET['edittorn'])) {
	
	// START FORM PROCESSING
	// only execute the form processing if the form has been submitted
	if (isset($_POST['submit'])) {
		include_once("includes/form_functions.php");
		// initialize an array to hold our errors
		$errors = array();
	
		// perform validations on the form data
		$required_fields = array('menu_name', 'position', 'content');
		$errors = array_merge($errors, check_required_fields($required_fields));
		
		$fields_with_lengths = array('menu_name' => 50);
		$errors = array_merge($errors, check_max_field_lengths($fields_with_lengths));
		
		// clean up the form data before putting it in the database
		$id = mysql_prep($_GET['edittorn']);
		$menu_name = trim(mysql_prep($_POST['menu_name']));
		$position = mysql_prep($_POST['position']);
		$visible = mysql_prep($_POST['visible']);
		$content = mysql_prep($_POST['content']);
		$istitle = mysql_prep($_POST['istitle']);
	
		// Database submission only proceeds if there were NO errors.
		if (empty($errors)) {			
				$query = 	"UPDATE ".DB_PREFIX."tornpages SET 
							menu_name = '{$menu_name}',
							position = {$position}, 
							visible = {$visible},
							content = '{$content}',
							istitle = '{$istitle}'
						WHERE id = {$id}";
			
			
			
			$result = mysql_query($query);
			// test to see if the update occurred
			if (mysql_affected_rows() == 1) {
				// Success!
				$message = "The page was successfully updated.";
			} else {
				$message = "The page could not be updated.";
				$message .= "<br />" . mysql_error();
			}
		 } else {
			if (count($errors) == 1) {
				$message = "There was 1 error in the form.";
			} else {
				$message = "There were " . count($errors) . " errors in the form.";
			}
		} // end of errors in form
		// END FORM PROCESSING
	} // end of if submit
	
} // end of edittorn section

elseif ($_GET['addtorn'] == "yes") {

// START FORM PROCESSING
	// only execute the form processing if the form has been submitted
	if (isset($_POST['submit'])) {
		include_once("includes/form_functions.php");
		// initialize an array to hold our errors
		$errors = array();
	
		// perform validations on the form data
		$required_fields = array('menu_name', 'position', 'content', 'istitle');
		$errors = array_merge($errors, check_required_fields($required_fields));
		
		$fields_with_lengths = array('menu_name' => 50);
		$errors = array_merge($errors, check_max_field_lengths($fields_with_lengths));
		
		// clean up the form data before putting it in the database
		$menu_name = trim(mysql_prep($_POST['menu_name']));
		$position = mysql_prep($_POST['position']);
		$visible = mysql_prep($_POST['visible']);
		$content = mysql_prep($_POST['content']);
		$istitle = mysql_prep($_POST['istitle']);
	
		// Database submission only proceeds if there were NO errors.
		if (empty($errors)) {			
					$query = "INSERT INTO ".DB_PREFIX."tornpages (
						menu_name, position, visible, content, istitle
					) VALUES (
						'{$menu_name}', {$position}, {$visible}, '{$content}', {$istitle}
					)";
			

			$result = mysql_query($query);
			// test to see if the update occurred
			if (mysql_affected_rows() == 1) {
				// Success!
				$message = "The page was successfully created.";
			} else {
				$message = "The page could not be created.";
			}
		 } else {
			if (count($errors) == 1) {
				$message = "There was 1 error in the form.";
			} else {
				$message = "There were " . count($errors) . " errors in the form.";
			}
		} // end of errors in form
		// END FORM PROCESSING
	} // end of if submit
	
} elseif (isset($_GET['deltorn'])) {
	
	$id = mysql_prep($_GET['deltorn']);
	// make sure the page exists (not strictly necessary)
	// it gives some extra security and allows use of 
	// the page's subject_id for the redirect
	if ($tornpage = get_torn_by_id($id)) {
		// LIMIT 1 isn't necessary but is a good fail safe
		$query = "DELETE FROM ".DB_PREFIX."tornpages WHERE id = {$tornpage['id']} LIMIT 1";
		$result = mysql_query ($query);
		if (mysql_affected_rows() == 1) {
			// Successfully deleted
			redirect_to("torns.php");
		} else {
			// Deletion failed
			echo "<p>Page deletion failed.</p>";
			echo "<p>" . mysql_error() . "</p>";
			echo "<a href=\"torns.php\">Return to torn pages</a>";
		}
	} else {
		// page didn't exist, deletion was not attempted
		redirect_to('torns.php');
	}

}
?>
<?php 
if (isset($_GET['edittorn']) || $_GET['addtorn'] == "yes") {
	include("includes/header_mce.php");
} else {
	include("includes/header.php");
}
?>
<table id="structure">
	<tr>
		<td id="navigation">
			<?php include("includes/lang_select.php");	?>
			<?php
			if (isset($_GET['torn'])) {
				echo "<a href = \"torns.php\"> + " . $l_back_to_torns . "</a>";
			} elseif (isset($_GET['edittorn'])) {
				if (isset($_POST['submit'])) {
					echo "<a href = \"torns.php?torn=" . $_GET['edittorn'] . "\"> + " . $l_back_to_torn . "</a>";
				} else {
				echo "<a href = \"torns.php?torn=" . $_GET['edittorn'] . "\"> + " . $l_cancel_torn_edit . "</a>";
				}
			} elseif ($_GET['addtorn'] == "yes") {
				echo "<a href = \"torns.php\"> + " . $l_back_to_torns . "</a>";
			}
			?>
		</td>
		<td id="page"><?php
			// If a torn ID is passed from the URL, display its contents and suggest to edit it
			if (isset($_GET['torn'])) {
				$sel_torn_id = $_GET['torn'];
				$torn = get_torn_by_id($sel_torn_id);
				echo "<h2>" . $torn['menu_name'] . "</h2>";
				echo "<div class=\"page-content\">" . $torn['content'] . "</div>";
				echo "<div style=\"margin-top: 2em; border-top: 1px solid #000000;\">
				<br /><a href=\"torns.php?edittorn=" . $torn['id'] . "\">" . $l_edit_torn_page .  "</a>
			
			</div>";
				
			} elseif (isset($_GET['edittorn'])) { // start of edit form
			$torn = get_torn_by_id($_GET['edittorn']); ?>
			
			<h2><?php echo $l_edittorn2; ?><?php echo $torn['menu_name']; ?></h2>
			<?php if (!empty($message)) {echo "<p class=\"message\">" . $message . "</p>";} ?>
			<?php if (!empty($errors)) { display_errors($errors); } ?>
			
			<form action="torns.php?edittorn=<?php echo $torn['id']; ?>" method="post">
			
			<p><?php echo $l_page_name; ?><input type="text" name="menu_name" value="<?php echo $torn['menu_name']; ?>" id="menu_name" /></p>

			<p><?php echo $l_position; ?><select name="position">
				<?php
						$torn_page_set = get_all_torns();
						$tornpage_count = mysql_num_rows($torn_page_set);
					
					for ($count=1; $count <= $tornpage_count; $count++) {
						echo "<option value=\"{$count}\"";
						if ($torn['position'] == $count) { echo " selected"; }
						echo ">{$count}</option>";
					}
				?>
			</select></p>
			<p><?php echo $l_visible; ?>
				<input type="radio" name="visible" value="0"<?php 
				if ($torn['visible'] == 0) { echo " checked"; } 
				?> /> <?php echo $l_No; ?>
				&nbsp;
				<input type="radio" name="visible" value="1"<?php 
				if ($torn['visible'] == 1) { echo " checked"; } 
				?> /> <?php echo $l_Yes; ?> &nbsp; &nbsp; &nbsp;
				
				<?php echo $l_torn_visible_on_title; ?>
				<input type="radio" name="istitle" value="0"<?php 
				if ($torn['istitle'] == 0) { echo " checked"; } 
				?> /> <?php echo $l_No; ?>
				&nbsp;
				<input type="radio" name="istitle" value="1"<?php 
				if ($torn['istitle'] == 1) { echo " checked"; } 
				?> /> <?php echo $l_Yes; ?>
				
			</p>
			<p><?php echo $l_content; ?><br />
				<textarea name="content" rows="20" cols="100"><?php echo $torn['content']; ?></textarea>
			</p>	
				<input type="submit" name="submit" value="<?php echo $l_update_torn; ?>" />&nbsp;&nbsp;
				<a href="torns.php?deltorn=<?php echo $torn['id']; ?>" onclick="return confirm('<?php echo $l_del_torn_confirm; ?>');"><?php echo $l_del_torn; ?></a>
			</form>
			
			<?php } // end of edit form
			
			elseif ($_GET['addtorn'] == "yes") { ?>
				
			<h2><?php echo $l_addtorn2; ?></h2>
			<?php if (!empty($message)) {echo "<p class=\"message\">" . $message . "</p>";} ?>
			<?php if (!empty($errors)) { display_errors($errors); } ?>
			
			<form action="torns.php?addtorn=yes" method="post">
			
			<p><?php echo $l_page_name; ?><input type="text" name="menu_name" value="" id="menu_name" /></p>

			<p><?php echo $l_position; ?><select name="position">
				<?php
						$torn_page_set = get_all_torns();
						$tornpage_count = mysql_num_rows($torn_page_set) + 1;
					
					for ($count=1; $count <= $tornpage_count; $count++) {
						echo "<option value=\"{$count}\"";
						echo ">{$count}</option>";
					}
				?>
			</select></p>
			<p><?php echo $l_visible; ?>
				<input type="radio" name="visible" value="0" /> <?php echo $l_No; ?>
				&nbsp;
				<input type="radio" name="visible" value="1" "checked" /> <?php echo $l_Yes; ?> &nbsp; &nbsp; &nbsp;
				
				<?php echo $l_torn_visible_on_title; ?>
				<input type="radio" name="istitle" value="0" "checked" /> <?php echo $l_No; ?>
				&nbsp;
				<input type="radio" name="istitle" value="1" /> <?php echo $l_Yes; ?>
				
			</p>
			<p><?php echo $l_content; ?><br />
				<textarea name="content" rows="20" cols="100"></textarea>
			</p>	
				<input type="submit" name="submit" value="<?php echo $l_add_this_torn; ?>" />
			</form>
			
			<?php } // end of edit form
				
				
			
			else { ?>
			<h2><?php echo $l_torn_pages; ?></h2>
			<div class="page-content">
			<p><em><?php echo $l_torn_pages_intro; ?></em></p>
			<?php display_all_torns(); ?>
			</div>
			
			<div style="margin-top: 2em; border-top: 1px solid #000000;">
			<?php
			echo "<br /><a href=\"torns.php?addtorn=yes\"><strong>". $l_add_new_torn . "</strong></a>";
			
			?>
			</div>
			
				
			<br /><a href="staff.php"><?php echo $l_return_to_menu; ?></a>
		
		<?php } ?></td>
	</tr>
</table>
<?php require("includes/footer.php"); ?>
