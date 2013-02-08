<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php
		if (intval($_GET['subj']) == 0) {
			redirect_to("content.php");
		}
		if (isset($_POST['submit'])) {
			$errors = array();

			$required_fields = array('menu_name', 'position');
			foreach($required_fields as $fieldname) {
				if (!isset($_POST[$fieldname]) || (empty($_POST[$fieldname]) && $_POST[$fieldname] != 0)) { 
					$errors[] = $fieldname; 
				}
			}
			$fields_with_lengths = array('menu_name' => 50);
			foreach($fields_with_lengths as $fieldname => $maxlength ) {
				if (strlen(trim(mysql_prep($_POST[$fieldname]))) > $maxlength) { $errors[] = $fieldname; }
			}
			
			if (empty($errors)) {
				// Perform Update
				$id = mysql_prep($_GET['subj']);
				$menu_name = mysql_prep($_POST['menu_name']);
				$position = mysql_prep($_POST['position']);
				$visible = mysql_prep($_POST['visible']);
				
				$query = "UPDATE ".DB_PREFIX."subjects SET 
							menu_name = '{$menu_name}', 
							position = {$position}, 
							visible = {$visible} 
						WHERE id = {$id}";
				$result = mysql_query($query, $connection);
				if (mysql_affected_rows() == 1) {
					// Success
					$message = "The subject was successfully updated.";
				} else {
					// Failed
					$message = "The subject update failed.";
					$message .= "<br />". mysql_error();
				}
				
			} else {
				// Errors occurred
				$message = "There were " . count($errors) . " errors in the form.";
			}
			
			
			
			
		} // end: if (isset($_POST['submit']))
?>
<?php find_selected_page(); ?>
<?php include("includes/header.php"); ?>
<table id="structure">
	<tr>
		<td id="navigation">
			<?php include("includes/lang_select.php");	?>
		
			<?php echo navigation($sel_subject, $sel_page, $sel_subpage); ?>
			<br />
			<a href="new_subject.php">+ <?php echo $l_add_subject; ?></a>			
		</td>
		<td id="page">
			<h2><?php echo $l_edit_subject; ?><?php echo $sel_subject['menu_name']; ?></h2>
			<?php if (!empty($message)) {
				echo "<p class=\"message\">" . $message . "</p>";
			} ?>
			<?php
			// output a list of the fields that had errors
			if (!empty($errors)) {
				echo "<p class=\"errors\">";
				echo "Please review the following fields:<br />";
				foreach($errors as $error) {
					echo " - " . $error . "<br />";
				}
				echo "</p>";
			}
			?>
			<form action="edit_subject.php?subj=<?php echo urlencode($sel_subject['id']); ?>" method="post">
				<p><?php echo $l_subject_name; ?>
					<input type="text" name="menu_name" value="<?php echo $sel_subject['menu_name']; ?>" id="menu_name" />
				</p>
				<p><?php echo $l_position; ?> 
					<select name="position">
						<?php
							$subject_set = get_all_subjects();
							$subject_count = mysql_num_rows($subject_set);
							// $subject_count + 1 b/c we are adding a subject
							for($count=1; $count <= $subject_count+1; $count++) {
								echo "<option value=\"{$count}\"";
								if ($sel_subject['position'] == $count) {
									echo " selected";
								} 
								echo ">{$count}</option>";
							}
						?>
					</select>
				</p>
				<p><?php echo $l_visible; ?> 
					<input type="radio" name="visible" value="0"<?php 
					if ($sel_subject['visible'] == 0) { echo " checked"; } 
					?> /> <?php echo $l_No ?>
					&nbsp;
					<input type="radio" name="visible" value="1"<?php 
					if ($sel_subject['visible'] == 1) { echo " checked"; } 
					?> /> <?php echo $l_Yes; ?>
				</p>
				<input type="submit" name="submit" value="<?php echo $l_edit_subject2; ?>" />
				&nbsp;&nbsp;
				<a href="delete_subject.php?subj=<?php echo urlencode($sel_subject['id']); ?>" onclick="return confirm('<?php echo $l_confirm_del_subject; ?>');"><?php echo $l_delete_subject; ?></a>
			</form>
			<br />
			<a href="content.php"><?php echo $l_cancel; ?></a>
			<div style="margin-top: 2em; border-top: 1px solid #000000;">
				<h3><?php echo $l_pages_in_subject; ?></h3>
				<ul>
<?php 
	$subject_pages = get_pages_for_subject($sel_subject['id']);
	while($page = mysql_fetch_array($subject_pages)) {
		echo "<li><a href=\"content.php?page={$page['id']}\">
		{$page['menu_name']}</a></li>";
	}
?>
				</ul>
				<br />
				+ <a href="new_page.php?subj=<?php echo $sel_subject['id']; ?>"><?php echo $l_add_page_to_subject; ?></a>
			</div>
		</td>
	</tr>
</table>
<?php require("includes/footer.php"); ?>
