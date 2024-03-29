<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php find_selected_page(); ?>

<?php include("includes/header.php"); ?>
<table id="structure">
	<tr>
		<td id="navigation">
			<?php include("includes/lang_select.php");	?>
		
			<?php echo navigation($sel_subject, $sel_page, $sel_subpage); ?>
		</td>
		<td id="page">
			<h2><?php echo $l_add_subject2; ?></h2>
			<form action="create_subject.php" method="post">
				<p><?php echo $l_subject_name; ?>
					<input type="text" name="menu_name" value="" id="menu_name" />
				</p>
				<p><?php echo $l_position; ?>
					<select name="position">
						<?php
							$subject_set = get_all_subjects();
							$subject_count = mysql_num_rows($subject_set);
							// $subject_count + 1 b/c we are adding a subject
							for($count=1; $count <= $subject_count+1; $count++) {
								echo "<option value=\"{$count}\">{$count}</option>";
							}
						?>
					</select>
				</p>
				<p><?php echo $l_visible; ?> 
					<input type="radio" name="visible" value="0" /> <? echo $l_No; ?>
					&nbsp;
					<input type="radio" name="visible" value="1" "checked" /> <? echo $l_Yes; ?>
				</p>
				<input type="submit" value="<? echo $l_add_subject2; ?>" />
			</form>
			<br />
			<a href="content.php"><?php echo $l_cancel; ?></a>
		</td>
	</tr>
</table>
<?php require("includes/footer.php"); ?>
