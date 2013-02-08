<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php
	// make sure the subject id sent is an integer
//	if (intval($_GET['page'] ) == 0 || intval($_GET['subpage'] ) == 0) {
//		redirect_to('content.php');
//	}

	include_once("includes/form_functions.php");

	// START FORM PROCESSING
	// only execute the form processing if the form has been submitted
	if (isset($_POST['submit'])) {
		// initialize an array to hold our errors
		$errors = array();
	
		// perform validations on the form data
		$required_fields = array('menu_name', 'position', 'content');
		$errors = array_merge($errors, check_required_fields($required_fields));
		
		$fields_with_lengths = array('menu_name' => 50);
		$errors = array_merge($errors, check_max_field_lengths($fields_with_lengths));
		
		// clean up the form data before putting it in the database
		if (isset($_GET['page'])) {
			$id = mysql_prep($_GET['page']);
		} elseif (isset($_GET['subpage'])) {
			$id = mysql_prep($_GET['subpage']);
		}
		$menu_name = trim(mysql_prep($_POST['menu_name']));
		$position = mysql_prep($_POST['position']);
		$visible = mysql_prep($_POST['visible']);
		$content = mysql_prep($_POST['content']);
		$description = mysql_prep($_POST['description']);
		$keywords = mysql_prep($_POST['keywords']);
		$editor_id = $_SESSION['user_id'];
		$editor_data = get_admin_by_id ($editor_id);
		$editor = $editor_data['full_name'];
	
		// Database submission only proceeds if there were NO errors.
		if (empty($errors)) {
			if (isset($_GET['page'])) {
				$query = 	"UPDATE ".DB_PREFIX."pages SET 
							menu_name = '{$menu_name}',
							position = {$position}, 
							visible = {$visible},
							content = '{$content}',
							description = '{$description}',
							keywords = '{$keywords}',
							editor = '{$editor}'
						WHERE id = {$id}";
			} elseif (isset($_GET['subpage'])) {
			$query = 	"UPDATE ".DB_PREFIX."subpages SET 
							menu_name = '{$menu_name}',
							position = {$position}, 
							visible = {$visible},
							content = '{$content}',
							description = '{$description}',
							keywords = '{$keywords}',
							editor = '{$editor}'
						WHERE id = {$id}";	
			}
			
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
		
			<?php echo navigation($sel_subject, $sel_page, $sel_subpage); ?>
			<br />
			<a href="new_subject.php">+ Add a new subject</a>
		</td>
		<td id="page">
			<?php
			if (isset($_GET['page'])) { ?>
			<h2><?php echo $l_editpage2; ?><?php echo $sel_page['menu_name']; ?></h2>
			<?php if (!empty($message)) {echo "<p class=\"message\">" . $message . "</p>";} ?>
			<?php if (!empty($errors)) { display_errors($errors); } ?>
			
			<form action="edit_page.php?page=<?php echo $sel_page['id']; ?>" method="post">
				<?php include "page_form.php" ?>
				<input type="submit" name="submit" value="<?php echo $l_update_page; ?>" />&nbsp;&nbsp;
				<a href="delete_page.php?page=<?php echo $sel_page['id']; ?>" onclick="return confirm('<?php echo $l_del_page_confirm; ?>');"><?php echo $l_del_page; ?></a>
			</form>
			<strong><a href="edit_page.php?uploadimg=1&type=page&id=<?php echo $sel_page['id']; ?>"><?php echo $l_add_image; ?></a></strong>
			<br />
			<br />
			<a href="content.php?page=<?php echo $sel_page['id']; ?>"><?php echo $l_cancel; ?></a><br />
	
			<?php }  elseif  (isset($_GET['subpage'])) {?>
	
			<h2><?php echo $l_editsubpage2; ?><?php echo $sel_subpage['menu_name']; ?></h2>
			<?php if (!empty($message)) {echo "<p class=\"message\">" . $message . "</p>";} ?>
			<?php if (!empty($errors)) { display_errors($errors); } ?>
			
			<form action="edit_page.php?subpage=<?php echo $sel_subpage['id']; ?>" method="post">
				<?php include "subpage_form.php" ?>
				<input type="submit" name="submit" value="<?php echo $l_update_subpage; ?>" />&nbsp;&nbsp;
				<a href="delete_subpage.php?subpage=<?php echo $sel_subpage['id']; ?>" onclick="return confirm('<?php echo $l_del_subpage_confirm; ?>');"><?php echo $l_del_subpage; ?></a>
			</form>
			<strong><a href="edit_page.php?uploadimg=1&type=subpage&id=<?php echo $sel_subpage['id']; ?>"><?php echo $l_add_image; ?></a></strong>
			<br />
			<br />
			<a href="content.php?subpage=<?php echo $sel_subpage['id']; ?>"><?php echo $l_cancel; ?></a><br />
			<?php } elseif ($_GET['uploadimg'] == 1) {
				$type = $_GET['type'];
				$id = $_GET['id'];
				?>
				<h2><?php echo $l_img_upload; ?></h2>
				<p><em>Max. 20000 KB</em></p>
				<br />
				<form action="upload_image.php" method="post" enctype="multipart/form-data" name="form1">
			      <input type="file" name="file">
			      <input type="hidden" name="MAX_FILE_SIZE" value="20000000">
			      <input type="hidden" name="type" value="<?php echo $type; ?>">
			      <input type="hidden" name="id" value="<?php echo $id; ?>">
			      &nbsp;<input type="submit" name="Submit" value="<?php echo $l_upload; ?>">
			      </form>
			
			
		<?php	}
			
			
			 ?>
		</td>
	</tr>
</table>
<?php include("includes/footer.php"); ?>
