<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php
	// make sure the subject id sent is an integer
	if (intval($_GET['subj']) == 0) {
		redirect_to('content.php');
	}

	include_once("includes/form_functions.php");

	// START FORM PROCESSING
	// only execute the form processing if the form has been submitted
	if (isset($_POST['submit'])) {
		// initialize an array to hold our errors
		$errors = array();
	
		// perform validations on the form data
		$required_fields = array('menu_name', 'position', 'content');
		$errors = array_merge($errors, check_required_fields($required_fields, $_POST));
		
		$fields_with_lengths = array('menu_name' => 50);
		//$errors = array_merge($errors, 
//check_max_field_lengths($fields_with_lengths, $_POST));
		
		// clean up the form data before putting it in the database
		$subject_id = mysql_prep($_GET['subj']);
		$menu_name = trim(mysql_prep($_POST['menu_name']));
		$position = mysql_prep($_POST['position']);
		$visible = mysql_prep($_POST['visible']);
		$content = mysql_prep($_POST['content']);
		$description = mysql_prep($_POST['description']);
		$keywords = mysql_prep($_POST['keywords']);
		$editor_id = $_SESSION['user_id'];
		$editor_data = get_admin_by_id ($editor_id);
		$editor = $editor_data['full_name'];
		$author = $editor;
	
		// Database submission only proceeds if there were NO errors.
		if (empty($errors)) {
			$query = "INSERT INTO ".DB_PREFIX."pages (
						menu_name, position, visible, content, subject_id, description, keywords, editor, author
					) VALUES (
						'{$menu_name}', {$position}, {$visible}, '{$content}', {$subject_id}, '{$description}', '{$keywords}', '{$editor}', '{$author}'
					)";
			if ($result = mysql_query($query, $connection)) {
				// as is, $message will still be discarded on the redirect
				$message = "The page was successfully created.";
				// get the last id inserted over the current db connection
				$new_page_id = mysql_insert_id();
				redirect_to("content.php?page={$new_page_id}");
			} else {
				$message = "The page could not be created.";
				$message .= "<br />" . mysql_error();
			}
		} else {
			if (count($errors) == 1) {
				$message = "There was 1 error in the form.";
			} else {
				$message = "There were " . count($errors) . " errors in the form.";
			}
		}
		// END FORM PROCESSING
	}
?>
<?php find_selected_page(); ?>
<?php include("includes/header_mce.php"); ?>
<table id="structure">
	<tr>
		<td id="navigation">
			<?php include("includes/lang_select.php");	?>
		
			<?php echo navigation($sel_subject, $sel_page, $sel_subpage, $public = false); ?>
			<br />
			<a href="new_subject.php">+ <?php echo $l_add_subject; ?></a>
		</td>
		<td id="page">
			<h2><?php echo $l_adding_page; ?></h2>
			<?php if (!empty($message)) {echo "<p class=\"message\">" . $message . "</p>";} ?>
			<?php if (!empty($errors)) { display_errors($errors); } ?>
			
			<form action="new_page.php?subj=<?php echo $sel_subject['id']; ?>" method="post">
				<?php $new_page = true; ?>
				
				
				<?php if (!isset($new_page)) {$new_page = false;} ?>

<p><?php echo $l_page_name; ?><input type="text" name="menu_name" value="" id="menu_name" /></p>

<p><?php echo $l_position; ?><select name="position">
	<?php
		if (!$new_page) {
			$page_set = get_pages_for_subject($sel_page['subject_id']);
			$page_count = mysql_num_rows($page_set);
		} else {
			$page_set = get_pages_for_subject($sel_subject['id']);
			$page_count = mysql_num_rows($page_set) + 1;
		}
		for ($count=1; $count <= $page_count; $count++) {
			echo "<option value=\"{$count}\"";
			if ($sel_page['position'] == $count) { echo " selected"; }
			echo ">{$count}</option>";
		}
	?>
</select></p>
<p><?php echo $l_visible; ?>
	<input type="radio" name="visible" value="0" /> <?php echo $l_No; ?>
	&nbsp;
	<input type="radio" name="visible" value="1" "checked" /> <?php echo $l_Yes; ?>
</p>
<p><?php echo $l_content; ?><br />
	<textarea name="content" rows="20" cols="100" class="mceEditor"></textarea>
</p>
<p><?php echo $l_description . ": "; ?><br />
	<textarea name="description" rows="5" cols="60" class="mceNoEditor"></textarea>
</p>
<p><?php echo $l_keywords . ": "; ?><br />
	<textarea name="keywords" rows="5" cols="60" class="mceNoEditor"></textarea>
</p>
				
				
				<input type="submit" name="submit" value="<?php echo $l_create_page; ?>" />
			</form>
			<br />
			<a href="edit_subject.php?subj=<?php echo $sel_subject['id']; ?>"><?php echo $l_cancel; ?></a><br />
		</td>
	</tr>
</table>
<?php include("includes/footer.php"); ?>
