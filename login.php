<?php
// Get the language setting - from cookie,
// from browser URL or set default
if (isset($_GET['lang'])) {
	$lang = $_GET['lang'];
} elseif  (isset ($_COOKIE['psi-lang'])) {
	$lang = $_COOKIE['psi-lang'];
} else {
	$lang="us";
}
//Set a cookie with the language choice
setcookie("psi-lang", $lang, time()+3600*24*365);

// Get the appropriate language file
if ($lang == "us") {
	require_once("includes/languages/psi-english_us.php");
} elseif ($lang == "bg") {
	require_once("includes/languages/psi-bulgarian.php");
}
 ?>
<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php
	
	if (logged_in()) {
		redirect_to("staff.php");
	}

	include_once("includes/form_functions.php");
	
	// START FORM PROCESSING
	if (isset($_POST['submit'])) { // Form has been submitted.
		$errors = array();

		// perform validations on the form data
		$required_fields = array('username', 'password');
		$errors = array_merge($errors, check_required_fields($required_fields, $_POST));

		$fields_with_lengths = array('username' => 30, 'password' => 30);
		$errors = array_merge($errors, check_max_field_lengths($fields_with_lengths, $_POST));

		$username = trim(mysql_prep($_POST['username']));
		$password = trim(mysql_prep($_POST['password']));
		$hashed_password = sha1($password);
		
		if ( empty($errors) ) {
			// Check database to see if username and the hashed password exist there.
			$query = "SELECT id, username ";
			$query .= "FROM ".DB_PREFIX."users ";
			$query .= "WHERE username = '{$username}' ";
			$query .= "AND hashed_password = '{$hashed_password}' ";
			$query .= "LIMIT 1";
			$result_set = mysql_query($query);
			confirm_query($result_set);
			if (mysql_num_rows($result_set) == 1) {
				// username/password authenticated
				// and only 1 match
				$found_user = mysql_fetch_array($result_set);
				$_SESSION['user_id'] = $found_user['id'];
				$_SESSION['username'] = $found_user['username'];
				
				redirect_to("staff.php");
			} else {
				// username/password combo was not found in the database
				
				$message = $l_login_error_message;
			}
		} else {
			if (count($errors) == 1) {
				$message = $l_one_error_in_form;
			} else {
				$message = $l_there_were . count($errors) . $l_errors_in_form;
			}
		}
		
	} else { // Form has not been submitted.
		if (isset($_GET['logout']) && $_GET['logout'] == 1) {
			$message = $l_you_logged_out;
		} 
		$username = "";
		$password = "";
	}
?>
<?php include("includes/header.php"); ?>
<table id="structure">
	<tr>
		<td id="navigation">
			<?php include("includes/lang_select.php");	?>
			
			<a href="index.php"><?php echo $l_return_to_public; ?></a>
		</td>
		<td id="page">
			<h2><?php echo $l_staff_login; ?></h2>
			<?php if (!empty($message)) {echo "<p class=\"message\">" . $message . "</p>";} ?>
			<?php if (!empty($errors)) { display_errors($errors); } ?>
			<form action="login.php" method="post">
			<table>
				<tr>
					<td><?php echo $l_username; ?></td>
					<td><input type="text" name="username" maxlength="30" value="<?php echo htmlentities($username); ?>" /></td>
				</tr>
				<tr>
					<td><?php echo $l_password; ?></td>
					<td><input type="password" name="password" maxlength="30" value="<?php echo htmlentities($password); ?>" /></td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" name="submit" value="<?php echo $l_Login; ?>" /></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
<?php include("includes/footer.php"); ?>