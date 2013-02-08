<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php
	include_once("includes/form_functions.php");

if (empty($_GET['editid']) && empty($_GET['delid']) && $_GET['edited'] != "yes") {	
	// START FORM PROCESSING
	if (isset($_POST['submit'])) { // Form has been submitted.
		$errors = array();

		// perform validations on the form data
		$required_fields = array('username', 'password');
		$errors = check_required_fields($required_fields);
		// $errors = array_merge($errors, check_required_fields($required_fields, $_POST));

		// $fields_with_lengths = array('username' => 30, 'password' => 30);
		// $errors = array_merge($errors, check_max_field_lengths($fields_with_lengths, $_POST));

		$username = trim(mysql_prep($_POST['username']));
		$full_name = mysql_prep($_POST['full_name']);
		$password = trim(mysql_prep($_POST['password']));
		$hashed_password = sha1($password);

		if ( empty($errors) ) {
			$query = "INSERT INTO ".DB_PREFIX."users (
							username, hashed_password, full_name
						) VALUES (
							'{$username}', '{$hashed_password}', '{$full_name}'
						)";
			$result = mysql_query($query, $connection);
			if ($result) {
				$message = "The user was successfully created.";
			} else {
				$message = "The user could not be created.";
				$message .= "<br />" . mysql_error();
			}
		} else {
			if (count($errors) == 1) {
				$message = "There was 1 error in the form.";
			} else {
				$message = "There were " . count($errors) . " errors in the form.";
			}
		}
	} else { // Form has not been submitted.
		$username = "";
		$password = "";
	}
} //end of not edit and not del

elseif (!empty($_GET['delid'])) {
$query = "DELETE FROM ".DB_PREFIX."users WHERE id = {$_GET['delid']} LIMIT 1";
		$result = mysql_query ($query);
		if (mysql_affected_rows() == 1) {
			// Successfully deleted
			redirect_to("manage_users.php");
		}

} elseif ($_GET['edited'] == "yes" && isset($_POST['submit'])) {

		$id = $_GET['eid'];
		$username = trim(mysql_prep($_POST['username']));
		$full_name = mysql_prep($_POST['full_name']);
		if (!empty($_POST['password'])) {
			$password = trim(mysql_prep($_POST['password']));
			$hashed_password = sha1($password);
		}
		
		$query = "UPDATE ".DB_PREFIX."users SET 
					username = '{$username}',
					full_name = '{$full_name}' ";
			if (!empty($_POST['password'])) {		
					$query .=		",  
					hashed_password = '{$hashed_password}' ";
			} 
		$query .=		" WHERE id = {$id}";
		if ($result = mysql_query($query, $connection)) {
			redirect_to("manage_users.php");		
		}	else {
	//		redirect_to("staff.php");
		
		}				
}
?>
<?php include("includes/header.php"); ?>
<table id="structure">
	<tr>
		<td id="navigation">
			<?php include("includes/lang_select.php");	?>
		
			<a href="staff.php"><?php echo $l_return_to_menu; ?></a><br />
			<br />
		</td>
		<td id="page">
<?php
if (empty($_GET['editid'])) {
?>
			<h2><?php echo $l_existing_admin_users; ?></h2>
			<?php
			$admin_set = get_all_admins();
			$num_admins = mysql_affected_rows();
			echo "<table width=\"450px\">";
			while ($admin = mysql_fetch_array($admin_set)) {
			echo "<tr><td width=\"80px\">" . $admin['username'] . "</td>";
			echo "<td width=\"150px\">" . $admin['full_name'] . "</td>";
			echo "<td width=\"120px\"><a href=\"manage_users.php?editid=" . $admin['id'] . "\">" . $l_edit_user . "</a></td>";
			if ($num_admins!=1) {
			echo "<td width=\"100px\"><a href=\"manage_users.php?delid=" . $admin['id'] . "\" onclick=\"return confirm('" . $l_del_admin_confirm . "')\">" . $l_delete_user . "</a></td>";
			}
			echo "</tr>";
			}
			echo "</table>";
			?>
			<h2><?php echo $l_create_new_usser; ?></h2>
			<?php if (!empty($message)) {echo "<p class=\"message\">" . $message . "</p>";} ?>
			<?php if (!empty($errors)) { display_errors($errors); } ?>
			<form action="manage_users.php" method="post">
			<table>
				<tr>
					<td><?php echo $l_username; ?></td>
					<td><input type="text" name="username" maxlength="30" value="<?php echo htmlentities($username); ?>" /></td>
				</tr>
				<tr>
					<td><?php echo $l_fullname; ?></td>
					<td><input type="text" name="full_name" maxlength="50" value="<?php echo $full_name; ?>" /></td>
				</tr>
				<tr>
					<td><?php echo $l_password; ?></td>
					<td><input type="password" name="password" maxlength="30" value="<?php echo htmlentities($password); ?>" /></td>
				</tr>
				<tr>
					<td colspan="2"><br /><input type="submit" name="submit" value="<?php echo $l_creat_user; ?>" /></td>
				</tr>
			</table>
			</form>
		</td>
<?php
} else {
			$user_id = $_GET['editid'];
			$admin = get_admin_by_id($user_id);
?>

			<h2><?php echo $l_edit_usser . $admin['username']; ?></h2>
			
			<form action="manage_users.php?edited=yes&eid=<?php echo $user_id; ?>" method="post">
			<table>
				<tr>
					<td><?php echo $l_username; ?></td>
					<td><input type="text" name="username" maxlength="30" value="<?php echo htmlentities($admin['username']); ?>" /></td>
				</tr>
				<tr>
					<td><?php echo $l_fullname; ?></td>
					<td><input type="text" name="full_name" maxlength="50" value="<?php echo $admin['full_name']; ?>" /></td>
				</tr>
				<tr>
					<td><?php echo $l_password; ?></td>
					<td><input type="password" name="password" maxlength="30" value="" /></td>
				</tr>
				<tr>
					<td colspan="2"><br /><input type="submit" name="submit" value="<?php echo $l_edt_user; ?>" /></td>
				</tr>
			</table>
			</form>
<?php
}
?>
	</tr>
</table>
<?php echo $errors; ?>
<?php include("includes/footer.php"); ?>
